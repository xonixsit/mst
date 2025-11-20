<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Client;

class QueryOptimizationService
{
    /**
     * Cache duration for query results (in minutes).
     */
    private const CACHE_DURATION = 60;

    /**
     * Get optimized client list with caching.
     */
    public function getOptimizedClientList(array $filters = [], int $perPage = 25): array
    {
        $cacheKey = 'client_list_' . md5(serialize($filters) . $perPage);
        
        return Cache::remember($cacheKey, self::CACHE_DURATION, function () use ($filters, $perPage) {
            $query = Client::query()
                ->select([
                    'id', 'first_name', 'middle_name', 'last_name', 'email', 
                    'phone', 'status', 'created_at', 'user_id'
                ])
                ->with(['user:id,first_name,last_name,email']);

            // Apply filters efficiently
            $this->applyFilters($query, $filters);

            return $query->paginate($perPage);
        });
    }

    /**
     * Get client with all related data using optimized eager loading.
     */
    public function getClientWithRelations(int $clientId): ?Client
    {
        $cacheKey = "client_full_{$clientId}";
        
        return Cache::remember($cacheKey, self::CACHE_DURATION, function () use ($clientId) {
            return Client::with([
                'user:id,first_name,last_name,email',
                'spouse',
                'employees',
                'projects' => function ($query) {
                    $query->orderBy('created_at', 'desc');
                },
                'assets' => function ($query) {
                    $query->orderBy('date_of_purchase', 'desc');
                },
                'expenses' => function ($query) {
                    $query->orderBy('created_at', 'desc');
                },
                'invoices' => function ($query) {
                    $query->select(['id', 'client_id', 'total', 'status', 'created_at'])
                          ->orderBy('created_at', 'desc')
                          ->limit(10);
                },
                'documents' => function ($query) {
                    $query->select(['id', 'client_id', 'name', 'type', 'size', 'created_at'])
                          ->orderBy('created_at', 'desc')
                          ->limit(20);
                }
            ])->find($clientId);
        });
    }

    /**
     * Apply filters to client query efficiently.
     */
    private function applyFilters(Builder $query, array $filters): void
    {
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhereRaw("CONCAT(first_name, ' ', last_name) like ?", ["%{$search}%"]);
            });
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        if (!empty($filters['date_from'])) {
            $query->where('created_at', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->where('created_at', '<=', $filters['date_to']);
        }

        if (!empty($filters['archived'])) {
            if ($filters['archived'] === 'yes') {
                $query->whereNotNull('archived_at');
            } else {
                $query->whereNull('archived_at');
            }
        }
    }

    /**
     * Get dashboard statistics with caching.
     */
    public function getDashboardStats(): array
    {
        return Cache::remember('dashboard_stats', 30, function () {
            return [
                'total_clients' => Client::count(),
                'active_clients' => Client::where('status', 'active')->count(),
                'new_clients_this_month' => Client::where('created_at', '>=', now()->startOfMonth())->count(),
                'total_invoices' => DB::table('invoices')->count(),
                'paid_invoices' => DB::table('invoices')->where('status', 'paid')->count(),
                'pending_invoices' => DB::table('invoices')->where('status', 'pending')->count(),
                'total_revenue' => DB::table('invoices')->where('status', 'paid')->sum('total'),
                'pending_revenue' => DB::table('invoices')->where('status', 'pending')->sum('total'),
            ];
        });
    }

    /**
     * Get client search suggestions for autocomplete.
     */
    public function getClientSearchSuggestions(string $query, int $limit = 10): array
    {
        $cacheKey = "client_suggestions_" . md5($query . $limit);
        
        return Cache::remember($cacheKey, 15, function () use ($query, $limit) {
            return Client::with('user:id,first_name,last_name,email')
                ->whereHas('user', function ($q) use ($query) {
                    $q->where('role', 'client') // Only get actual client users
                      ->where(function ($subQ) use ($query) {
                          $subQ->where('first_name', 'like', "%{$query}%")
                               ->orWhere('last_name', 'like', "%{$query}%")
                               ->orWhere('email', 'like', "%{$query}%")
                               ->orWhereRaw("CONCAT(first_name, ' ', last_name) like ?", ["%{$query}%"]);
                      });
                })
                ->limit($limit)
                ->get()
                ->groupBy('user_id') // Group by user_id to handle duplicates
                ->map(function ($clientGroup) {
                    // Take the first (or most recent) client for each user
                    $client = $clientGroup->sortByDesc('created_at')->first();
                    return [
                        'id' => $client->id,
                        'name' => trim(($client->user->first_name ?? '') . ' ' . ($client->user->last_name ?? '')),
                        'email' => $client->user->email ?? '',
                    ];
                });
        });
    }

    /**
     * Bulk update client status with optimized query.
     */
    public function bulkUpdateClientStatus(array $clientIds, string $status): int
    {
        // Clear related cache
        $this->clearClientCache($clientIds);
        
        return Client::whereIn('id', $clientIds)->update([
            'status' => $status,
            'updated_at' => now()
        ]);
    }

    /**
     * Get clients with pending reviews efficiently.
     */
    public function getClientsWithPendingReviews(): array
    {
        return Cache::remember('clients_pending_reviews', 30, function () {
            return Client::with('user:id,first_name,last_name,email')
                ->whereHas('user', function ($query) {
                    $query->where('role', 'client');
                })
                ->whereNotNull('review_requested_at')
                ->orderBy('review_requested_at', 'asc')
                ->limit(50)
                ->get()
                ->groupBy('user_id') // Group by user_id to handle duplicates
                ->map(function ($clientGroup) {
                    // Take the first (or most recent) client for each user
                    $client = $clientGroup->sortByDesc('created_at')->first();
                    return [
                        'id' => $client->id,
                        'first_name' => $client->user->first_name ?? '',
                        'last_name' => $client->user->last_name ?? '',
                        'email' => $client->user->email ?? '',
                        'review_requested_at' => $client->review_requested_at,
                        'name' => trim(($client->user->first_name ?? '') . ' ' . ($client->user->last_name ?? ''))
                    ];
                })
                ->values()
                ->toArray();
        });
    }

    /**
     * Get invoice statistics by client.
     */
    public function getInvoiceStatsByClient(int $clientId): array
    {
        $cacheKey = "invoice_stats_client_{$clientId}";
        
        return Cache::remember($cacheKey, self::CACHE_DURATION, function () use ($clientId) {
            return DB::table('invoices')
                ->where('client_id', $clientId)
                ->selectRaw('
                    COUNT(*) as total_invoices,
                    SUM(CASE WHEN status = "paid" THEN total ELSE 0 END) as paid_amount,
                    SUM(CASE WHEN status = "pending" THEN total ELSE 0 END) as pending_amount,
                    SUM(total) as total_amount,
                    AVG(total) as average_invoice
                ')
                ->first();
        });
    }

    /**
     * Clear cache for specific clients.
     */
    public function clearClientCache(array $clientIds = []): void
    {
        if (empty($clientIds)) {
            // Clear all client-related cache
            Cache::flush();
            return;
        }

        foreach ($clientIds as $clientId) {
            Cache::forget("client_full_{$clientId}");
            Cache::forget("invoice_stats_client_{$clientId}");
        }

        // Clear list caches
        Cache::forget('dashboard_stats');
        Cache::forget('clients_pending_reviews');
        
        // Clear search suggestion caches (pattern-based)
        $this->clearCacheByPattern('client_suggestions_*');
        $this->clearCacheByPattern('client_list_*');
    }

    /**
     * Clear cache by pattern (simplified implementation).
     */
    private function clearCacheByPattern(string $pattern): void
    {
        // Note: This is a simplified implementation
        // In production, you might want to use Redis with SCAN command
        // or maintain a list of cache keys to clear
        Cache::flush();
    }

    /**
     * Analyze query performance and suggest optimizations.
     */
    public function analyzeQueryPerformance(): array
    {
        $slowQueries = DB::select("
            SELECT 
                query_time,
                lock_time,
                rows_sent,
                rows_examined,
                sql_text
            FROM mysql.slow_log 
            WHERE start_time >= DATE_SUB(NOW(), INTERVAL 1 HOUR)
            ORDER BY query_time DESC 
            LIMIT 10
        ");

        $tableStats = DB::select("
            SELECT 
                table_name,
                table_rows,
                data_length,
                index_length,
                (data_length + index_length) as total_size
            FROM information_schema.tables 
            WHERE table_schema = DATABASE()
            ORDER BY total_size DESC
        ");

        return [
            'slow_queries' => $slowQueries,
            'table_stats' => $tableStats,
            'recommendations' => $this->generateOptimizationRecommendations($tableStats)
        ];
    }

    /**
     * Generate optimization recommendations based on table statistics.
     */
    private function generateOptimizationRecommendations(array $tableStats): array
    {
        $recommendations = [];

        foreach ($tableStats as $table) {
            if ($table->table_rows > 100000) {
                $recommendations[] = "Consider partitioning table {$table->table_name} (has {$table->table_rows} rows)";
            }

            if ($table->data_length > $table->index_length * 10) {
                $recommendations[] = "Table {$table->table_name} might benefit from additional indexes";
            }
        }

        return $recommendations;
    }
}