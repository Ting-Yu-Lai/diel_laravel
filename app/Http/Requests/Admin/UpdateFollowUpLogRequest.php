<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateFollowUpLogRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'day_number' => 'required|integer|min:1',
            'content'    => 'required|string|max:5000',
        ];
    }

    public function messages(): array
    {
        return [
            'day_number.required' => '請填寫第幾天',
            'day_number.integer'  => '天數必須為整數',
            'day_number.min'      => '天數至少為第1天',
            'content.required'    => '請填寫追蹤內容',
            'content.max'         => '追蹤內容不得超過5000字',
        ];
    }
}
