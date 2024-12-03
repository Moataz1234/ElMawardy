<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        return view('notifications.index');
    }

    public function markAsRead($id)
    {
        Auth::user()->notifications->where('id', $id)->first()->markAsRead();
        return back()->with('success', 'Notification marked as read');
    }
}