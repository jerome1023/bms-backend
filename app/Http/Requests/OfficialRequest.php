<?php

namespace App\Http\Requests;

use App\Rules\SingleNameRegex;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Foundation\Http\FormRequest;

class OfficialRequest extends FormRequest
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
            'position' => 'required|string|max:255',
            'birthdate' => 'required|date_format:Y-m-d|before:today',
            'start_term' => 'required|date_format:Y-m-d',
            'end_term' => 'required|date_format:Y-m-d',
            'archive_status' => 'nullable|boolean'
        ];
    }
}
