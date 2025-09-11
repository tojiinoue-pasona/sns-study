<?php

namespace App\Helpers;

use Illuminate\Database\Query\Builder;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;

class SqlSecurityHelper
{
    /**
     * LIKE検索用の特殊文字をエスケープ
     * 
     * @param string $value 
     * @return string
     */
    public static function escapeLike($value)
    {
        // LIKE検索で特殊な意味を持つ文字をエスケープ
        $search = ['\\', '%', '_'];
        $replace = ['\\\\', '\\%', '\\_'];
        
        return str_replace($search, $replace, $value);
    }

    /**
     * 安全なLIKE検索用のクエリビルダー拡張
     * 
     * @param Builder|EloquentBuilder $query
     * @param string $column
     * @param string $value
     * @param string $type (both|start|end)
     * @return Builder|EloquentBuilder
     */
    public static function safeLike($query, $column, $value, $type = 'both')
    {
        $escapedValue = self::escapeLike($value);
        
        switch ($type) {
            case 'start':
                $likeValue = $escapedValue . '%';
                break;
            case 'end':
                $likeValue = '%' . $escapedValue;
                break;
            case 'both':
            default:
                $likeValue = '%' . $escapedValue . '%';
                break;
        }
        
        return $query->where($column, 'LIKE', $likeValue);
    }

    /**
     * 複数のカラムに対する安全なLIKE検索（OR条件）
     * 
     * @param Builder|EloquentBuilder $query
     * @param array $columns
     * @param string $value
     * @param string $type
     * @return Builder|EloquentBuilder
     */
    public static function safeLikeMultiple($query, array $columns, $value, $type = 'both')
    {
        return $query->where(function ($subQuery) use ($columns, $value, $type) {
            foreach ($columns as $column) {
                $subQuery->orWhere(function ($q) use ($column, $value, $type) {
                    self::safeLike($q, $column, $value, $type);
                });
            }
        });
    }

    /**
     * SQL文字列の危険なパターンを検出
     * 
     * @param string $input
     * @return bool
     */
    public static function detectSqlInjection($input)
    {
        $dangerousPatterns = [
            '/(\b(SELECT|INSERT|UPDATE|DELETE|DROP|CREATE|ALTER|EXEC|EXECUTE|UNION|SCRIPT|JAVASCRIPT)\b)/i',
            '/(\-\-|\;|\*\/|\*\*)/',
            '/(\b(OR|AND)\b\s+\d+\s*=\s*\d+)/i',
            '/(\bUNION\b.*\bSELECT\b)/i',
            '/(\'\s*(OR|AND)\s*\'.*\')/i'
        ];
        
        foreach ($dangerousPatterns as $pattern) {
            if (preg_match($pattern, $input)) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * 検索入力の安全性チェック
     * 
     * @param string $input
     * @return array ['safe' => bool, 'sanitized' => string]
     */
    public static function validateSearchInput($input)
    {
        $isSafe = !self::detectSqlInjection($input);
        $sanitized = $isSafe ? $input : '';
        
        return [
            'safe' => $isSafe,
            'sanitized' => $sanitized,
            'original' => $input
        ];
    }
}
