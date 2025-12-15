<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'string'],
            'email' => ['sometimes', 'email', 'max:254', 'unique:users,email,' . $this->user()->id],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
