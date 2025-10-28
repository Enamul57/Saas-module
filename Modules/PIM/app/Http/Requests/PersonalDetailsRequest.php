<?php

namespace Modules\PIM\App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class PersonalDetailsRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules()
    {
        return [
            'first_name'       => ['required', 'string', 'max:255'],
            'middle_name'      => ['nullable', 'string', 'max:255'],
            'last_name'        => ['required', 'string', 'max:255'],
            'dob'              => ['required', 'date', 'before:today'],
            'gender'           => ['required', Rule::in(['Male', 'Female'])],
            'marital_status'   => ['required', Rule::in(['Single', 'Married', 'Divorced', 'Widowed'])],
            'nationality'      => ['required', 'string', 'max:255'],
            'religion'         => ['nullable', 'string', 'max:255'],
            'blood_group'      => ['nullable', 'string', 'max:3'], // e.g., O+, AB-
            'national_id'      => ['required', 'string', 'max:20', 'unique:personal_details,national_id'],
            'passport_number'  => ['nullable', 'string', 'max:50'],
            'signature'        => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'], // max 2MB
            'activity_log'     => ['required', 'string'],
            'img'              => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:4096'], // max 4MB
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
