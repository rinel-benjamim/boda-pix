<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UploadMediaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('uploadMedia', $this->route('event'));
    }

    public function rules(): array
    {
        return [
            'files' => ['required', 'array'],
            'files.*' => ['file', 'mimes:jpg,jpeg,png,gif,mp4,mov,avi', 'max:102400'],
        ];
    }
}
