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
                    'max:12',
                    Rule::unique('orders')->ignore($this->input('id')),
                ],
                'date' => 'required|date_format:Y-m-d',
                'action_type' => 'required|string',
                'stock_type' => 'required|string',
                'stock' => 'required|string|max:200',
                'unit_price' => 'required|decimal:0,2',
                'quantity' => 'required|decimal:0,2',
                'total_price' => 'required|decimal:0,2',
                'current_price' => 'required|decimal:0,2',
                'profit' => 'required|decimal:0,2',
                'status' => 'required|string',
                'confirmed_at' => 'nullable|date_format:Y-m-d H:i:s',
                'confirmation_name' => 'required|string|max:1000',
                'limb_stage' => 'required|string',
                'user_link' => 'required|boolean',
            ];
        } else {
            return [
                'trade_id' => 'required|string|max:12|unique:orders',
                'date' => 'required|date_format:Y-m-d',
                'action_type' => 'required|string',
                'stock_type' => 'required|string',
                'stock' => 'required|string|max:200',
                'unit_price' => 'required|decimal:0,2',
                'quantity' => 'required|decimal:0,2',
                'total_price' => 'required|decimal:0,2',
                'current_price' => 'required|decimal:0,2',
                'profit' => 'required|decimal:0,2',
                'status' => 'required|string',
                'confirmed_at' => 'nullable|date_format:Y-m-d H:i:s',
                'confirmation_name' => 'required|string|max:1000',
                'limb_stage' => 'required|string',
                'user_link' => 'required|boolean',
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
            'total_price' => 'Total Price',
            'current_price' => 'Current Price',
            'profit' => 'Profit',
            'status' => 'Status',
            'confirmed_at' => 'Confirmed At',
            'confirmation_name' => 'Confirmation Name',
            'limb_stage' => 'Limb Stage',
            'user_link' => 'User Link',
            'send_notification' => 'Send Notification',
            'notification_title' => 'Notification Title',
            'notification_description' => 'Notification Description',
        ];
    }
}
