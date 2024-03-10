<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\File;

class StorePostRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'body' => ['nullable', 'string'],
            'attachments' => 'array|max:20',
            'attachments.*' => [
                'file',
                File::types(['jpg', 'jpeg', 'png', 'gif', 'webp',
                    'mp3', 'wav', 'mp4',
                    'doc', 'docx', 'pdf', 'csv', 'xls', 'xlsx',
                    'zip'
                ])->max(500 * 1024 * 1024)
            ],
            'user_id' => ['numeric']
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'user_id' => auth()->user()->id,
            'body' => $this->input('body') ?: ''
        ]);
    }
}
