<?php

namespace App\Policies;

use App\Models\Post;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class PostPolicy
{
    /**
     * 投稿閲覧権限
     * - public: 全員
     * - draft: 投稿者のみ
     * - followers: 投稿者またはフォロワー
     */
    public function view(?User $user, Post $post): bool
    {
        // public投稿は全員閲覧可能
        if ($post->visibility->code === 'public') {
            return true;
        }

        // 未ログインユーザーはpublic以外閲覧不可
        if (!$user) {
            return false;
        }

        // 管理者または投稿者は全て閲覧可能
        if ($user->isAdmin() || $post->user_id === $user->id) {
            return true;
        }

        // draft投稿は投稿者のみ
        if ($post->visibility->code === 'draft') {
            return false;
        }

        // followers投稿はフォロワーのみ
        if ($post->visibility->code === 'followers') {
            return $user->isFollowing($post->user);
        }

        return false;
    }

    /**
     * 投稿作成権限
     */
    public function create(User $user): bool
    {
        return true; // ログインユーザーは誰でも投稿可能
    }

    /**
     * 投稿更新権限
     */
    public function update(User $user, Post $post): bool
    {
        return $user->isAdmin() || $post->user_id === $user->id;
    }

    /**
     * 投稿削除権限
     */
    public function delete(User $user, Post $post): bool
    {
        return $user->isAdmin() || $post->user_id === $user->id;
    }

    /**
     * 投稿復元権限（管理者のみ）
     */
    public function restore(User $user, Post $post): bool
    {
        return $user->isAdmin();
    }

    /**
     * 投稿完全削除権限（管理者のみ）
     */
    public function forceDelete(User $user, Post $post): bool
    {
        return $user->isAdmin();
    }
}
