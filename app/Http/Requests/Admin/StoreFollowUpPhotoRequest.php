<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreFollowUpPhotoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'photos'   => 'required|array|min:1',
            'photos.*' => 'image|max:4096',
            'category' => 'required|in:before,after,recovery',
        ];
    }

    public function messages(): array
    {
        return [
            'photos.required'   => '請選擇至少一張照片',
            'photos.*.image'    => '所有檔案必須為圖片格式',
            'photos.*.max'      => '每張圖片大小不得超過4MB',
            'category.required' => '請選擇照片類別',
            'category.in'       => '照片類別無效',
        ];
    }
}
