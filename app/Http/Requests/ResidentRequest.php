<?php

namespace App\Http\Requests;

use App\Rules\SingleNameRegex;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Foundation\Http\FormRequest;

class ResidentRequest extends FormRequest
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
            'firstname' => ['required', new SingleNameRegex, 'max:255'],
            'middlename' => ['required', new SingleNameRegex, 'max:255'],
            'lastname' => ['required', new SingleNameRegex, 'max:255'],
            'gender' => 'required|string|max:255',
            'birthdate' => 'required|date|before:today',
            'birthplace' => 'required|string|max:255',
            'civil_status' => 'required|string|max:255',
            'religion' => 'required|string|max:255',
            'educational_attainment' => 'required|string|max:255',
            'sitio' => 'required|string|max:255',
            'house_number' => 'required|string|max:255',
            'occupation' => 'required|string|max:255',
            'nationality' => 'required|regex:/^[a-zA-Z\s\-]+$/|max:255',
            'voter_status' => 'required|string|max:255',
            'archive_status' => 'nullable|boolean'
        ];
    }

    public function messages(): array
    {
        return [
            'nationality.regex' => 'The nationality may only contain letters, spaces or hyphens',
        ];
    }
}
