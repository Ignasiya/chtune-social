<?php

namespace App\Http\Requests;

use App\Models\Group;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

/**
 * @property mixed $about
 */
class UpdateGroupRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        /* @var Group $group */
        $group = $this->route('group');

        return $group->isAdmin(Auth::id());
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'max:255'],
            'auto_approval' => ['required', 'boolean'],
            'about' => ['nullable']
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'about' => nl2br($this->about)
        ]);
    }
}
