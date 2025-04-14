<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Event extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'date',
        'time',
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

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    public function attendees(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'attendances');
    }

    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }
}
