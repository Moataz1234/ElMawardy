<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Log;

class OrderPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any orders.
     */
    public function viewAny(User $user)
    {
        // Only Rabea can view orders
        return $user->name === 'rabea';
    }

    /**
     * Determine whether the user can view the order.
     */
    public function view(User $user, Order $order)
    {
        // Add your logic to check if Rabea can view this specific order
        return $user->name === 'rabea';
    }
    public function update(User $user, Order $order)
    {
        // Log the authorization check
        Log::info('Authorization check for user: ' . $user->id . ' with role: ' . $user->role . ' on order: ' . $order->id);
        
        // Check if the user has the 'admin' role
        return $user->name === 'rabea';
    }
}
