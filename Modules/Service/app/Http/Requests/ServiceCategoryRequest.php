<?php

namespace Modules\Service\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ServiceCategoryRequest extends FormRequest
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
        $isUpdate = $this->isMethod('put') || $this->isMethod('patch');
        $ruleSet = $isUpdate ? 'sometimes' : 'required';

        // Get the ID of the category being updated from the route.
        // Assuming your route is like /service-categories/{id}
        $categoryId = $this->route('id');

        return [
            'name' => [
                $ruleSet,
                'string',
                'max:255',
                // Fix: Ignore the current ID if we are updating
                Rule::unique('service_categories', 'name')->ignore($categoryId),
            ],
            'description' => [
                'nullable',
                'string'
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.unique' => 'A category with this name already exists.',
        ];
    }
}
