<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Foundation\Http\FormRequest;

class AnnouncementRequest extends FormRequest
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
            'what' => ['required', 'string', 'max:255'],
            'where' => ['required', 'string', 'max:255'],
            'who' => ['required', 'string', 'max:255'],
            'when' => ['required', 'date', 'date_format:Y-m-d H:i'],
            'details' => ['nullable', 'string', 'max:255'],
            'image' => ['nullable', 'string'],
            'archive_status' => ['nullable', 'boolean']
        ];
    }
}
