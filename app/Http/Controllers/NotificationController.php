<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index() {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['error' => 'Not authenticated'], 401);
        }

        $notifs = $user->unreadNotifications()->orderBy('created_at', 'desc')->get();

        $data = $notifs->map(function ($notif) {
            $isUnread = is_null($notif->read_at);
            $icon = $notif->data['type'] == 'request_created' ? '📝' : ($notif->data['status'] == 1 ? '✅' : '❌');
    
            return [
                'id' => $notif->id,
                'message' => $notif->data['message'],
                'url' => $notif->data['url'],
                'icon' => $icon,
                'isUnread' => $isUnread,
                'created_at' => $notif->created_at->diffForHumans(),
            ];
        });

        return response()->json([
            'unreadCount' => $notifs->whereNull('read_at')->count(),
            'notifications' => $data,
        ]);
    
    }

    public function read($id) {
        $user = Auth::user();
        
        Log::info('Current user: ', ['user' => $user]);
    
        if (!$user) {
            return response()->json(['error' => 'User not authenticated'], 401);
        }
    
        // dump method existence
        if (!method_exists($user, 'notifications')) {
            return response()->json(['error' => 'notifications() method not available on user'], 500);
        }
    
        $notif = $user->notifications()->findOrFail($id);
        $notif->markAsRead();
    
        return response()->json(['success' => true]);
    }
}
