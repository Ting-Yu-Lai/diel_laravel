<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTagRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'            => 'required|string|max:50',
            'tag_category_id' => 'required|exists:tag_categories,id',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'            => '標籤名稱為必填',
            'name.max'                 => '標籤名稱最多 50 字',
            'tag_category_id.required' => '請選擇所屬分類',
            'tag_category_id.exists'   => '所選分類不存在',
        ];
    }
}
