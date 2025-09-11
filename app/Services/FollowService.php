<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FollowService
{
    /**
     * フォロー処理
     * 戻り値: ['following' => bool, 'count' => int]
     */
    public function follow(Request $request, User $follower, User $target): array
    {
        // 既にフォロー済みか
        $exists = DB::table('follows')->where([
            'follower_id' => $follower->id,
            'followee_id' => $target->id,
        ])->exists();

        if ($exists) {
            AuditLogService::logFollow('failed', $follower->id, $target->id, $request);
            return ['following' => true, 'count' => $this->countFollowers($target->id)];
        }

        DB::table('follows')->insert([
            'follower_id' => $follower->id,
            'followee_id' => $target->id,
            'created_at'  => now(),
        ]);

        $count = $this->countFollowers($target->id);
        AuditLogger::followToggled($follower->id, $target->id, true, $request);
        AuditLogService::logFollow('success', $follower->id, $target->id, $request);

        return ['following' => true, 'count' => $count];
    }

    /**
     * フォロー解除処理
     */
    public function unfollow(Request $request, User $follower, User $target): array
    {
        $deleted = DB::table('follows')->where([
            'follower_id' => $follower->id,
            'followee_id' => $target->id,
        ])->delete();

        if (!$deleted) {
            return ['following' => false, 'count' => $this->countFollowers($target->id)];
        }

        $count = $this->countFollowers($target->id);
        AuditLogger::followToggled($follower->id, $target->id, false, $request);
        AuditLogService::logFollow('success', $follower->id, $target->id, $request);

        return ['following' => false, 'count' => $count];
    }

    private function countFollowers(int $userId): int
    {
        return (int) DB::table('follows')->where('followee_id', $userId)->count();
    }
}

