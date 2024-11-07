<?php

namespace App\Services;

class SortAndFilterService
{
    public function getSortColumnAndDirection($sort)
    {
        switch (strtolower($sort)) {
            case 'serial_number':
                return ['serial_number', 'asc'];
            // case 'shop_name':
            //     return ['shop_name', 'asc'];
            case 'model':
                return ['model', 'asc'];
            case 'quantity':
                return ['quantity', 'desc'];
            case 'kind':
                return ['kind', 'asc'];
            default:
                return ['created_at', 'desc']; // Default sorting
        }
    }
}
