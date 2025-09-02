<?php
namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Services\AuditLogger;

class PostLikeController extends Controller
{
    public function __invoke(Request $request, Post $post): JsonResponse
    {
        $userId = auth()->id() ?: User::query()->value('id');
        if (!$userId) {
            AuditLogger::likeFailed('no_user', $request, ['post_id' => $post->id]);
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
                'user_id'    => $userId,
                'post_id'    => $post->id,
                'created_at' => now(),
            ]);
            $liked = true;
        }

        $count = DB::table('likes')->where('post_id', $post->id)->count();
        AuditLogger::likeToggled($userId, $post->id, $liked, $request);

        return response()->json(['liked' => $liked, 'count' => $count]);
    }
}