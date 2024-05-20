<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PaymentMethodRequest extends FormRequest
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
            'title' => 'required|string|max:200',
            'description' => 'required|string',
            'bank_name' => 'required|string|max:200',
            'account_name' => 'required|string|max:200',
            'account_number' => 'required|string|max:200',
            'logo' => 'required',
        ];
    }

    public function attributes(): array
    {
        return [
            'title' => 'Title',
            'description' => 'Description',
            'bank_name' => 'Bank Name',
            'account_name' => 'Account Name',
            'account_number' => 'Account Number',
            'logo' => 'Logo',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'The title field is required.',
            'title.string' => 'The title field must be a string.',
            'title.max' => 'The title field must not exceed 200 digits.',
            'description.required' => 'The description field is required.',
            'description.string' => 'The description field must be a string.',
            'bank_name.required' => 'The bank_name field is required.',
            'bank_name.string' => 'The bank_name field must be a string.',
            'bank_name.max' => 'The bank_name field must not exceed 200 digits.',
            'account_name.required' => 'The account_name field is required.',
            'account_name.string' => 'The account_name field must be a string.',
            'account_name.max' => 'The account_name field must not exceed 200 digits.',
            'account_number.required' => 'The account_number field is required.',
            'account_number.string' => 'The account_number field must be a string.',
            'account_number.max' => 'The account_number field must not exceed 200 digits.',
            'logo.required' => 'The logo field is required.',
        ];
    }
}
