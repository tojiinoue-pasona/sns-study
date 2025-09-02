<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Services\AuditLogger;

class UserFollowController extends Controller
{
    public function __invoke(Request $request, User $user): JsonResponse
    {
        $followerId = auth()->id() ?: User::query()->value('id');
        if (!$followerId) {
            AuditLogger::followFailed('no_user', $request, ['followee_id' => $user->id]);
            return response()->json(['message' => 'ユーザーが存在しません'], 401);
        }

        // 自分自身は不可
        if ((int)$followerId === (int)$user->id) {
            $count = DB::table('follows')->where('followee_id', $user->id)->count();
            AuditLogger::followFailed('self_target', $request, ['user_id' => $user->id, 'count' => $count]);
            return response()->json(['following' => false, 'count' => $count], 400);
        }

        $exists = DB::table('follows')->where([
            'follower_id' => $followerId,
            'followee_id' => $user->id,
        ])->exists();

        if ($exists) {
            DB::table('follows')->where([
                'follower_id' => $followerId,
                'followee_id' => $user->id,
            ])->delete();
            $following = false;
        } else {
            DB::table('follows')->insert([
                'follower_id' => $followerId,
                'followee_id' => $user->id,
                'created_at'  => now(),
            ]);
            $following = true;
        }

        $count = DB::table('follows')->where('followee_id', $user->id)->count();
        AuditLogger::followToggled($followerId, $user->id, $following, $request);

        return response()->json(['following' => $following, 'count' => $count]);
    }
}