<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BlotterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'complainant' => ['required', 'string', 'max:255'],
            'complainant_age' => ['required', 'integer'],
            'complainant_address' => ['required', 'string', 'max:255'],
            'complainant_contact_number' => ['required', 'numeric', 'integer'],
            'complainee' => ['required', 'string', 'max:255'],
            'complainee_age' => ['required', 'integer'],
            'complainee_address' => ['required', 'string', 'max:255'],
            'complainee_contact_number' => ['required', 'numeric', 'integer'],
            'date' => ['required', 'date'],
            'complain' => ['required', 'string', 'max:255'],
            'agreement' => ['required', 'string', 'max:255'],
            'official_id' => ['required', 'string', 'max:255'],
            'witness' => ['required', 'string', 'max:255'],
            'status' => ['required', 'string', 'max:255'],
            'archive_status' => ['nullable', 'boolean']
        ];
    }
}
