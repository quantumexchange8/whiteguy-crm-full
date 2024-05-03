<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SaleOrderItemRequest extends FormRequest
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
            'order_type' => 'required|string|max:3',
            'product' => 'required|string|max:200',
            'price' => 'required|max:99999999999999999999.99|decimal:0,2',
            'exchanged_price' => 'required|max:99999999999999999999.99|decimal:0,2',
            'quantity' => 'required|max:99999999999999999999.99|decimal:0,2',
            'subtotal' => 'required|max:99999999999999999999.99|decimal:0,2',
            'commission_rate' => 'required|max:99999999999999999999.99|decimal:0,2',
            'commission' => 'required|max:99999999999999999999.99|decimal:0,2',
            'total_exchanged_price' => 'required|max:99999999999999999999.99|decimal:0,2',
            'total_price' => 'required|max:99999999999999999999.99|decimal:0,2',
            'sale_order_id' => 'required|integer',
            'completed_at' => 'nullable|date_format:Y-m-d H:i:s.uO',
            'order_id' => 'nullable|integer',
        ];
    }

    public function attributes(): array
    {
        return [
            'order_type' => 'Order Type',
            'product' => 'Product',
            'price' => 'Price',
            'exchanged_price' => 'Exchanged Price',
            'quantity' => 'Quantity',
            'subtotal' => 'Subtotal',
            'commission_rate' => 'Commission Rate',
            'commission' => 'Commission',
            'total_exchanged_price' => 'Total Exchanged Price',
            'total_price' => 'Total Price',
            'sale_order_id' => 'Sale Order Id',
            'completed_at' => 'Completed At',
            'order_id' => 'Order Id',
        ];
    }
}
