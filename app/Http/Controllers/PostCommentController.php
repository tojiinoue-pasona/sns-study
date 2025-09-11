<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Comment;
use App\Http\Requests\CommentStoreRequest;
use Illuminate\Http\Request;

class PostCommentController extends Controller
{
    /**
     * コメント投稿
     */
    public function store(CommentStoreRequest $request, Post $post)
    {
        $this->authorize('create', Comment::class);
        // コメント対象の投稿を閲覧できるか確認
        $this->authorize('view', $post);
        
        $data = $request->validated();

        // 認証ユーザーのコメント作成
        $post->comments()->create([
            'user_id' => auth()->id(),
            'body'    => $data['body'],
        ]);

        return back()->with('status', 'コメントを投稿しました！');
    }

    /**
     * コメント削除
     */
    public function destroy(Comment $comment)
    {
        $this->authorize('delete', $comment);

        $comment->delete();

        return back()->with('status', 'コメントを削除しました。');
    }
}
