<?php

namespace App\Policies;

use App\Models\Comment;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class CommentPolicy
{
    /**
     * コメント作成権限
     */
    public function create(User $user): bool
    {
        return true; // ログインユーザーは誰でもコメント可能
    }

    /**
     * コメント削除権限
     * - コメント作成者本人
     * - 管理者
     */
    public function delete(User $user, Comment $comment): bool
    {
        return $user->isAdmin() || $comment->user_id === $user->id;
    }

    /**
     * コメント復元権限（管理者のみ）
     */
    public function restore(User $user, Comment $comment): bool
    {
        return $user->isAdmin();
    }

    /**
     * コメント完全削除権限（管理者のみ）
     */
    public function forceDelete(User $user, Comment $comment): bool
    {
        return $user->isAdmin();
    }
}
