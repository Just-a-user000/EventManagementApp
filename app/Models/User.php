<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'event_preferences',
        'email_notifications',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'event_preferences' => 'array',
        'email_notifications' => 'boolean',
    ];

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isUser(): bool
    {
        return $this->role === 'user';
    }

    public function createdEvents()
    {
        return $this->hasMany(Event::class, 'created_by');
    }

    public function registeredEvents()
    {
        return $this->belongsToMany(Event::class, 'event_user')
            ->withTimestamps()
            ->withPivot('registered_at');
    }

    public function isRegisteredFor(Event $event): bool
    {
        return $this->registeredEvents->contains($event);
    }

    public function updatePreferences(array $preferences): void
    {
        $this->event_preferences = $preferences;
        $this->save();
    }

    public function getPreferredEventTypes(): array
    {
        return $this->event_preferences['types'] ?? [];
    }
}
