<?php

namespace Modules\PIM\App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EmployeeStore extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $tenantId = session('tenant_id');

        return [
            'img' => 'nullable|image|max:2048',
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',

            // Employee ID must be unique per tenant
            'employee_id' => [
                'required',
                'string',
                Rule::unique('employees', 'employee_id')
                    ->where(fn($q) => $q->where('tenant_id', $tenantId)),
            ],

            'showCredentials' => 'boolean',

            // Email validation only if credentials are created
            'email' => [
                'required',
                'email',
                'unique:employees,email',
            ],

            // Password rules only if login credentials enabled
            'password' => [
                'nullable',
                'required_if:showCredentials,true',
                'confirmed',
                'min:8',
            ],
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }
    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        throw new \Illuminate\Validation\ValidationException($validator, response()->json([
            'success' => false,
            'errors' => $validator->errors()
        ], 422));
    }
}
