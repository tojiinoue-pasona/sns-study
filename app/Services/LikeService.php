<?php

namespace App\Services;

use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LikeService
{
    /**
     * いいね追加処理
     * 戻り値: ['liked' => bool, 'count' => int]
     */
    public function like(Request $request, User $user, Post $post): array
    {
        // 既にいいね済みなら失敗応答
        $exists = DB::table('likes')->where([
            'user_id' => $user->id,
            'post_id' => $post->id,
        ])->exists();

        if ($exists) {
            AuditLogService::logLike('failed', $user->id, $post->id, $request);
            return ['liked' => false, 'count' => $this->count($post->id)];
        }

        DB::table('likes')->insert([
            'user_id'    => $user->id,
            'post_id'    => $post->id,
            'created_at' => now(),
        ]);

        $count = $this->count($post->id);
        AuditLogger::likeToggled($user->id, $post->id, true, $request);
        AuditLogService::logLike('success', $user->id, $post->id, $request);

        return ['liked' => true, 'count' => $count];
    }

    /**
     * いいね削除処理
     */
    public function unlike(Request $request, User $user, Post $post): array
    {
        $deleted = DB::table('likes')->where([
            'user_id' => $user->id,
            'post_id' => $post->id,
        ])->delete();

        if (!$deleted) {
            AuditLogService::logLike('failed', $user->id, $post->id, $request);
            return ['liked' => false, 'count' => $this->count($post->id)];
        }

        $count = $this->count($post->id);
        AuditLogger::likeToggled($user->id, $post->id, false, $request);
        AuditLogService::logLike('success', $user->id, $post->id, $request);

        return ['liked' => false, 'count' => $count];
    }

    private function count(int $postId): int
    {
        return (int) DB::table('likes')->where('post_id', $postId)->count();
    }
}

