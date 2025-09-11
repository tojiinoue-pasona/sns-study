<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Visibility extends Model
{
    public $timestamps = false;
    protected $fillable = ['code'];

    // 可視性コードの定数
    public const DRAFT = 'draft';
    public const FOLLOWERS = 'followers';
    public const PUBLIC = 'public';

    /**
     * 表示用ラベルを返す（自分のみ/フォロワー/全ての人）
     */
    public function getLabelAttribute(): string
    {
        return match ($this->code) {
            self::DRAFT => '自分のみ',
            self::FOLLOWERS => 'フォロワー',
            default => '全ての人',
        };
    }

    /**
     * 表示用アイコンのクラス名を返す（Font Awesome）
     */
    public function getIconClassAttribute(): string
    {
        return match ($this->code) {
            self::DRAFT => 'fas fa-lock',
            self::FOLLOWERS => 'fas fa-user-friends',
            default => 'fas fa-globe',
        };
    }
}
