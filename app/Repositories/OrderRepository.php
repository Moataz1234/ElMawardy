<?php

namespace App\Repositories;

use App\Models\Order;
use Carbon\Carbon;
use App\Constants\OrderStatus;
class OrderRepository
{
    public function getFilteredOrders($sort, $direction, $searchType = null, $searchValue = null)
    {
        $query = Order::query()
            ->where('status', '<>', OrderStatus::PENDING)
            ->where('status', '<>', OrderStatus::COMPLETED);
    
        if ($searchType && $searchValue) {
            $query->where($searchType, 'LIKE', "%{$searchValue}%");
        }
    
        return $query->orderBy($sort, $direction)
            ->paginate(20);
    }

    public function getPendingOrders()
    {
        return Order::where('status', OrderStatus::PENDING)->get();
    }

    public function getCompletedOrders()
    {
        return Order::where('status', OrderStatus::COMPLETED)->get();
    }

    public function getToPrintOrders($filter, $sort, $direction)
    {
        return Order::where('status', '!=', OrderStatus::PENDING)
            ->when($filter === 'today', function ($query) {
                $query->whereDate('order_date', Carbon::today());
            })
            ->orderBy($sort, $direction)
            ->paginate(20);
    }
}