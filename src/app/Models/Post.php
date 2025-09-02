<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, BelongsToMany, HasMany, HasOne};

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

    public function likedByUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'likes', 'post_id', 'user_id');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class)->latest('id');
    }
}
