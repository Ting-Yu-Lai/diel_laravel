<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreTagRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:50',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => '標籤名稱為必填',
            'name.max'      => '標籤名稱最多 50 字',
        ];
    }
}
