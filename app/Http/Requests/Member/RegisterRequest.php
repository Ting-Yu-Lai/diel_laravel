<?php

namespace App\Http\Requests\Member;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'username'  => 'required|string|max:50|unique:members,username',
            'email'     => 'required|email|max:100|unique:members,email',
            'password'  => 'required|string|min:6|confirmed',
            'full_name' => 'nullable|string|max:100',
            'phone'     => 'nullable|string|max:20',
            'address'   => 'nullable|string|max:255',
        ];
    }
}
