<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, BelongsToMany, HasOne};

class Post extends Model
{
    protected $fillable = ['user_id', 'visibility_id', 'body'];

    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function visibility(): BelongsTo { return $this->belongsTo(Visibility::class); }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'post_tags');
    }

    public function attachment(): HasOne
    {
        return $this->hasOne(PostAttachment::class);
    }
}
