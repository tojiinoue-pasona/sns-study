<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Services\AuditLogger;
use App\Services\AuditLogService;
use App\Services\FollowService;

class UserFollowController extends Controller
{
    /**
     * フォローする
     */
    public function store(Request $request, User $user, FollowService $followService): JsonResponse
    {
        // 権限: 自分自身をフォロー不可 & Gate で制約
        $this->authorize('follow-user', $user);
        $followerId = auth()->id();

        try {
            $result = $followService->follow($request, auth()->user(), $user);
            if (($result['following'] ?? false) !== true) {
                return response()->json(['message' => '既にフォローしています', 'count' => $result['count'] ?? 0], 400);
            }
            return response()->json($result);
        } catch (\Exception $e) {
            AuditLogService::logFollow('failed', $followerId, $user->id, $request);
            return response()->json(['message' => 'フォローに失敗しました'], 500);
        }
    }

    /**
     * フォローを解除する
     */
    public function destroy(Request $request, User $user, FollowService $followService): JsonResponse
    {
        // 権限: Gate で制約
        $this->authorize('follow-user', $user);
        $result = $followService->unfollow($request, auth()->user(), $user);
        if (($result['following'] ?? true) === true) {
            return response()->json(['message' => 'フォローが見つかりません'], 404);
        }
        return response()->json($result);
    }
}
