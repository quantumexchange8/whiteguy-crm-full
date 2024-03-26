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
}
