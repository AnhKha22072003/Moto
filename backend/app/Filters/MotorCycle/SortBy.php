<?php
namespace App\QueryFilters;

use Closure;

class SortBy
{
    public function handle($query, Closure $next)
    {
        $sortField = request('sort_by', 'id'); // mặc định sort theo id
        $sortDirection = request('sort_dir', 'desc'); // mặc định là desc

        // Danh sách các trường hợp cho phép sắp xếp
        $allowedSortFields = ['id', 'year', 'odo', 'price', 'created_at'];

        if (in_array($sortField, $allowedSortFields)) {
            $query->orderBy($sortField, $sortDirection === 'asc' ? 'asc' : 'desc');
        }

        return $next($query);
    }
}