<?php
namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class PostLikeController extends Controller
{
    public function __invoke(Request $request, Post $post): JsonResponse
    {
        // 認証前の擬似ユーザー（先頭のユーザーを使用）
        $userId = auth()->id() ?: User::query()->value('id');
        if (!$userId) {
            return response()->json(['message' => 'ユーザーが存在しません'], 401);
        }

        $exists = DB::table('likes')->where([
            'user_id' => $userId,
            'post_id' => $post->id,
        ])->exists();

        if ($exists) {
            DB::table('likes')->where([
                'user_id' => $userId,
                'post_id' => $post->id,
            ])->delete();
            $liked = false;
        } else {
            DB::table('likes')->insert([
                'user_id' => $userId,
                'post_id' => $post->id,
                'created_at' => now(),
            ]);
            $liked = true;
        }

        $count = DB::table('likes')->where('post_id', $post->id)->count();

        return response()->json(['liked' => $liked, 'count' => $count]);
    }
}