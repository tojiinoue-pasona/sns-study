<?php

namespace App\Helpers;

class SecurityHelper
{
    /**
     * 安全にHTMLをエスケープし、改行を<br>タグに変換
     * 
     * @param string $text
     * @return string
     */
    public static function safeBr($text)
    {
        return nl2br(e($text));
    }

    /**
     * HTMLタグを完全に除去（ストリップ）
     * 
     * @param string $text
     * @return string
     */
    public static function stripTags($text)
    {
        return strip_tags($text);
    }

    /**
     * 安全なHTMLタグのみを許可
     * 
     * @param string $text
     * @param array $allowedTags
     * @return string
     */
    public static function safeTags($text, $allowedTags = ['<b>', '<i>', '<em>', '<strong>'])
    {
        return strip_tags($text, implode('', $allowedTags));
    }

    /**
     * 長いテキストを安全に切り詰める
     * 
     * @param string $text
     * @param int $limit
     * @param string $end
     * @return string
     */
    public static function safeLimit($text, $limit = 200, $end = '...')
    {
        return \Illuminate\Support\Str::limit(e($text), $limit, $end);
    }

    /**
     * URLが安全かチェック
     * 
     * @param string $url
     * @return bool
     */
    public static function isSafeUrl($url)
    {
        // javascript:, data:, vbscript: などの危険なスキームをブロック
        $dangerousSchemes = [
            'javascript:', 
            'data:', 
            'vbscript:', 
            'blob:', 
            'file:'
        ];
        
        $lowercaseUrl = strtolower($url);
        
        foreach ($dangerousSchemes as $scheme) {
            if (strpos($lowercaseUrl, $scheme) === 0) {
                return false;
            }
        }
        
        return true;
    }

    /**
     * 安全なリンクを生成
     * 
     * @param string $url
     * @param string $text
     * @param array $attributes
     * @return string
     */
    public static function safeLink($url, $text, $attributes = [])
    {
        if (!self::isSafeUrl($url)) {
            return e($text); // 危険なURLの場合はテキストのみ返す
        }
        
        $attributeString = '';
        foreach ($attributes as $key => $value) {
            $attributeString .= ' ' . e($key) . '="' . e($value) . '"';
        }
        
        return '<a href="' . e($url) . '"' . $attributeString . '>' . e($text) . '</a>';
    }

    /**
     * Content Security Policy用のnonce生成
     * 
     * @return string
     */
    public static function generateNonce()
    {
        return base64_encode(random_bytes(16));
    }
}
