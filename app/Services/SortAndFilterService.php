<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

class SortAndFilterService
{
    protected $defaultPerPage = 36;
    protected $allowedSortFields = [
        'serial_number' => 'asc',
        'model' => 'asc',
        'quantity' => 'desc',
        'kind' => 'asc',
        'created_at' => 'desc'
    ];

    protected function applySearchFilter(Builder $query, string $search): void
    {
        $normalizedSearch = ltrim(preg_replace('/\D/', '', $search), '0');
        
        $query->where(function ($query) use ($search, $normalizedSearch) {
            $query->where('model', 'like', "%{$normalizedSearch}%")
                  ->orWhere('model', 'like', "%-" . substr($normalizedSearch, 1) . "%")
                  ->orWhere('serial_number', 'like', "%{$search}%");
        });
    }

    public function applyFilters(Builder $query, Request $request, array $allowedFilters): Builder
    {
        foreach ($allowedFilters as $filter) {
            if ($request->filled($filter)) {
                if ($filter === 'search') {
                    $this->applySearchFilter($query, $request->input('search'));
                } else {
                    $filterValues = (array) $request->input($filter);
                    if (!empty($filterValues)) {
                        $query->whereIn($filter, $filterValues);
                    }
                }
            }
        }

        return $query;
    }


    /**
     * Apply sorting to the query
     */
    protected function applySort(Builder $query, Request $request): Builder
    {
        $sortField = $request->get('sort', 'created_at');
        $direction = $request->get('direction');

        if (array_key_exists($sortField, $this->allowedSortFields)) {
            $direction = $direction ?: $this->allowedSortFields[$sortField];
            $query->orderBy($sortField, $direction);
        }

        return $query;
    }

    /**
     * Apply filters and sorting, then paginate results
     */
    public function getFilteredAndSortedResults(
        Builder $query, 
        Request $request, 
        array $allowedFilters = [],
        ?int $perPage = null
    ): LengthAwarePaginator {
        $query = $this->applyFilters($query, $request, $allowedFilters);
        $query = $this->applySort($query, $request);

        return $query->paginate($perPage ?? $this->defaultPerPage)
                    ->appends($request->all());
    }
}