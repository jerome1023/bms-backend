<?php

namespace App\Http\Requests;

use App\Rules\FullnameRegex;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Foundation\Http\FormRequest;

class BlotterRequest extends FormRequest
{
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'status' => false,
            'status_code' => 400,
            'message' => 'Validation error',
            'errors' => $validator->errors()->toArray()
        ], 400));
    }
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'complainant' => ['required', new FullnameRegex, 'max:255'],
            'complainant_age' => ['required', 'numeric'],
            'complainant_address' => ['required', 'string', 'max:255'],
            'complainant_contact_number' => ['required', 'regex:/^09\d{2}-\d{3}-\d{4}$/'],
            'complainee' => ['required', new FullnameRegex, 'max:255'],
            'complainee_age' => ['required', 'numeric'],
            'complainee_address' => ['required', 'string', 'max:255'],
            'complainee_contact_number' => ['required', 'regex:/^09\d{2}-\d{3}-\d{4}$/'],
            'date' => ['required', 'date', 'date_format:Y-m-d', 'before_or_equal:today'],
            'complain' => ['required', 'string', 'max:255'],
            'agreement' => ['nullable', 'string', 'max:255'],
            'namagitan' => ['nullable',  'string', 'max:255'],
            'witness' => ['nullable', new FullnameRegex, 'max:255'],
            'status' => ['nullable', 'string', 'max:255'],
            'archive_status' => ['nullable', 'boolean']
        ];
    }
}
