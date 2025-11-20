<?php

namespace App\Repositories;

use App\Models\Client;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class ClientRepository
{
    /**
     * Get all clients with optional filtering, sorting, and pagination.
     *
     * @param array $filters
     * @param array $sorting
     * @param int|null $perPage
     * @return LengthAwarePaginator|Collection
     */
    public function getClients(array $filters = [], array $sorting = [], ?int $perPage = null)
    {
        $query = $this->buildClientQuery($filters, $sorting);

        if ($perPage) {
            return $query->paginate($perPage);
        }

        return $query->get();
    }

    /**
     * Find a client by ID with relationships.
     *
     * @param int $id
     * @param array $relationships
     * @return Client|null
     */
    public function findById(int $id, array $relationships = []): ?Client
    {
        $query = Client::query();

        if (!empty($relationships)) {
            $query->with($relationships);
        }

        return $query->find($id);
    }

    /**
     * Find a client by email.
     *
     * @param string $email
     * @return Client|null
     */
    public function findByEmail(string $email): ?Client
    {
        return Client::whereHas('user', function ($query) use ($email) {
            $query->where('email', $email);
        })->first();
    }

    /**
     * Find a client by SSN.
     *
     * @param string $ssn
     * @return Client|null
     */
    public function findBySSN(string $ssn): ?Client
    {
        return Client::where('ssn', $ssn)->first();
    }

    /**
     * Search clients by multiple criteria.
     *
     * @param string $searchTerm
     * @param array $searchFields
     * @return Builder
     */
    public function search(string $searchTerm, array $searchFields = []): Builder
    {
        $defaultFields = ['first_name', 'last_name', 'email', 'phone', 'mobile_number'];
        $fields = !empty($searchFields) ? $searchFields : $defaultFields;

        return Client::where(function (Builder $query) use ($searchTerm, $fields) {
            foreach ($fields as $field) {
                $query->orWhere($field, 'LIKE', "%{$searchTerm}%");
            }

            // Search full name
            $query->orWhereRaw("CONCAT(first_name, ' ', COALESCE(middle_name, ''), ' ', last_name) LIKE ?", ["%{$searchTerm}%"]);
            
            // Search by client ID if search term is numeric
            if (is_numeric($searchTerm)) {
                $query->orWhere('id', $searchTerm);
            }
        });
    }

    /**
     * Get clients by status.
     *
     * @param string $status
     * @return Collection
     */
    public function getByStatus(string $status): Collection
    {
        return Client::where('status', $status)->get();
    }

    /**
     * Get clients by marital status.
     *
     * @param string $maritalStatus
     * @return Collection
     */
    public function getByMaritalStatus(string $maritalStatus): Collection
    {
        return Client::where('marital_status', $maritalStatus)->get();
    }

    /**
     * Get clients assigned to a specific user.
     *
     * @param int $userId
     * @return Collection
     */
    public function getByAssignedUser(int $userId): Collection
    {
        return Client::where('user_id', $userId)->get();
    }

    /**
     * Get clients with spouse information.
     *
     * @return Collection
     */
    public function getClientsWithSpouse(): Collection
    {
        return Client::with('spouse')
            ->whereHas('spouse')
            ->get();
    }

    /**
     * Get clients with active projects.
     *
     * @return Collection
     */
    public function getClientsWithActiveProjects(): Collection
    {
        return Client::with('projects')
            ->whereHas('projects', function (Builder $query) {
                $query->whereIn('status', ['pending', 'in_progress']);
            })
            ->get();
    }

    /**
     * Get clients with overdue projects.
     *
     * @return Collection
     */
    public function getClientsWithOverdueProjects(): Collection
    {
        return Client::with('projects')
            ->whereHas('projects', function (Builder $query) {
                $query->where('due_date', '<', now())
                      ->whereNotIn('status', ['completed', 'cancelled']);
            })
            ->get();
    }

    /**
     * Get clients registered within a date range.
     *
     * @param string $startDate
     * @param string $endDate
     * @return Collection
     */
    public function getByRegistrationDateRange(string $startDate, string $endDate): Collection
    {
        return Client::whereBetween('created_at', [$startDate, $endDate])->get();
    }

    /**
     * Get clients by visa status.
     *
     * @param string $visaStatus
     * @return Collection
     */
    public function getByVisaStatus(string $visaStatus): Collection
    {
        return Client::where('visa_status', $visaStatus)->get();
    }

    /**
     * Get clients with assets above a certain value.
     *
     * @param float $minValue
     * @return Collection
     */
    public function getClientsWithAssetsAboveValue(float $minValue): Collection
    {
        return Client::with('assets')
            ->whereHas('assets', function (Builder $query) use ($minValue) {
                $query->where('cost_of_acquisition', '>', $minValue);
            })
            ->get();
    }

    /**
     * Get clients with expenses in a specific category.
     *
     * @param string $category
     * @return Collection
     */
    public function getClientsWithExpenseCategory(string $category): Collection
    {
        return Client::with('expenses')
            ->whereHas('expenses', function (Builder $query) use ($category) {
                $query->where('category', $category);
            })
            ->get();
    }

    /**
     * Get client statistics.
     *
     * @return array
     */
    public function getClientStatistics(): array
    {
        return [
            'total_clients' => Client::count(),
            'active_clients' => Client::where('status', 'active')->count(),
            'inactive_clients' => Client::where('status', 'inactive')->count(),
            'archived_clients' => Client::where('status', 'archived')->count(),
            'married_clients' => Client::where('marital_status', 'married')->count(),
            'clients_with_spouse' => Client::whereHas('spouse')->count(),
            'clients_with_projects' => Client::whereHas('projects')->count(),
            'clients_with_assets' => Client::whereHas('assets')->count(),
            'clients_with_expenses' => Client::whereHas('expenses')->count(),
        ];
    }

    /**
     * Get clients for bulk operations.
     *
     * @param array $clientIds
     * @return Collection
     */
    public function getForBulkOperations(array $clientIds): Collection
    {
        return Client::whereIn('id', $clientIds)->get();
    }

    /**
     * Update multiple clients' status.
     *
     * @param array $clientIds
     * @param string $status
     * @return int
     */
    public function bulkUpdateStatus(array $clientIds, string $status): int
    {
        return Client::whereIn('id', $clientIds)->update(['status' => $status]);
    }

    /**
     * Update multiple clients' assigned user.
     *
     * @param array $clientIds
     * @param int $userId
     * @return int
     */
    public function bulkUpdateAssignedUser(array $clientIds, int $userId): int
    {
        return Client::whereIn('id', $clientIds)->update(['user_id' => $userId]);
    }  
  /**
     * Build the base client query with filters and sorting.
     *
     * @param array $filters
     * @param array $sorting
     * @return Builder
     */
    private function buildClientQuery(array $filters = [], array $sorting = []): Builder
    {
        $query = Client::with(['spouse', 'user'])
            ->withCount(['projects', 'assets', 'expenses']);

        // Apply filters
        $this->applyFilters($query, $filters);

        // Apply sorting
        $this->applySorting($query, $sorting);

        return $query;
    }

    /**
     * Apply filters to the query.
     *
     * @param Builder $query
     * @param array $filters
     * @return void
     */
    private function applyFilters(Builder $query, array $filters): void
    {
        // Search filter
        if (!empty($filters['search'])) {
            $searchTerm = $filters['search'];
            $query->where(function (Builder $q) use ($searchTerm) {
                // Search in client table fields
                $q->where('phone', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('mobile_number', 'LIKE', "%{$searchTerm}%")
                  // Search in user table fields
                  ->orWhereHas('user', function (Builder $userQuery) use ($searchTerm) {
                      $userQuery->where('first_name', 'LIKE', "%{$searchTerm}%")
                               ->orWhere('last_name', 'LIKE', "%{$searchTerm}%")
                               ->orWhere('email', 'LIKE', "%{$searchTerm}%")
                               ->orWhereRaw("CONCAT(first_name, ' ', COALESCE(middle_name, ''), ' ', last_name) LIKE ?", ["%{$searchTerm}%"]);
                  });
                
                if (is_numeric($searchTerm)) {
                    $q->orWhere('id', $searchTerm);
                }
            });
        }

        // Status filter
        if (!empty($filters['status'])) {
            if (is_array($filters['status'])) {
                $query->whereIn('status', $filters['status']);
            } else {
                $query->where('status', $filters['status']);
            }
        }

        // Marital status filter
        if (!empty($filters['marital_status'])) {
            if (is_array($filters['marital_status'])) {
                $query->whereIn('marital_status', $filters['marital_status']);
            } else {
                $query->where('marital_status', $filters['marital_status']);
            }
        }

        // Visa status filter
        if (!empty($filters['visa_status'])) {
            if (is_array($filters['visa_status'])) {
                $query->whereIn('visa_status', $filters['visa_status']);
            } else {
                $query->where('visa_status', $filters['visa_status']);
            }
        }

        // Assigned user filter
        if (!empty($filters['user_id'])) {
            if (is_array($filters['user_id'])) {
                $query->whereIn('user_id', $filters['user_id']);
            } else {
                $query->where('user_id', $filters['user_id']);
            }
        }

        // Registration date range filter
        if (!empty($filters['registration_date_from'])) {
            $query->where('created_at', '>=', $filters['registration_date_from']);
        }

        if (!empty($filters['registration_date_to'])) {
            $query->where('created_at', '<=', $filters['registration_date_to']);
        }

        // Date of birth range filter
        if (!empty($filters['date_of_birth_from'])) {
            $query->where('date_of_birth', '>=', $filters['date_of_birth_from']);
        }

        if (!empty($filters['date_of_birth_to'])) {
            $query->where('date_of_birth', '<=', $filters['date_of_birth_to']);
        }

        // Insurance coverage filter
        if (isset($filters['insurance_covered'])) {
            $query->where('insurance_covered', (bool) $filters['insurance_covered']);
        }

        // Has spouse filter
        if (isset($filters['has_spouse'])) {
            if ($filters['has_spouse']) {
                $query->whereHas('spouse');
            } else {
                $query->whereDoesntHave('spouse');
            }
        }

        // Has active projects filter
        if (isset($filters['has_active_projects'])) {
            if ($filters['has_active_projects']) {
                $query->whereHas('projects', function (Builder $q) {
                    $q->whereIn('status', ['pending', 'in_progress']);
                });
            } else {
                $query->whereDoesntHave('projects', function (Builder $q) {
                    $q->whereIn('status', ['pending', 'in_progress']);
                });
            }
        }

        // Has overdue projects filter
        if (isset($filters['has_overdue_projects'])) {
            if ($filters['has_overdue_projects']) {
                $query->whereHas('projects', function (Builder $q) {
                    $q->where('due_date', '<', now())
                      ->whereNotIn('status', ['completed', 'cancelled']);
                });
            }
        }

        // City filter
        if (!empty($filters['city'])) {
            if (is_array($filters['city'])) {
                $query->whereIn('city', $filters['city']);
            } else {
                $query->where('city', 'LIKE', "%{$filters['city']}%");
            }
        }

        // State filter
        if (!empty($filters['state'])) {
            if (is_array($filters['state'])) {
                $query->whereIn('state', $filters['state']);
            } else {
                $query->where('state', $filters['state']);
            }
        }

        // Country filter
        if (!empty($filters['country'])) {
            if (is_array($filters['country'])) {
                $query->whereIn('country', $filters['country']);
            } else {
                $query->where('country', $filters['country']);
            }
        }

        // Occupation filter
        if (!empty($filters['occupation'])) {
            $query->where('occupation', 'LIKE', "%{$filters['occupation']}%");
        }
    }

    /**
     * Apply sorting to the query.
     *
     * @param Builder $query
     * @param array $sorting
     * @return void
     */
    private function applySorting(Builder $query, array $sorting): void
    {
        $sortBy = $sorting['sort_by'] ?? 'created_at';
        $sortDirection = $sorting['sort_direction'] ?? 'desc';

        // Validate sort direction
        $sortDirection = in_array(strtolower($sortDirection), ['asc', 'desc']) ? $sortDirection : 'desc';

        // Define allowed sort fields
        $allowedSortFields = [
            'id', 'first_name', 'last_name', 'email', 'phone', 'mobile_number',
            'date_of_birth', 'marital_status', 'occupation', 'status', 'city',
            'state', 'country', 'visa_status', 'created_at', 'updated_at'
        ];

        if (in_array($sortBy, $allowedSortFields)) {
            $query->orderBy($sortBy, $sortDirection);
        } else {
            // Handle special sorting cases
            switch ($sortBy) {
                case 'full_name':
                    $query->orderByRaw("CONCAT(first_name, ' ', COALESCE(middle_name, ''), ' ', last_name) {$sortDirection}");
                    break;
                case 'projects_count':
                    $query->orderBy('projects_count', $sortDirection);
                    break;
                case 'assets_count':
                    $query->orderBy('assets_count', $sortDirection);
                    break;
                case 'expenses_count':
                    $query->orderBy('expenses_count', $sortDirection);
                    break;
                default:
                    $query->orderBy('created_at', 'desc');
            }
        }

        // Add secondary sort by ID for consistent pagination
        if ($sortBy !== 'id') {
            $query->orderBy('id', 'asc');
        }
    }

    /**
     * Get advanced search results with complex filtering.
     *
     * @param array $criteria
     * @return Builder
     */
    public function advancedSearch(array $criteria): Builder
    {
        $query = Client::with(['spouse', 'user', 'projects', 'assets', 'expenses']);

        // Apply complex search criteria
        foreach ($criteria as $field => $condition) {
            if (!is_array($condition)) {
                continue;
            }

            $operator = $condition['operator'] ?? '=';
            $value = $condition['value'] ?? null;

            if ($value === null) {
                continue;
            }

            switch ($operator) {
                case 'like':
                    $query->where($field, 'LIKE', "%{$value}%");
                    break;
                case 'not_like':
                    $query->where($field, 'NOT LIKE', "%{$value}%");
                    break;
                case 'in':
                    if (is_array($value)) {
                        $query->whereIn($field, $value);
                    }
                    break;
                case 'not_in':
                    if (is_array($value)) {
                        $query->whereNotIn($field, $value);
                    }
                    break;
                case 'between':
                    if (is_array($value) && count($value) === 2) {
                        $query->whereBetween($field, $value);
                    }
                    break;
                case 'not_between':
                    if (is_array($value) && count($value) === 2) {
                        $query->whereNotBetween($field, $value);
                    }
                    break;
                case 'null':
                    $query->whereNull($field);
                    break;
                case 'not_null':
                    $query->whereNotNull($field);
                    break;
                case '>':
                case '<':
                case '>=':
                case '<=':
                case '!=':
                case '<>':
                    $query->where($field, $operator, $value);
                    break;
                default:
                    $query->where($field, $value);
            }
        }

        return $query;
    }

    /**
     * Get clients for export with optimized loading.
     *
     * @param array $clientIds
     * @return Collection
     */
    public function getForExport(array $clientIds = []): Collection
    {
        $query = Client::with([
            'spouse',
            'employees',
            'projects',
            'assets',
            'expenses'
        ]);

        if (!empty($clientIds)) {
            $query->whereIn('id', $clientIds);
        }

        return $query->get();
    }

    /**
     * Get paginated clients with optimized queries for large datasets.
     *
     * @param array $filters
     * @param int $perPage
     * @param array $columns
     * @return LengthAwarePaginator
     */
    public function getPaginatedOptimized(array $filters = [], int $perPage = 15, array $columns = ['*']): LengthAwarePaginator
    {
        $query = Client::select($columns);

        // Apply only essential filters for performance
        if (!empty($filters['search'])) {
            $query->search($filters['search']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        // Apply sorting
        $sortBy = $filters['sort_by'] ?? 'created_at';
        $sortDirection = $filters['sort_direction'] ?? 'desc';
        $query->orderBy($sortBy, $sortDirection);

        return $query->paginate($perPage);
    }

    /**
     * Get clients with advanced filters for admin interface.
     *
     * @param array $filters
     * @param int $perPage
     * @param string $sortBy
     * @param string $sortDirection
     * @return LengthAwarePaginator
     */
    public function getClientsWithFilters(array $filters = [], int $perPage = 25, string $sortBy = 'created_at', string $sortDirection = 'desc'): LengthAwarePaginator
    {
        $query = Client::with(['spouse', 'user'])
            ->withCount(['projects', 'assets', 'expenses']);

        // Apply search filter
        if (!empty($filters['search'])) {
            $searchTerm = $filters['search'];
            $query->where(function (Builder $q) use ($searchTerm) {
                // Search in client table fields
                $q->where('phone', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('mobile_number', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('ssn', 'LIKE', "%{$searchTerm}%")
                  // Search in user table fields
                  ->orWhereHas('user', function (Builder $userQuery) use ($searchTerm) {
                      $userQuery->where('first_name', 'LIKE', "%{$searchTerm}%")
                               ->orWhere('last_name', 'LIKE', "%{$searchTerm}%")
                               ->orWhere('email', 'LIKE', "%{$searchTerm}%")
                               ->orWhereRaw("CONCAT(first_name, ' ', COALESCE(middle_name, ''), ' ', last_name) LIKE ?", ["%{$searchTerm}%"]);
                  });
                
                if (is_numeric($searchTerm)) {
                    $q->orWhere('id', $searchTerm);
                }
            });
        }

        // Apply status filter
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        // Apply date range filters
        if (!empty($filters['date_from'])) {
            $query->where('created_at', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->where('created_at', '<=', $filters['date_to'] . ' 23:59:59');
        }

        // Apply marital status filter
        if (!empty($filters['marital_status'])) {
            $query->where('marital_status', $filters['marital_status']);
        }

        // Apply visa status filter
        if (!empty($filters['visa_status'])) {
            $query->where('visa_status', $filters['visa_status']);
        }

        // Apply assigned user filter
        if (!empty($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        // Apply sorting
        $allowedSortFields = [
            'id', 'first_name', 'last_name', 'email', 'phone', 'mobile_number',
            'date_of_birth', 'marital_status', 'occupation', 'status', 'city',
            'state', 'country', 'visa_status', 'created_at', 'updated_at'
        ];

        if (in_array($sortBy, $allowedSortFields)) {
            $query->orderBy($sortBy, $sortDirection);
        } else {
            $query->orderBy('created_at', 'desc');
        }

        // Add secondary sort by ID for consistent pagination
        if ($sortBy !== 'id') {
            $query->orderBy('id', 'asc');
        }

        return $query->paginate($perPage);
    }

    /**
     * Get client with all relationships for detailed view.
     *
     * @param int $id
     * @return Client|null
     */
    public function getClientWithRelations(int $id): ?Client
    {
        return Client::with([
            'spouse',
            'employees',
            'projects',
            'assets',
            'expenses',
            'user'
        ])->find($id);
    }

    /**
     * Get client statistics for admin dashboard.
     *
     * @return array
     */
    public function getClientStats(): array
    {
        $totalClients = Client::count();
        $activeClients = Client::where('status', 'active')->count();
        $archivedClients = Client::where('status', 'archived')->count();
        $newThisMonth = Client::where('created_at', '>=', now()->startOfMonth())->count();

        return [
            'total_clients' => $totalClients,
            'active_clients' => $activeClients,
            'inactive_clients' => Client::where('status', 'inactive')->count(),
            'archived_clients' => $archivedClients,
            'new_this_month' => $newThisMonth,
            'married_clients' => Client::where('marital_status', 'married')->count(),
            'clients_with_spouse' => Client::whereHas('spouse')->count(),
            'clients_with_projects' => Client::whereHas('projects')->count(),
            'clients_with_assets' => Client::whereHas('assets')->count(),
            'clients_with_expenses' => Client::whereHas('expenses')->count(),
        ];
    }
}