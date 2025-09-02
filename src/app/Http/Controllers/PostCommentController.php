<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\User;
use App\Http\Requests\CommentStoreRequest;

class PostCommentController extends Controller
{
    public function __invoke(CommentStoreRequest $request, Post $post)
    {
        $data = $request->validated();

        // 認証未導入の暫定：先頭のユーザーを利用
        $userId = auth()->id() ?: User::query()->value('id');
        if (!$userId) {
            return back()->withInput()->with('error', 'ユーザーが存在しないためコメントできません。');
        }

        $post->comments()->create([
            'user_id' => $userId,
            'body'    => $data['body'],
        ]);

        return back()->with('status', 'commented');
    }
}