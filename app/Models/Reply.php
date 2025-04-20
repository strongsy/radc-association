<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @method static create(array $array)
 */
class Reply extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'mail_id',
        'user_id',
        'subject',
        'message',
    ];

    // Relation to the Mail model
    /*public function mail(): BelongsTo
    {
        return $this->belongsTo(Mail::class);
    }*/

    // Relation to the User model
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
