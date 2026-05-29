<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateJobTitleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('job_title');

        return [
            'name'           => "required|string|max:50|unique:job_titles,name,{$id}",
            'treatment_role' => 'nullable|in:doctor,nurse,consultant',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'     => '職稱名稱為必填',
            'name.unique'       => '此職稱已存在',
            'name.max'          => '職稱名稱最多 50 字',
            'treatment_role.in' => '療程角色必須為 doctor、nurse 或 consultant',
        ];
    }
}
