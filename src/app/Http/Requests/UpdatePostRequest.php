<?php

namespace App\Http\Requests;

use Illuminate\Support\Facades\Auth;

class UpdatePostRequest extends StorePostRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    #[\Override]
    public function authorize(): bool
    {
        $post = $this->route('post');

        return $post->user_id == Auth::id();
    }

    #[\Override] public function rules(): array
    {
        $rules = parent::rules();
        unset($rules['group_id']);
        return array_merge($rules, [
            'deleted_file_ids' => 'array',
            'deleted_file_ids.*' => 'numeric',
        ]);
    }


}
