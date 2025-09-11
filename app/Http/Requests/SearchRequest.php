<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SearchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'q'   => ['nullable', 'string', 'max:200'],
            'tag' => ['nullable', 'integer', 'exists:tags,id'],
        ];
    }

    public function attributes(): array
    {
        return [
            'q' => 'キーワード',
            'tag' => 'タグ',
        ];
    }
}