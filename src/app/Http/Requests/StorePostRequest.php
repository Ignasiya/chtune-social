<?php

namespace App\Http\Requests;

use App\Http\Enums\GroupUserStatus;
use App\Models\GroupUser;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\File;

class StorePostRequest extends FormRequest
{
    public static array $extensions = [
        'jpg', 'jpeg', 'png', 'gif', 'webp', 'svg',
        'mp3', 'wav', 'mp4',
        'doc', 'docx', 'pdf', 'csv', 'xls', 'xlsx',
        'zip'
    ];

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
            'preview' => ['nullable', 'array'],
            'preview_url' => ['nullable', 'string'],
            'attachments' => [
                'array',
                'max:50',
                function ($attribute, $value, Closure $fail) {
                    $totalSize = collect($value)->sum(function ($file) {
                        if ($file instanceof UploadedFile) {
                            return $file->getSize();
                        }
                        return 0;
                    });

                    if ($totalSize > 500 * 1024 * 1024) {
                        $fail('Максимальный размер всех вложений 500MB.');
                    }
                }
            ],
            'attachments.*' => [
                'file',
                File::types(StorePostRequest::$extensions),
            ],
            'user_id' => ['numeric'],
            'group_id' => [
                'nullable',
                'exists:groups,id',
                function ($attribute, $value, Closure $fail) {

                    $groupUser = GroupUser::where('user_id', Auth::id())
                        ->where('group_id', $value)
                        ->where('status', GroupUserStatus::APPROVED->value)
                        ->exists();

                    if (!$groupUser) {
                        $fail('У Вас нет доступа на создание записей в группе');
                    }
                }]
        ];
    }

    protected function prepareForValidation(): void
    {
        $body = $this->input('body') ?: '';
        $previewUrl = $this->input('preview_url') ?: '';

        $trimmedBody = trim(strip_tags($body));
        if ($trimmedBody === $previewUrl) {
            $body = '';
        }

        $this->merge([
            'user_id' => auth()->user()->id,
            'body' => $body
        ]);
    }

    public function messages(): array
    {
        return [
            'attachments.*.file' => 'Каждое вложение должно быть файлом.',
            'attachments.*.mimes' => 'Недопустимый тип файла для вложений.',
        ];
    }
}
