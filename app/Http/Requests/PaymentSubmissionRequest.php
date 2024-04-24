<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PaymentSubmissionRequest extends FormRequest
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
            'date' => 'required|date_format:Y-m-d',
            'amount' => 'required|max:999999999999999999.99|decimal:0,2',
            'converted_amount' => 'nullable|max:999999999999999999.99|decimal:0,2',
            'user_memo' => 'required|string',
            'admin_memo' => 'required|string',
            'admin_remark' => 'required|string',
            'status' => 'required|integer',
            'approved_at' => 'nullable|date_format:Y-m-d H:i:s.uO',
            'payment_method_id' => 'required|integer',
            'user_id' => 'required|integer',
        ];
    }

    public function attributes(): array
    {
        return [
            'date' => 'Date',
            'amount' => 'Amount',
            'converted_amount' => 'Converted Amount',
            'user_memo' => 'User Memo',
            'admin_memo' => 'Admin Memo',
            'admin_remark' => 'Admin Remark',
            'status' => 'Status',
            'approved_at' => 'Approved At',
            'payment_method_id' => 'Payment Method',
            'user_id' => 'User',
        ];
    }

    public function messages(): array
    {
        return [
            'date.required' => 'The date field is required.',
            'amount.required' => 'The amount field is required.',
            'amount.max' => 'The amount field must not exceed 20 digits.',
            'amount.decimal' => 'The amount field must be of number with 2 decimal points.',
            'converted_amount.required' => 'The converted amount field is required.',
            'converted_amount.max' => 'The converted amount field must not exceed 20 digits.',
            'converted_amount.decimal' => 'The converted amount field must be of number with 2 decimal points.',
            'user_memo.required' => 'The user memo field is required.',
            'user_memo.string' => 'The user memo field must be a string.',
            'admin_memo.required' => 'The admin memo field is required.',
            'admin_memo.string' => 'The admin memo field must be a string.',
            'admin_remark.required' => 'The admin remark field is required.',
            'admin_remark.string' => 'The admin remark field must be a string.',
            'status.required' => 'The status is required.',
            'status.integer' => 'The status must be an integer.',
            'approved_at.required' => 'The price field is required.',
            'payment_method_id.required' => 'The payment method is required.',
            'payment_method_id.integer' => 'The payment method must be an integer.',
        ];
    }
}
