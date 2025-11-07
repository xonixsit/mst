<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class DatabasePerformanceService
{
    /**
     * Monitor database performance metrics.
     */
    public function getPerformanceMetrics(): array
    {
        return Cache::remember('db_performance_metrics', 5, function () {
            return [
                'connection_stats' => $this->getConnectionStats(),
                'query_stats' => $this->getQueryStats(),
                'table_stats' => $this->getTableStats(),
                'index_usage' => $this->getIndexUsage(),
                'slow_queries' => $this->getSlowQueries(),
            ];
        });
    }

    /**
     * Get database connection statistics.
     */
    private function getConnectionStats(): array
    {
        try {
            $stats = DB::select("SHOW STATUS LIKE 'Connections'");
            $maxConnections = DB::select("SHOW VARIABLES LIKE 'max_connections'");
            $threadsConnected = DB::select("SHOW STATUS LIKE 'Threads_connected'");

            return [
                'total_connections' => $stats[0]->Value ?? 0,
                'max_connections' => $maxConnections[0]->Value ?? 0,
                'current_connections' => $threadsConnected[0]->Value ?? 0,
            ];
        } catch (\Exception $e) {
            Log::error('Failed to get connection stats', ['error' => $e->getMessage()]);
            return [];
        }
    }

    /**
     * Get query performance statistics.
     */
    private function getQueryStats(): array
    {
        try {
            $queries = DB::select("SHOW STATUS LIKE 'Queries'");
            $slowQueries = DB::select("SHOW STATUS LIKE 'Slow_queries'");
            $uptime = DB::select("SHOW STATUS LIKE 'Uptime'");

            $totalQueries = $queries[0]->Value ?? 0;
            $totalSlowQueries = $slowQueries[0]->Value ?? 0;
            $uptimeSeconds = $uptime[0]->Value ?? 1;

            return [
                'total_queries' => $totalQueries,
                'slow_queries' => $totalSlowQueries,
                'queries_per_second' => round($totalQueries / $uptimeSeconds, 2),
                'slow_query_percentage' => $totalQueries > 0 ? round(($totalSlowQueries / $totalQueries) * 100, 2) : 0,
            ];
        } catch (\Exception $e) {
            Log::error('Failed to get query stats', ['error' => $e->getMessage()]);
            return [];
        }
    }

    /**
     * Get table statistics for optimization insights.
     */
    private function getTableStats(): array
    {
        try {
            return DB::select("
                SELECT 
                    table_name,
                    table_rows,
                    ROUND(((data_length + index_length) / 1024 / 1024), 2) AS size_mb,
                    ROUND((data_length / 1024 / 1024), 2) AS data_mb,
                    ROUND((index_length / 1024 / 1024), 2) AS index_mb,
                    ROUND((index_length / data_length), 2) AS index_ratio
                FROM information_schema.tables 
                WHERE table_schema = DATABASE()
                AND table_type = 'BASE TABLE'
                ORDER BY (data_length + index_length) DESC
                LIMIT 20
            ");
        } catch (\Exception $e) {
            Log::error('Failed to get table stats', ['error' => $e->getMessage()]);
            return [];
        }
    }

    /**
     * Get index usage statistics.
     */
    private function getIndexUsage(): array
    {
        try {
            return DB::select("
                SELECT 
                    t.table_name,
                    s.index_name,
                    s.column_name,
                    s.cardinality,
                    CASE 
                        WHEN s.cardinality = 0 THEN 'Unused'
                        WHEN s.cardinality < 10 THEN 'Low Usage'
                        ELSE 'Good Usage'
                    END as usage_status
                FROM information_schema.statistics s
                JOIN information_schema.tables t ON s.table_name = t.table_name
                WHERE s.table_schema = DATABASE()
                AND t.table_type = 'BASE TABLE'
                ORDER BY s.table_name, s.index_name
            ");
        } catch (\Exception $e) {
            Log::error('Failed to get index usage', ['error' => $e->getMessage()]);
            return [];
        }
    }

    /**
     * Get recent slow queries.
     */
    private function getSlowQueries(): array
    {
        try {
            // Note: This requires slow query log to be enabled
            return DB::select("
                SELECT 
                    start_time,
                    query_time,
                    lock_time,
                    rows_sent,
                    rows_examined,
                    LEFT(sql_text, 200) as sql_preview
                FROM mysql.slow_log 
                WHERE start_time >= DATE_SUB(NOW(), INTERVAL 1 HOUR)
                ORDER BY query_time DESC 
                LIMIT 10
            ");
        } catch (\Exception $e) {
            // Slow query log might not be enabled or accessible
            return [];
        }
    }

    /**
     * Analyze and suggest database optimizations.
     */
    public function analyzeAndSuggestOptimizations(): array
    {
        $metrics = $this->getPerformanceMetrics();
        $suggestions = [];

        // Analyze connection usage
        if (isset($metrics['connection_stats'])) {
            $connStats = $metrics['connection_stats'];
            $connectionUsage = ($connStats['current_connections'] / $connStats['max_connections']) * 100;
            
            if ($connectionUsage > 80) {
                $suggestions[] = [
                    'type' => 'warning',
                    'category' => 'connections',
                    'message' => 'High connection usage detected. Consider connection pooling or increasing max_connections.',
                    'current_value' => $connectionUsage . '%'
                ];
            }
        }

        // Analyze slow queries
        if (isset($metrics['query_stats'])) {
            $queryStats = $metrics['query_stats'];
            if ($queryStats['slow_query_percentage'] > 5) {
                $suggestions[] = [
                    'type' => 'error',
                    'category' => 'performance',
                    'message' => 'High percentage of slow queries detected. Review and optimize queries.',
                    'current_value' => $queryStats['slow_query_percentage'] . '%'
                ];
            }
        }

        // Analyze table sizes
        if (isset($metrics['table_stats'])) {
            foreach ($metrics['table_stats'] as $table) {
                if ($table->size_mb > 1000) {
                    $suggestions[] = [
                        'type' => 'info',
                        'category' => 'storage',
                        'message' => "Table {$table->table_name} is large ({$table->size_mb}MB). Consider archiving old data.",
                        'table' => $table->table_name
                    ];
                }

                if ($table->index_ratio > 2) {
                    $suggestions[] = [
                        'type' => 'warning',
                        'category' => 'indexes',
                        'message' => "Table {$table->table_name} has high index overhead. Review index necessity.",
                        'table' => $table->table_name
                    ];
                }
            }
        }

        return [
            'metrics' => $metrics,
            'suggestions' => $suggestions,
            'health_score' => $this->calculateHealthScore($metrics, $suggestions)
        ];
    }

    /**
     * Calculate overall database health score.
     */
    private function calculateHealthScore(array $metrics, array $suggestions): int
    {
        $score = 100;

        // Deduct points for issues
        foreach ($suggestions as $suggestion) {
            switch ($suggestion['type']) {
                case 'error':
                    $score -= 20;
                    break;
                case 'warning':
                    $score -= 10;
                    break;
                case 'info':
                    $score -= 5;
                    break;
            }
        }

        return max(0, min(100, $score));
    }

    /**
     * Log performance metrics for historical analysis.
     */
    public function logPerformanceMetrics(): void
    {
        $metrics = $this->getPerformanceMetrics();
        
        Log::channel('performance')->info('Database Performance Metrics', [
            'timestamp' => now()->toISOString(),
            'metrics' => $metrics
        ]);
    }

    /**
     * Get performance trends over time.
     */
    public function getPerformanceTrends(int $days = 7): array
    {
        // This would typically read from a performance metrics table
        // For now, return sample data structure
        return [
            'query_performance' => [
                'dates' => collect(range(0, $days - 1))->map(fn($i) => now()->subDays($i)->format('Y-m-d')),
                'queries_per_second' => collect(range(0, $days - 1))->map(fn($i) => rand(50, 200)),
                'slow_query_percentage' => collect(range(0, $days - 1))->map(fn($i) => rand(1, 10)),
            ],
            'connection_usage' => [
                'dates' => collect(range(0, $days - 1))->map(fn($i) => now()->subDays($i)->format('Y-m-d')),
                'peak_connections' => collect(range(0, $days - 1))->map(fn($i) => rand(20, 80)),
                'average_connections' => collect(range(0, $days - 1))->map(fn($i) => rand(10, 40)),
            ]
        ];
    }

    /**
     * Optimize specific table by analyzing its structure.
     */
    public function optimizeTable(string $tableName): array
    {
        try {
            // Analyze table
            $analysis = DB::select("ANALYZE TABLE {$tableName}");
            
            // Optimize table
            $optimization = DB::select("OPTIMIZE TABLE {$tableName}");
            
            // Get updated stats
            $stats = DB::select("
                SELECT 
                    table_rows,
                    data_length,
                    index_length,
                    data_free
                FROM information_schema.tables 
                WHERE table_schema = DATABASE() 
                AND table_name = ?
            ", [$tableName]);

            return [
                'success' => true,
                'analysis' => $analysis,
                'optimization' => $optimization,
                'stats' => $stats[0] ?? null
            ];
        } catch (\Exception $e) {
            Log::error("Failed to optimize table {$tableName}", ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
}