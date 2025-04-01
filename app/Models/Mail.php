<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Mail extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'subject',
        'message',
    ];

    /**
     * Relationships
     */
    public function mail(): belongsToMany {
        return $this->belongsToMany(MailUser::class, 'mail_user', 'mail_id', 'user_id');
    }
}
