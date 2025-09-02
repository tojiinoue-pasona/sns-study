<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AuditLogger
{
    public static function likeToggled(?int $userId, ?int $postId, bool $liked, Request $req): void
    {
        Log::info('like.toggled', self::ctx($req) + [
            'user_id' => $userId,
            'post_id' => $postId,
            'liked'   => $liked,
        ]);
    }

    public static function likeFailed(string $reason, Request $req, array $extra = []): void
    {
        Log::info('like.failed', self::ctx($req) + ['reason' => $reason] + $extra);
    }

    public static function followToggled(?int $followerId, ?int $followeeId, bool $following, Request $req): void
    {
        Log::info('follow.toggled', self::ctx($req) + [
            'follower_id' => $followerId,
            'followee_id' => $followeeId,
            'following'   => $following,
        ]);
    }

    public static function followFailed(string $reason, Request $req, array $extra = []): void
    {
        Log::info('follow.failed', self::ctx($req) + ['reason' => $reason] + $extra);
    }

    public static function uploadSucceeded(?int $userId, ?int $postId, string $path, string $mime, int $size, Request $req): void
    {
        Log::info('upload.succeeded', self::ctx($req) + [
            'user_id' => $userId,
            'post_id' => $postId,
            'path'    => $path,
            'mime'    => $mime,
            'size'    => $size,
        ]);
    }

    public static function uploadFailed(string $reason, Request $req, array $extra = []): void
    {
        Log::info('upload.failed', self::ctx($req) + ['reason' => $reason] + $extra);
    }

    private static function ctx(Request $req): array
    {
        return [
            'ip'    => $req->ip(),
            'ua'    => substr((string) $req->userAgent(), 0, 255),
            'route' => optional($req->route())->getName(),
        ];
    }
}