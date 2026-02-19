<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UploadMediaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Autorização será feita no controller
    }

    public function rules(): array
    {
        return [
            'files' => ['required', 'array'],
            'files.*' => ['file', 'mimes:jpg,jpeg,png,gif,webp,mp4,mov,avi,webm', 'max:10240'],
        ];
    }
}
