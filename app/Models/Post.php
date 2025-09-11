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

    // 共通スコープ
    public function scopePublic($query)
    {
        return $query->whereHas('visibility', fn($q) => $q->where('code', 'public'));
    }

    public function scopeWithBasics($query)
    {
        // include avatar and followers_count when eager-loading user to avoid N+1 queries
        return $query->with([
            'user' => function ($q) { $q->select('id','name','avatar')->withCount('followers'); },
            'visibility:id,code',
            'attachment'
        ]);
    }

    public function scopeWithLikeCount($query)
    {
        return $query->withCount('likedByUsers');
    }

    public function scopeLatestFirst($query)
    {
    return $query->latest('created_at');
    }
}
