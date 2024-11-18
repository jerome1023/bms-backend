<?php

namespace App\Http\Requests;

use App\Rules\SingleNameRegex;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
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
        $userId = $this->route('id') ?? $this->input('id');
        return [
            'firstname' => ['required', new SingleNameRegex, 'max:255'],
            'lastname' => ['required', new SingleNameRegex, 'max:255'],
            'address' => 'required|string',
            'email' => 'required|email|unique:users,email,' . $userId,
            'password' => 'nullable|min:6|same:confirm_password',
            'confirm_password' => 'nullable|min:6',
            'archive_status' => ['nullable', 'boolean',]
        ];
    }
}
