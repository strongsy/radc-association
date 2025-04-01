<?php

namespace App\Models;

use App\Enums\Community;
use App\Enums\Membership;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Registrant extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'community',
        'membership',
        'affiliation',
        'is_subscribed',
    ];

    protected $casts = [
        'community' => Community::class,
        'membership' => Membership::class,
    ];

    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->map(fn (string $name) => Str::of($name)->substr(0, 1))
            ->implode('');
    }
}
