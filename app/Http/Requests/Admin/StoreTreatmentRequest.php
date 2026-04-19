<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreTreatmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'treatment_category_id' => 'required|integer|exists:treatment_categories,id',
            'name'                  => 'required|string|max:50',
            'is_active'             => 'boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'treatment_category_id.required' => '請選擇療程分類',
            'treatment_category_id.exists'   => '所選分類不存在',
            'name.required'                  => '療程名稱為必填',
            'name.max'                       => '療程名稱最多 50 字',
        ];
    }
}
