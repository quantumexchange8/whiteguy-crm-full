<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LeadFrontRequest extends FormRequest
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
            'lead_front_name' => 'required|string|max:200',
            'lead_front_mimo' => 'required|string',
            'lead_front_product' => 'required|string|max:200',
            'lead_front_quantity' => 'required|max_digits:20|decimal:0,2',
            'lead_front_price' => 'required|max_digits:20|decimal:0,2',
            'lead_front_vc' => 'required|string|max:200',
            'lead_front_sdm' => 'required|boolean',
            'lead_front_liquid' => 'required|boolean',
            'lead_front_bank' => 'required|string',
            'lead_front_note' => 'required|string',
            'lead_front_commission' => 'required|max_digits:20|decimal:0,2',
            'lead_front_edited_at' => 'required|date_format:Y-m-d H:i:s.uO',
            'lead_front_created_at' => 'required|date_format:Y-m-d H:i:s.uO',
            'lead_front_email' => 'required|string|max:254',
            'lead_front_phone_number' => 'required|string|max:50',
        ];
    }

    public function attributes(): array
    {
        return [
            'lead_front_name' => 'Lead Front Name',
            'lead_front_mimo' => 'Mimo',
            'lead_front_product' => 'Product',
            'lead_front_quantity' => 'Quantity',
            'lead_front_price' => 'Price',
            'lead_front_vc' => 'Lead Front VC',
            'lead_front_sdm' => 'Sdm',
            'lead_front_liquid' => 'Liquid',
            'lead_front_bank' => 'Bank',
            'lead_front_note' => 'Note',
            'lead_front_commission' => 'Commission',
            'lead_front_edited_at' => 'Edited At',
            'lead_front_created_at' => 'Created At',
            'lead_front_email' => 'Email',
            'lead_front_phone_number' => 'Phone Number',
        ];
    }

    public function messages(): array
    {
        return [
            'lead_front_name.required' => 'The name field is required.',
            'lead_front_name.string' => 'The name field must be a string.',
            'lead_front_name.max' => 'The name field must not exceed 200 characters.',
            'lead_front_mimo.required' => 'The mimo field is required.',
            'lead_front_mimo.string' => 'The mimo field must be a string.',
            'lead_front_product.required' => 'The product field is required.',
            'lead_front_product.string' => 'The product field must be a string.',
            'lead_front_product.max' => 'The product field must not exceed 200 characters.',
            'lead_front_quantity.required' => 'The quantity field is required.',
            'lead_front_quantity.max_digits' => 'The quantity field must not exceed 20 digits.',
            'lead_front_quantity.decimal' => 'The quantity field must be of number with 2 decimal points.',
            'lead_front_price.required' => 'The price field is required.',
            'lead_front_price.max_digits' => 'The price field must not exceed 20 digits.',
            'lead_front_price.decimal' => 'The price field must be of number with 2 decimal points.',
            'lead_front_vc.required' => 'The vc field is required.',
            'lead_front_vc.string' => 'The vc field must be a string.',
            'lead_front_vc.max' => 'The vc field must not exceed 200 characters.',
            'lead_front_sdm.required' => 'The sdm field is required.',
            'lead_front_sdm.boolean' => 'The sdm field must be a boolean.',
            'lead_front_liquid.required' => 'The liquid field is required.',
            'lead_front_bank.required' => 'The bank field is required.',
            'lead_front_bank.string' => 'The bank field must be a string.',
            'lead_front_note.required' => 'The note field is required.',
            'lead_front_note.string' => 'The note field must be a string.',
            'lead_front_commission.required' => 'The commission field is required.',
            'lead_front_commission.max_digits' => 'The commission field must not exceed 20 digits.',
            'lead_front_commission.decimal' => 'The commission field must be of number with 2 decimal points.',
            'lead_front_email.required' => 'The email field is required.',
            'lead_front_email.string' => 'The email field must be a string.',
            'lead_front_email.max' => 'The email field must not exceed 254 characters.',
            'lead_front_phone_number.required' => 'The phone number field is required.',
            'lead_front_phone_number.string' => 'The phone number field must be a string.',
            'lead_front_phone_number.max' => 'The phone number field must not exceed 50 characters.',
        ];
    }
}
