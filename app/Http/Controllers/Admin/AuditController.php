<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\User;
use App\Services\AuditService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AuditController extends Controller
{
    public function __construct(
        private AuditService $auditService
    ) {}

    /**
     * Display audit logs with filtering and pagination.
     */
    public function index(Request $request): Response
    {
        $filters = $request->validate([
            'search' => 'nullable|string|max:255',
            'event' => 'nullable|string|in:created,updated,deleted,status_changed,assigned,bulk_email,bulk_status_update',
            'model_type' => 'nullable|string',
            'user_id' => 'nullable|integer|exists:users,id',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'per_page' => 'nullable|integer|min:10|max:100',
        ]);

        $query = AuditLog::with(['user'])
            ->orderBy('created_at', 'desc');

        // Apply filters
        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('auditable_type', 'like', "%{$filters['search']}%")
                  ->orWhere('event', 'like', "%{$filters['search']}%")
                  ->orWhereHas('user', function ($userQuery) use ($filters) {
                      $userQuery->where('first_name', 'like', "%{$filters['search']}%")
                               ->orWhere('last_name', 'like', "%{$filters['search']}%")
                               ->orWhere('email', 'like', "%{$filters['search']}%")
                               ->orWhereRaw("CONCAT(first_name, ' ', last_name) like ?", ["%{$filters['search']}%"]);
                  });
            });
        }

        if (!empty($filters['event'])) {
            $query->where('event', $filters['event']);
        }

        if (!empty($filters['model_type'])) {
            $query->where('auditable_type', $filters['model_type']);
        }

        if (!empty($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        if (!empty($filters['start_date'])) {
            $query->whereDate('created_at', '>=', $filters['start_date']);
        }

        if (!empty($filters['end_date'])) {
            $query->whereDate('created_at', '<=', $filters['end_date']);
        }

        $perPage = $filters['per_page'] ?? 25;
        $auditLogs = $query->paginate($perPage)->withQueryString();

        // Get filter options
        $users = User::select('id', 'first_name', 'last_name', 'email')
            ->orderBy('first_name')
            ->get()
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => trim($user->first_name . ' ' . $user->last_name),
                    'email' => $user->email
                ];
            });
        $modelTypes = AuditLog::select('auditable_type')
            ->distinct()
            ->orderBy('auditable_type')
            ->pluck('auditable_type')
            ->map(function ($type) {
                return [
                    'value' => $type,
                    'label' => class_basename($type)
                ];
            });

        $eventTypes = [
            ['value' => 'created', 'label' => 'Created'],
            ['value' => 'updated', 'label' => 'Updated'],
            ['value' => 'deleted', 'label' => 'Deleted'],
            ['value' => 'status_changed', 'label' => 'Status Changed'],
            ['value' => 'assigned', 'label' => 'Assigned'],
            ['value' => 'bulk_email', 'label' => 'Bulk Email'],
            ['value' => 'bulk_status_update', 'label' => 'Bulk Status Update'],
        ];

        return Inertia::render('Admin/Audit/Index', [
            'auditLogs' => $auditLogs,
            'filters' => $filters,
            'users' => $users,
            'modelTypes' => $modelTypes,
            'eventTypes' => $eventTypes,
        ]);
    }

    /**
     * Show detailed audit log entry.
     */
    public function show(AuditLog $auditLog): Response
    {
        $auditLog->load(['user']);

        return Inertia::render('Admin/Audit/Show', [
            'auditLog' => $auditLog,
        ]);
    }

    /**
     * Export audit logs to CSV.
     */
    public function export(Request $request): StreamedResponse
    {
        $filters = $request->validate([
            'event' => 'nullable|string',
            'model_type' => 'nullable|string',
            'user_id' => 'nullable|integer|exists:users,id',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $query = AuditLog::with(['user'])
            ->orderBy('created_at', 'desc');

        // Apply same filters as index
        if (!empty($filters['event'])) {
            $query->where('event', $filters['event']);
        }

        if (!empty($filters['model_type'])) {
            $query->where('auditable_type', $filters['model_type']);
        }

        if (!empty($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        if (!empty($filters['start_date'])) {
            $query->whereDate('created_at', '>=', $filters['start_date']);
        }

        if (!empty($filters['end_date'])) {
            $query->whereDate('created_at', '<=', $filters['end_date']);
        }

        $filename = 'audit_logs_' . now()->format('Y-m-d_H-i-s') . '.csv';

        return response()->streamDownload(function () use ($query) {
            $handle = fopen('php://output', 'w');

            // CSV headers
            fputcsv($handle, [
                'ID',
                'Date/Time',
                'User',
                'Event',
                'Model Type',
                'Model ID',
                'Description',
                'IP Address',
                'Changes',
            ]);

            // Stream data in chunks to handle large datasets
            $query->chunk(1000, function ($auditLogs) use ($handle) {
                foreach ($auditLogs as $log) {
                    $changes = '';
                    if ($log->old_values && $log->new_values) {
                        $changesList = [];
                        foreach ($log->changes as $field => $change) {
                            $changesList[] = "{$field}: '{$change['old']}' → '{$change['new']}'";
                        }
                        $changes = implode('; ', $changesList);
                    }

                    fputcsv($handle, [
                        $log->id,
                        $log->created_at->format('Y-m-d H:i:s'),
                        $log->user ? $log->user->name : 'System',
                        $log->event,
                        class_basename($log->auditable_type),
                        $log->auditable_id,
                        $log->description,
                        $log->ip_address,
                        $changes,
                    ]);
                }
            });

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    /**
     * Generate compliance report.
     */
    public function complianceReport(Request $request): Response
    {
        $filters = $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'model_type' => 'nullable|string',
        ]);

        $startDate = $filters['start_date'];
        $endDate = $filters['end_date'];

        $query = AuditLog::whereBetween('created_at', [$startDate, $endDate]);

        if (!empty($filters['model_type'])) {
            $query->where('auditable_type', $filters['model_type']);
        }

        // Generate compliance statistics
        $totalEvents = $query->count();
        
        $eventBreakdown = $query->select('event')
            ->selectRaw('COUNT(*) as count')
            ->groupBy('event')
            ->orderBy('count', 'desc')
            ->get();

        $userActivity = $query->with('user')
            ->select('user_id')
            ->selectRaw('COUNT(*) as count')
            ->groupBy('user_id')
            ->orderBy('count', 'desc')
            ->get()
            ->map(function ($item) {
                return [
                    'user' => $item->user ? $item->user->name : 'System',
                    'count' => $item->count,
                ];
            });

        $modelActivity = $query->select('auditable_type')
            ->selectRaw('COUNT(*) as count')
            ->groupBy('auditable_type')
            ->orderBy('count', 'desc')
            ->get()
            ->map(function ($item) {
                return [
                    'model' => class_basename($item->auditable_type),
                    'count' => $item->count,
                ];
            });

        $dailyActivity = $query->selectRaw('DATE(created_at) as date')
            ->selectRaw('COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return Inertia::render('Admin/Audit/ComplianceReport', [
            'filters' => $filters,
            'statistics' => [
                'total_events' => $totalEvents,
                'date_range' => [
                    'start' => $startDate,
                    'end' => $endDate,
                ],
                'event_breakdown' => $eventBreakdown,
                'user_activity' => $userActivity,
                'model_activity' => $modelActivity,
                'daily_activity' => $dailyActivity,
            ],
        ]);
    }

    /**
     * Clean up old audit logs.
     */
    public function cleanup(Request $request)
    {
        $request->validate([
            'days_to_keep' => 'required|integer|min:30|max:2555', // 30 days to 7 years
        ]);

        $deletedCount = $this->auditService->cleanup($request->days_to_keep);

        return back()->with('success', "Cleaned up {$deletedCount} old audit log entries.");
    }
}