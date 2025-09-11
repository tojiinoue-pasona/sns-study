<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Services\AuditLogService;
use App\Services\LikeService;

class PostLikeController extends Controller
{
    /**
     * いいねを追加
     */
    public function store(Request $request, Post $post, LikeService $likeService): JsonResponse
    {
        // 閲覧不可な投稿への操作を禁止
        $this->authorize('view', $post);
        $user = auth()->user();
        
        try {
            $result = $likeService->like($request, $user, $post);
            if ($result['liked'] === false) {
                return response()->json(['message' => '既にいいねしています', 'count' => $result['count']], 400);
            }
            return response()->json($result);
        } catch (\Throwable $e) {
            AuditLogService::logLike('failed', $user->id, $post->id, $request);
            return response()->json(['message' => 'いいねの追加に失敗しました'], 500);
        }
    }

    /**
     * いいねを削除
     */
    public function destroy(Request $request, Post $post, LikeService $likeService): JsonResponse
    {
        // 閲覧不可な投稿への操作を禁止
        $this->authorize('view', $post);
        $user = auth()->user();

        try {
            $result = $likeService->unlike($request, $user, $post);
            return response()->json($result);
        } catch (\Throwable $e) {
            AuditLogService::logLike('failed', $user->id, $post->id, $request);
            return response()->json(['message' => 'いいねの削除に失敗しました'], 500);
        }
    }
}
