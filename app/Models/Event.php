<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'location',
        'latitude',
        'longitude',
        'notes',
        'max_participants',
        'cost',
        'event_date',
        'event_time',
        'registration_deadline',
        'event_type',
        'status',
        'created_by',
    ];

    protected static function booted()
    {
        static::deleting(function ($event) {
            $event->participants()->detach();
        });
    }

    protected $casts = [
        'event_date' => 'date',
        'registration_deadline' => 'datetime',
        'cost' => 'decimal:2',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
    ];

    const STATUS_DRAFT = 'draft';
    const STATUS_PUBLISHED = 'published';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';

    const TYPE_CULTURAL = 'cultural';
    const TYPE_RECREATIONAL = 'recreational';
    const TYPE_EDUCATIONAL = 'educational';
    const TYPE_SPORTS = 'sports';
    const TYPE_OTHER = 'other';

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function participants()
    {
        return $this->belongsToMany(User::class, 'event_user')
            ->withTimestamps()
            ->withPivot('registered_at');
    }

    public function isPublished(): bool
    {
        return $this->status === self::STATUS_PUBLISHED;
    }

    public function isDraft(): bool
    {
        return $this->status === self::STATUS_DRAFT;
    }

    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    public function isCancelled(): bool
    {
        return $this->status === self::STATUS_CANCELLED;
    }

    public function isRegistrationOpen(): bool
    {
        if (!$this->isPublished()) {
            return false;
        }

        if ($this->registration_deadline && Carbon::now()->greaterThan($this->registration_deadline)) {
            return false;
        }

        if ($this->max_participants && $this->participantsCount() >= $this->max_participants) {
            return false;
        }

        return true;
    }

    public function isFull(): bool
    {
        return $this->max_participants && $this->participantsCount() >= $this->max_participants;
    }

    public function availableSpots(): ?int
    {
        if (!$this->max_participants) {
            return null;
        }

        return max(0, $this->max_participants - $this->participantsCount());
    }

    public function participantsCount(): int
    {
        return $this->participants_count ?? $this->participants()->count();
    }

    public function canUnregister(User $user): bool
    {
        $registration = $this->participants()->where('user_id', $user->id)->first();

        if (!$registration) {
            return false;
        }

        $registrationTime = Carbon::parse($registration->pivot->registered_at);
        $deadline = $registrationTime->copy()->addDay();

        return Carbon::now()->isBefore($deadline);
    }

    public function scopePublished($query)
    {
        return $query->where('status', self::STATUS_PUBLISHED);
    }

    public function scopeUpcoming($query)
    {
        return $query->where('event_date', '>=', Carbon::today())
            ->orderBy('event_date', 'asc');
    }

    public function scopeOfType($query, $type)
    {
        return $query->where('event_type', $type);
    }
}
