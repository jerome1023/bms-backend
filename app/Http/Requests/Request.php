<?php

namespace App\Http\Requests;

use App\Rules\FullnameRegex;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Foundation\Http\FormRequest;

class Request extends FormRequest
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
            'fullname' => ['required', new FullnameRegex, 'max:255'],
            'age' => 'required|numeric',
            'document' => 'required|string|max:255',
            'purpose' => 'required|string|max:255',
            'sitio' => 'required|string|max:255',
            'income' => 'nullable|integer',
            'status' => 'nullable|string|max:255',
            'archive_status' => 'nullable|boolean'
        ];
    }
}
