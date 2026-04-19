<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreJobTitleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:50|unique:job_titles,name',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => '職稱名稱為必填',
            'name.unique'   => '此職稱已存在',
            'name.max'      => '職稱名稱最多 50 字',
        ];
    }
}
