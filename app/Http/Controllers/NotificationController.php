<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function index(Request $request): View
    {
        return view('notifications.index', [
            'notifications' => $request->user()->notifications()->latest()->paginate(15),
        ]);
    }

    public function open(Request $request, string $notification): RedirectResponse
    {
        $record = $request->user()->notifications()->findOrFail($notification);
        $record->markAsRead();
        $destination = $record->data['url'] ?? null;
        $destinationHost = is_string($destination) ? parse_url($destination, PHP_URL_HOST) : null;
        $destinationScheme = is_string($destination) ? parse_url($destination, PHP_URL_SCHEME) : null;

        if ($destinationHost !== $request->getHost() || ! in_array($destinationScheme, ['http', 'https'], true)) {
            return redirect()->route('notifications.index');
        }

        return redirect()->to($destination);
    }

    public function markAsRead(Request $request, string $notification): RedirectResponse
    {
        $record = $request->user()->notifications()->findOrFail($notification);
        $record->markAsRead();

        return back()->with('status', 'Notification marked as read.');
    }

    public function markAllAsRead(Request $request): RedirectResponse
    {
        $request->user()->unreadNotifications()->update(['read_at' => now()]);

        return back()->with('status', 'All notifications marked as read.');
    }
}
