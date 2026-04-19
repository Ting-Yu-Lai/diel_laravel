<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreStaffRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'job_title_id' => 'required|exists:job_titles,id',
            'name'         => 'required|string|max:50',
            'gender'       => 'nullable|in:M,F,other',
            'phone'        => 'required|string|max:20|unique:staff,phone',
            'email'        => 'nullable|email|max:100',
            'hire_date'    => 'nullable|date',
            'is_active'    => 'boolean',
            'notes'        => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'job_title_id.required' => '請選擇職稱',
            'job_title_id.exists'   => '所選職稱不存在',
            'name.required'         => '姓名為必填',
            'phone.required'        => '手機為必填',
            'phone.unique'          => '此手機號碼已存在',
            'email.email'           => 'Email 格式不正確',
        ];
    }
}
