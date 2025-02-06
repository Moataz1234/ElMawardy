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
        header('Content-Type: text/event-stream');
        header('Cache-Control: no-cache');
        header('Connection: keep-alive');
        header('X-Accel-Buffering: no');

        $shop_name = str_replace(' ', '_', Auth::user()->shop_name);
        $file = storage_path("app/notifications_{$shop_name}.txt");

        if (File::exists($file)) {
            $notification = File::get($file);
            echo "data: " . $notification . "\n\n";
            File::delete($file);
        } else {
            echo "data: {}\n\n";
        }

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
