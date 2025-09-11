<?php

namespace App\Services;

use App\Models\Post;
use App\Models\PostAttachment;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class PostService
{
    public function create(array $data, ?UploadedFile $image, Request $request): Post
    {
        try {
            $post = Post::create($data);

            if (isset($data['tags'])) {
                $post->tags()->sync($data['tags']);
            }

            if ($image) {
                $this->storeAttachment($post, $image, $request);
            }

            // 投稿作成成功ログ
            AuditLogService::logPost('success', $data['user_id'], $post->id, 'create', $request);

            return $post;
        } catch (\Exception $e) {
            // 投稿作成失敗ログ
            AuditLogService::logPost('failed', $data['user_id'], null, 'create', $request);
            throw $e;
        }
    }

    public function update(Post $post, array $data, ?UploadedFile $image, Request $request): Post
    {
        try {
            // tags は別管理
            $tags = $data['tags'] ?? null;
            unset($data['tags']);

            if (!empty($data)) {
                $post->fill($data)->save();
            }

            if (is_array($tags)) {
                $post->tags()->sync($tags);
            }

            if ($image) {
                $this->replaceAttachment($post, $image, $request);
            }

            // 投稿更新成功ログ
            AuditLogService::logPost('success', $post->user_id, $post->id, 'update', $request);

            return $post;
        } catch (\Exception $e) {
            // 投稿更新失敗ログ
            AuditLogService::logPost('failed', $post->user_id, $post->id, 'update', $request);
            throw $e;
        }
    }

    private function storeAttachment(Post $post, UploadedFile $file, Request $request): void
    {
        try {
            // process image: resize to a maximum dimension to keep attachments consistent
            $stored = $this->processAndStoreImage($file, 1024);

            $post->attachment()->create([
                'path' => $stored['path'],
                'mime' => $stored['mime'],
                'size' => (int) $stored['size'],
            ]);

            // 既存の監査ログ
            \App\Services\AuditLogger::uploadSucceeded(
                $post->user_id, $post->id, $stored['path'], (string)$stored['mime'], (int)$stored['size'], $request
            );

            // 新しい監査ログ
            AuditLogService::logImageUpload('success', $post->user_id, $file->getClientOriginalName(), $request, [
                'path' => $stored['path'],
                'mime' => $stored['mime'],
                'size' => $stored['size']
            ]);
        } catch (\Exception $e) {
            // 画像アップロード失敗ログ
            AuditLogService::logImageUpload('failed', $post->user_id, $file->getClientOriginalName(), $request);
            throw $e;
        }
    }

    private function replaceAttachment(Post $post, UploadedFile $file, Request $request): void
    {
        if ($post->attachment && $post->attachment->path) {
            Storage::disk('public')->delete($post->attachment->path);
        }

        // process and store resized image
        $stored = $this->processAndStoreImage($file, 1024);

        $post->attachment()->updateOrCreate(
            [],
            ['path' => $stored['path'], 'mime' => $stored['mime'], 'size' => (int)$stored['size']]
        );

        \App\Services\AuditLogger::uploadSucceeded(
            $post->user_id, $post->id, $stored['path'], (string)$stored['mime'], (int)$stored['size'], $request
        );
    }

    /**
     * Resize image file (if image) and store it on the public disk.
     * Returns array with keys: path, mime, size
     */
    private function processAndStoreImage(UploadedFile $file, int $maxDimension = 1024): array
    {
        $mime = $file->getClientMimeType() ?: 'application/octet-stream';
        $isImage = str_starts_with($mime, 'image/');

        if (!$isImage) {
            // fallback: store original
            $path = $file->store('attachments', 'public');
            return ['path' => $path, 'mime' => $mime, 'size' => Storage::disk('public')->size($path)];
        }

        // load image from uploaded file
        $contents = file_get_contents($file->getRealPath());
        $src = @imagecreatefromstring($contents);
        if (!$src) {
            // not a valid image for GD, store original
            $path = $file->store('attachments', 'public');
            return ['path' => $path, 'mime' => $mime, 'size' => Storage::disk('public')->size($path)];
        }

        $width = imagesx($src);
        $height = imagesy($src);

        // calculate new size preserving aspect ratio and not exceeding maxDimension
        $ratio = min(1, $maxDimension / max($width, $height));
        $newW = (int) max(1, floor($width * $ratio));
        $newH = (int) max(1, floor($height * $ratio));

        $dst = imagecreatetruecolor($newW, $newH);

        // preserve transparency for PNG/GIF/WebP
        imagealphablending($dst, false);
        imagesavealpha($dst, true);
        $transparent = imagecolorallocatealpha($dst, 0, 0, 0, 127);
        imagefilledrectangle($dst, 0, 0, $newW, $newH, $transparent);

        imagecopyresampled($dst, $src, 0, 0, 0, 0, $newW, $newH, $width, $height);

        // determine extension and output buffer
        $ext = strtolower(pathinfo($file->getClientOriginalName(), PATHINFO_EXTENSION)) ?: 'png';
        $tempData = null;
        ob_start();
        switch ($ext) {
            case 'jpg':
            case 'jpeg':
                // for jpeg, fill background white if source has alpha
                $bg = imagecreatetruecolor($newW, $newH);
                $white = imagecolorallocate($bg, 255, 255, 255);
                imagefill($bg, 0, 0, $white);
                imagecopy($bg, $dst, 0, 0, 0, 0, $newW, $newH);
                imagejpeg($bg, null, 90);
                imagedestroy($bg);
                break;
            case 'gif':
                imagegif($dst);
                break;
            case 'webp':
                if (function_exists('imagewebp')) {
                    imagewebp($dst, null, 80);
                } else {
                    imagepng($dst);
                }
                break;
            default:
                imagepng($dst);
                break;
        }
        $tempData = ob_get_clean();

        imagedestroy($src);
        imagedestroy($dst);

        // store file
        $filename = uniqid('', true) . '.' . $ext;
        $path = 'attachments/' . $filename;
        Storage::disk('public')->put($path, $tempData);

        $size = Storage::disk('public')->size($path);
        $storedMime = $mime;

        return ['path' => $path, 'mime' => $storedMime, 'size' => $size];
    }
}