<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TransactionRequest extends FormRequest
{
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
            'fullname' => ['nullable', 'string', 'max:255' ],
            'user_id' => ['nullable', 'string', 'max:255' ],
            'document_id' => ['required', 'string', 'max:255' ],
            'purpose' => ['required', 'string', 'max:255' ],
            'price' => ['required', 'integer'],
            'archive_status' => ['nullable', 'string', 'max:255' ]
        ];
    }
}
