<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class OrderPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any orders.
     */
    public function viewAny(User $user)
    {
        // Only Rabea can view orders
        return $user->name === 'Rabea';
    }

    /**
     * Determine whether the user can view the order.
     */
    public function view(User $user, Order $order)
    {
        // Add your logic to check if Rabea can view this specific order
        return $user->name === 'Rabea';
    }
}
