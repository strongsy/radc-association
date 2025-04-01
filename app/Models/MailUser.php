<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class MailUser extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'mail_id',
        'subject',
        'message',
        'replied_at',
    ];

    protected function casts(): array
    {
        return [
            'replied_at' => 'datetime',
        ];
    }

    /**
     * Relationships
     */
    public function mail(): MailUser|HasMany
    {
        return $this->hasMany(Mail::class, 'id', 'mail_id');
    }

    public function user(): MailUser|HasMany
    {
        return $this->hasMany(User::class, 'id', 'user_id');
    }
}
