<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ImageService
{
    /**
     * アバター画像を中央トリミングし、256x256にリサイズして保存
     * 返り値は保存した相対パス（例: avatars/xxxx.jpg）
     */
    public static function processAndStoreAvatar(UploadedFile $file): string
    {
        $origExt = strtolower($file->getClientOriginalExtension() ?? 'jpg');
        $tmpPath = $file->getRealPath();

        // 形式に応じて読み込み
        switch ($origExt) {
            case 'png':
                $srcImg = @imagecreatefrompng($tmpPath);
                break;
            case 'gif':
                $srcImg = @imagecreatefromgif($tmpPath);
                break;
            case 'webp':
                $srcImg = function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($tmpPath) : null;
                break;
            default:
                $srcImg = @imagecreatefromjpeg($tmpPath);
                break;
        }

        if (!$srcImg) {
            throw new \RuntimeException('画像の読み込みに失敗しました。');
        }

        $srcW = imagesx($srcImg);
        $srcH = imagesy($srcImg);
        $dstSize = 256;

        // 中央トリミング（正方形）
        $srcRatio = $srcW / max(1, $srcH);
        $dstRatio = 1; // 正方形

        if ($srcRatio > $dstRatio) {
            // 横長 → 幅をカット
            $newH = $srcH;
            $newW = (int)($srcH * $dstRatio);
            $srcX = (int)(($srcW - $newW) / 2);
            $srcY = 0;
        } else {
            // 縦長 → 高さをカット
            $newW = $srcW;
            $newH = (int)($srcW / $dstRatio);
            $srcX = 0;
            $srcY = (int)(($srcH - $newH) / 2);
        }

        $dstImg = imagecreatetruecolor($dstSize, $dstSize);

        // 透過の扱い
        if (in_array($origExt, ['png','gif','webp'], true)) {
            imagealphablending($dstImg, false);
            imagesavealpha($dstImg, true);
            $transparent = imagecolorallocatealpha($dstImg, 0, 0, 0, 127);
            imagefilledrectangle($dstImg, 0, 0, $dstSize, $dstSize, $transparent);
        } else {
            $white = imagecolorallocate($dstImg, 255, 255, 255);
            imagefilledrectangle($dstImg, 0, 0, $dstSize, $dstSize, $white);
        }

        imagecopyresampled($dstImg, $srcImg, 0, 0, $srcX, $srcY, $dstSize, $dstSize, $newW, $newH);

        // メモリにエンコード
        ob_start();
        if (in_array($origExt, ['png','gif','webp'], true)) {
            switch ($origExt) {
                case 'png':
                    imagepng($dstImg, null, 6);
                    $ext = 'png';
                    break;
                case 'gif':
                    imagegif($dstImg);
                    $ext = 'gif';
                    break;
                case 'webp':
                    if (function_exists('imagewebp')) {
                        imagewebp($dstImg, null, 80);
                        $ext = 'webp';
                        break;
                    }
                    // webp未対応環境ではPNGにフォールバック
                    imagepng($dstImg, null, 6);
                    $ext = 'png';
                    break;
            }
        } else {
            imagejpeg($dstImg, null, 90);
            $ext = 'jpg';
        }
        $encoded = ob_get_clean();

        imagedestroy($srcImg);
        imagedestroy($dstImg);

        $filename = 'avatars/' . uniqid('', true) . '.' . $ext;
        Storage::disk('public')->put($filename, (string) $encoded);

        return $filename;
    }
}

