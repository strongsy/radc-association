<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Album extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'gallery_id',
        'name',
        'description',
        'slug',
    ];

    public function mail(): belongsTo
    {
        return $this->belongsTo(Gallery::class);
    }

    public function images(): hasMany
    {
        return $this->hasMany(Image::class);
    }
}
