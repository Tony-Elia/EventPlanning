<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class UpdatePasswordRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'current_password' => 'required|current_password',
            'new_password' => ['required_with:confirm_password', 'same:confirm_password', Password::default()],
            'confirm_password' => 'required_with:new_password|same:new_password',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
