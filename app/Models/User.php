<?php

namespace App\Models;

use App\Enums\Community;
use App\Enums\Membership;
use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Spatie\Permission\Traits\HasRoles;

/**
 * @method static create(array $validated)
 */
class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, HasRoles, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'community',
        'membership',
        'affiliation',
        'is_subscribed',
        'is_blocked',
        'unsubscribe_token',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'community' => Community::class,
            'membership' => Membership::class,
        ];
    }

    protected static function booted(): void
    {
        static::creating(static function ($user) {
            $user->unsubscribe_token = $user->unsubscribe_token ?? Str::random(32);
        });

        static::updating(static function ($user) {
            if (! $user->unsubscribe_token) {
                $user->unsubscribe_token = Str::random(32);
            }
        });
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->map(fn (string $name) => Str::of($name)->substr(0, 1))
            ->implode('');
    }

    public function getFirstNameAttribute(): string
    {
        return explode(' ', $this->name)[0];
    }

    /**
     * Define the many-to-many relationship between the current model and the Reply model.
     */
    public function mail(): belongsToMany
    {
        return $this->belongsToMany(Reply::class, 'replies', 'user_id', 'mail_id');
    }

    /**
     * Define the one-to-many relationship between the current model and the Event model.
     */
    public function events(): hasMany
    {
        return $this->hasMany(Event::class, 'user_id');
    }

    /**
     * Define the many-to-many relationship between the current model and the Event model,
     * representing the events the user is attending.
     * Includes timestamp information for the pivot table entries.
     */
    public function attending(): BelongsToMany
    {
        return $this->belongsToMany(Event::class, 'attendees', 'user_id', 'event_id')->withTimestamps();
    }
}
