<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PostUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // 認証導入後は権限チェックに変更
    }

    public function rules(): array
    {
        return [
            'body' => ['sometimes', 'required', 'string', 'max:2000'],
            'visibility_id' => ['sometimes', 'required', 'integer', 'exists:visibilities,id'],
            'tags' => ['sometimes', 'array'],
            'tags.*' => ['integer', 'exists:tags,id'],
            'image' => ['sometimes', 'nullable', 'image', 'mimes:jpeg,png', 'max:2048'],
        ];
    }

    public function attributes(): array
    {
        return [
            'body' => '本文',
            'visibility_id' => '公開範囲',
            'tags' => 'タグ',
        ];
    }
}