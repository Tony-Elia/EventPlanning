<?php

namespace Modules\Service\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ServiceRequest extends FormRequest
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
            'category_id' => ["$ruleSet", 'exists:service_categories,id'],
            'name'        => ["$ruleSet", 'string', 'max:255'],
            'description' => ["$ruleSet", 'string'],
            'base_price'  => ["$ruleSet", 'numeric', 'min:0'],
            'price_unit'  => ["$ruleSet", Rule::in(['hour', 'fixed', 'person'])],
            'location'    => ["$ruleSet", 'string', 'max:255'],
            'is_active'   => ["$ruleSet", 'boolean'],

            // These are nullable/optional in both create and update
            'type'        => ['nullable', Rule::in(['event_service', 'venue'])],
            'capacity'    => ['nullable', 'numeric', 'min:0'],
            'address'     => ['nullable', 'string'],
            'latitude'    => ['nullable', 'numeric'],
            'longitude'   => ['nullable', 'numeric'],

            // Validate array structure
            'amenities'   => ['nullable', 'array'],
            'amenities.*' => ['string'], // Validate items inside array
        ];
    }
}
