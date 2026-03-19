<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserPreferencesController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        return view('preferences.index', compact('user'));
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'email_notifications' => 'boolean',
            'preferred_types' => 'array',
            'preferred_types.*' => 'in:cultural,recreational,educational,sports,other'
        ]);

        $user->email_notifications = $request->has('email_notifications');
        
        $preferences = [
            'types' => $request->input('preferred_types', [])
        ];
        
        $user->updatePreferences($preferences);

        return redirect()->route('preferences.index')
            ->with('success', 'Preferenze aggiornate con successo!');
    }

    public function notifications()
    {
        $notifications = auth()->user()
            ->notifications()
            ->paginate(20);

        $unreadCount = auth()->user()->unreadNotifications()->count();

        return view('preferences.notifications', compact('notifications', 'unreadCount'));
    }

    public function markAsRead($id)
    {
        $notification = auth()->user()
            ->notifications()
            ->findOrFail($id);

        $notification->markAsRead();

        return back()->with('success', 'Notifica segnata come letta');
    }
}
