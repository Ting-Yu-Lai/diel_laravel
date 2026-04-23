<?php

namespace App\Http\Requests\Member;

use Illuminate\Foundation\Http\FormRequest;

class StoreMemberRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'full_name' => 'required|string|max:100',
            'email'     => 'required|email|max:100|unique:members,email',
            'phone'     => 'required|string|max:20|unique:members,phone',
            'password'  => ['required', 'string', 'min:8', 'confirmed',
                            'regex:/[a-z]/', 'regex:/[A-Z]/',
                            'regex:/[0-9]/', 'regex:/[@$!%*#?&^_\-]/'],
        ];
    }

    public function messages(): array
    {
        return [
            'full_name.required' => '姓名為必填',
            'email.required'     => 'Email 為必填',
            'email.unique'       => '此 Email 已被使用',
            'phone.required'     => '手機號碼為必填',
            'phone.unique'       => '此手機號碼已被使用',
            'password.required'  => '密碼為必填',
            'password.min'       => '密碼至少 8 個字元',
            'password.confirmed' => '兩次密碼不一致',
            'password.regex'     => '密碼須包含大寫字母、小寫字母、數字及特殊符號（@$!%*#?&^_-）',
        ];
    }
}
