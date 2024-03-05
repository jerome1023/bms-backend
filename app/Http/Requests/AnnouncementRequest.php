<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AnnouncementRequest extends FormRequest
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
            'what' => ['required', 'string', 'max:255'],
            'where' => ['required', 'string', 'max:255'],
            'who' => ['required', 'string', 'max:255'],
            'when' => ['required', 'date_format:Y-m-d H:i:s'],
            'details' => ['nullable', 'string', 'max:255'],
            'image' => ['nullable', 'string', 'max:255'],
            'archive_status' => ['nullable', 'string', 'max:255']
        ];
    }
}
