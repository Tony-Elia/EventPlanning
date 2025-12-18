<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class UpdateProfileRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'string'],
            'email' => ['sometimes', 'email', 'max:254', 'unique:users,email,' . $this->user()->id],
            'current_password' => ['required_with:password', 'current_password'],
            'password' => ['required_with:current_password', 'same:confirm_password', Password::default()],
            'confirm_password' => ['required_with:password', 'same:password'],
            'avatar' => ['image', 'sometimes', 'max:2048'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
