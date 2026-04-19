<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreTagCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:50|unique:tag_categories,name',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => '分類名稱為必填',
            'name.unique'   => '此分類名稱已存在',
            'name.max'      => '分類名稱最多 50 字',
        ];
    }
}
