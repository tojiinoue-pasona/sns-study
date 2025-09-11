<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class AuditLogService
{
    /**
     * アクション実行の監査ログを記録
     * 
     * @param string $action いいね、フォロー、画像アップロード等
     * @param string $status success | failed
     * @param int|null $userId 実行ユーザーID
     * @param int|null $targetId 対象ID（投稿ID、ユーザーID等）
     * @param string|null $route ルート名
     * @param Request|null $request リクエストオブジェクト
     * @param array $additional 追加情報
     */
    public static function log(
        string $action,
        string $status,
        ?int $userId = null,
        ?int $targetId = null,
        ?string $route = null,
        ?Request $request = null,
        array $additional = []
    ): void {
        $logData = [
            'action' => $action,
            'status' => $status,
            'user_id' => $userId,
            'target_id' => $targetId,
            'route' => $route,
            'ip' => $request?->ip(),
            'user_agent' => $request?->userAgent(),
            'timestamp' => now()->toISOString(),
            ...$additional
        ];

        // 成功・失敗に応じてログレベルを分ける
        if ($status === 'success') {
            Log::info("AUDIT: {$action} {$status}", $logData);
        } else {
            Log::warning("AUDIT: {$action} {$status}", $logData);
        }
    }

    /**
     * いいね操作のログ
     */
    public static function logLike(string $status, int $userId, int $postId, Request $request): void
    {
        self::log(
            action: 'like_post',
            status: $status,
            userId: $userId,
            targetId: $postId,
            route: $request->route()?->getName(),
            request: $request
        );
    }

    /**
     * フォロー操作のログ
     */
    public static function logFollow(string $status, int $followerId, int $followeeId, Request $request): void
    {
        self::log(
            action: 'follow_user',
            status: $status,
            userId: $followerId,
            targetId: $followeeId,
            route: $request->route()?->getName(),
            request: $request
        );
    }

    /**
     * 画像アップロードのログ
     */
    public static function logImageUpload(string $status, int $userId, ?string $filename, Request $request, array $additional = []): void
    {
        self::log(
            action: 'image_upload',
            status: $status,
            userId: $userId,
            targetId: null,
            route: $request->route()?->getName(),
            request: $request,
            additional: [
                'filename' => $filename,
                ...$additional
            ]
        );
    }

    /**
     * 投稿操作のログ
     */
    public static function logPost(string $status, int $userId, ?int $postId, string $operation, Request $request): void
    {
        self::log(
            action: "post_{$operation}",
            status: $status,
            userId: $userId,
            targetId: $postId,
            route: $request->route()?->getName(),
            request: $request
        );
    }

    /**
     * 認証操作のログ
     */
    public static function logAuth(string $status, ?int $userId, string $operation, Request $request): void
    {
        self::log(
            action: "auth_{$operation}",
            status: $status,
            userId: $userId,
            targetId: null,
            route: $request->route()?->getName(),
            request: $request
        );
    }
}
