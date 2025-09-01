<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, BelongsToMany, HasMany};

class Post extends Model
{
    protected $fillable = ['user_id', 'visibility_id', 'body'];

    // --- Relations ---
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function visibility(): BelongsTo { return $this->belongsTo(Visibility::class); }
    public function tags(): BelongsToMany { return $this->belongsToMany(Tag::class, 'post_tags'); }
    // 公開投稿のみ（visibilities.code = 'public'）
    public function scopePublic($q) {
        return $q->whereHas('visibility', fn($qq) => $qq->where('code', 'public'));
    }
    public function likedByUsers(): BelongsToMany
    {
        // users ↔ posts を likes テーブル（pivot）で中継
        return $this->belongsToMany(User::class, 'likes'); // cols: user_id, post_id
    }
}
