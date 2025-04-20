<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Event extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'location',
        'date',
        'time',
        'allow_guests',
        'max_guests_per_user',
        'min_attendees',
        'max_attendees',
        'user_id',
        'photo_path',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'time' => 'datetime',
        ];
    }

    /**
     * Scope a query to clean expired entries by setting the 'deleted_at' timestamp.
     */
    public function scopeCleanExpired($query): void
    {
        $query->where('date', '<=', now())->whereNull('deleted_at')->update(['deleted_at' => now()]);
    }

    /**
     * Calculate the total count of attendees, including users and their guests.
     *
     * @return int The total number of attendees.
     */
    public function totalAttendeeCount(): int
    {
        return $this->attendances()->sum(DB::raw('1 + guest_count')); // user + their guests
    }

    /**
     * Defines a relationship where this entity belongs to a User.
     *
     * Establishes an inverse one-to-many relationship between this entity and the User model.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Define a many-to-many relationship with the User model.
     */
    public function attendees(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'attendees', 'event_id', 'user_id')->withTimestamps();
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('event_cover')
            ->singleFile();

        $this->addMediaCollection('event_gallery');
    }

    /*public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }*/

    public function guests(): HasMany
    {
        return $this->hasMany(EventGuest::class);
    }

    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    public function ratings(): MorphMany
    {
        return $this->morphMany(Rating::class, 'ratable');
    }

    public function getAttendeeCount(): int
    {
        return $this->attendees()->where('is_attending', true)->count();
    }

    public function getGuestCount(): int
    {
        return $this->guests()->count();
    }

    public function getTotalAttendeeCount(): int
    {
        return $this->getAttendeeCount() + $this->getGuestCount();
    }

    public function isUserAttending(User $user): bool
    {
        return $this->attendees()
            ->where('user_id', $user->id)
            ->where('is_attending', true)
            ->exists();
    }

    public function canInviteMoreGuests(User $user): bool
    {
        if (! $this->allow_guests) {
            return false;
        }

        $userGuestCount = $this->guests()->where('invited_by', $user->id)->count();

        return $userGuestCount < $this->max_guests_per_user;
    }

    public function getAverageRating(): float
    {
        return (float) $this->ratings()->avg('rating') ?: 0;
    }
}
