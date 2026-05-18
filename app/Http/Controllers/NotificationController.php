<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function markRead(Request $request, Notification $notification): RedirectResponse
    {
        if ($notification->user_id === $request->user()->id) {
            $notification->update(['read_at' => now()]);
        }

        return redirect($notification->url ?? back()->getTargetUrl());
    }

    public function markAllRead(Request $request): RedirectResponse
    {
        $request->user()
            ->appNotifications()
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return back()->with('status', 'All notifications marked as read.');
    }
}
