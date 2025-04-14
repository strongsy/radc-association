<?php

namespace App\Models;

use App\Enums\Community;
use App\Enums\Membership;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
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
    use HasFactory, Notifiable, HasRoles;

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

    public function getFirstName(): string
    {
        return explode(' ', $this->name)[0];
    }

    /**
     * Relationships
     */
    public function mail(): belongsToMany {
        return $this->belongsToMany(Reply::class, 'replies', 'user_id', 'mail_id');
    }

    public function events(): hasMany
    {
        return $this->hasMany(Event::class);
    }
    public function attending(): BelongsToMany
    {
        return $this->belongsToMany(Event::class, 'attendances');
    }
}
