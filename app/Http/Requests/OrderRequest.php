<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class OrderRequest extends FormRequest
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
        if ($this->input('id')) {
            return [
                'trade_id' => [
                    'required',
                    'string',
                    'max:200',
                    Rule::unique('core_order')->ignore($this->input('id')),
                ],
                'date' => 'required|date_format:Y-m-d',
                'action_type' => 'required|string|max:4',
                'stock_type' => 'required|string|max:20',
                'stock' => 'required|string|max:200',
                'unit_price' => 'required|decimal:0,2|max_digits:20',
                'quantity' => 'required|integer',
                'current_unit_price' => 'required|decimal:0,2|max_digits:20',
                'profit' => 'required|decimal:0,2|max_digits:20',
                'status' => 'required|integer',
                'user_id' => 'required|integer',
                'is_deleted' => 'required|boolean',
                'limb_stage' => 'nullable|integer',
                'confirmation_name' => 'required|string|max:250',
                'confirmed_at' => 'nullable|date_format:Y-m-d H:i:s.uO',
            ];
        } else {
            return [
                'trade_id' => 'required|string|max:200|unique:core_order',
                'date' => 'required|date_format:Y-m-d',
                'action_type' => 'required|string|max:4',
                'stock_type' => 'required|string|max:20',
                'stock' => 'required|string|max:200',
                'unit_price' => 'required|decimal:0,2|max_digits:20',
                'quantity' => 'required|integer',
                'current_unit_price' => 'required|decimal:0,2|max_digits:20',
                'profit' => 'required|decimal:0,2|max_digits:20',
                'status' => 'required|integer',
                'user_id' => 'required|integer',
                'is_deleted' => 'required|boolean',
                'limb_stage' => 'nullable|integer',
                'confirmation_name' => 'required|string|max:250',
                'confirmed_at' => 'nullable|date_format:Y-m-d H:i:s.uO',
            ];
        }
    }

    public function attributes(): array
    {
        return [
            'trade_id' => 'Trade Id',
            'date' => 'Date',
            'action_type' => 'Action Type',
            'stock_type' => 'Stock Type',
            'stock' => 'Stock',
            'unit_price' => 'Unit Price',
            'quantity' => 'Quantity',
            'current_unit_price' => 'Current Price',
            'profit' => 'Profit',
            'status' => 'Status',
            'user_id' => 'User Link',
            'limb_stage' => 'Limb Stage',
            'confirmation_name' => 'Confirmation Name',
            'confirmed_at' => 'Confirmed At',
        ];
    }
}
