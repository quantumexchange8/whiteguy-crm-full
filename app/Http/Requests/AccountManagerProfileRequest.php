<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AccountManagerProfileRequest extends FormRequest
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
            'file' => 'required|',
            'user_id' => 'required|integer',
        ];
    }

    public function attributes(): array
    {
        return [
            'file' => 'File',
            'user_id' => 'User',
        ];
    }

    public function messages(): array
    {
        return [
            'file.required' => 'The file field is required.',
            'user_id.required' => 'The user field is required.',
        ];
    }
}
