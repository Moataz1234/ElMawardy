<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

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
    public function stream()
    {
        // Ensure headers are sent before any output
        header('Content-Type: text/event-stream');
        header('Cache-Control: no-cache');
        header('Connection: keep-alive');
        header('X-Accel-Buffering: no'); // Disable nginx buffering

        // Get current user's shop name
        $shop_name = str_replace(' ', '_', Auth::user()->shop_name);

        // Path to notification file
        $file = storage_path("app/notifications_{$shop_name}.txt");

        // Check if notification file exists
        if (File::exists($file)) {
            // Read the notification
            $notification = File::get($file);

            // Send the notification
            echo "data: " . $notification . "\n\n";

            // Remove the notification file after sending
            File::delete($file);
        } else {
            // Send a keep-alive message
            echo "data: {}\n\n";
        }

        // Flush output
        ob_flush();
        flush();
    }
    
    public function clear()
    {
        $shop_name = Auth::user()->shop_name;
        $file = storage_path("app/notifications_{$shop_name}.txt");
        if (file_exists($file)) {
            unlink($file);
        }
        return response()->json(['status' => 'success']);
    }
}
