<?php

namespace Modules\PIM\App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ContactRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'address'       => ['nullable', 'string', 'max:255'],
            'division'      => ['nullable', 'string', 'max:100'],
            'city'          => ['nullable', 'string', 'max:100'],
            'zip'           => ['nullable', 'string', 'max:20'],
            'mobile_home'   => ['nullable', 'string', 'max:20'],
            'mobile_work'   => ['nullable', 'string', 'max:20'],
            'work_email'    => ['nullable', 'email', 'max:255'],
            'other_email'   => ['nullable', 'email', 'max:255'],
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }
}
