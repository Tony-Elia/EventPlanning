<?php

namespace Modules\Service\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PackageItemRequest extends FormRequest
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
     */
    public function rules(): array
    {
        // Check if the request is an update (PUT or PATCH)
        $isUpdate = $this->isMethod('put') || $this->isMethod('patch');

        // For updates, fields are 'sometimes' required (allows partial updates).
        // For creation (POST), fields are strictly 'required'.
        $ruleSet = $isUpdate ? 'sometimes' : 'required';

        return [
            'service_id' => $ruleSet . '|exists:services,id',
            'quantity' => 'nullable|integer|min:1',
        ];
    }
}
