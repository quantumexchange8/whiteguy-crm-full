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
            'sale_order_items.*.order_type' => 'required|string|max:3',
            'sale_order_items.*.product' => 'required|string|max:200',
            'sale_order_items.*.price' => 'required|max:99999999999999999999.99|decimal:0,2',
            'sale_order_items.*.exchanged_price' => 'required|max:99999999999999999999.99|decimal:0,2',
            'sale_order_items.*.quantity' => 'required|max:99999999999999999999.99|decimal:0,2',
            'sale_order_items.*.subtotal' => 'required|max:99999999999999999999.99|decimal:0,2',
            'sale_order_items.*.commission_rate' => 'required|max:99999999999999999999.99|decimal:0,2',
            'sale_order_items.*.commission' => 'required|max:99999999999999999999.99|decimal:0,2',
            'sale_order_items.*.total_exchanged_price' => 'required|max:99999999999999999999.99|decimal:0,2',
            'sale_order_items.*.total_price' => 'required|max:99999999999999999999.99|decimal:0,2',
            // 'sale_order_items.*.sale_order_id' => 'required|integer',
            'sale_order_items.*.completed_at' => 'nullable|date_format:Y-m-d H:i:s.uO',
            // 'sale_order_items.*.order_id' => 'nullable|integer',
        ];
    }

    public function attributes(): array
    {
        return [
            'sale_order_items.*.order_type' => 'Order Type',
            'sale_order_items.*.product' => 'Product',
            'sale_order_items.*.price' => 'Price',
            'sale_order_items.*.exchanged_price' => 'Exchanged Price',
            'sale_order_items.*.quantity' => 'Quantity',
            'sale_order_items.*.subtotal' => 'Subtotal',
            'sale_order_items.*.commission_rate' => 'Commission Rate',
            'sale_order_items.*.commission' => 'Commission',
            'sale_order_items.*.total_exchanged_price' => 'Total Exchanged Price',
            'sale_order_items.*.total_price' => 'Total Price',
            // 'sale_order_items.*.sale_order_id' => 'Sale Order Id',
            'sale_order_items.*.completed_at' => 'Completed At',
            // 'sale_order_items.*.order_id' => 'Order Id',
        ];
    }

    public function messages(): array
    {
        return [
            'sale_order_items.*.order_type.required' => 'This field is required',
            'sale_order_items.*.order_type.string' => 'This field must be a string.',
            'sale_order_items.*.order_type.max' => 'This field must have a maximum of 3 characters.',
            'sale_order_items.*.product.required' => 'This field is required',
            'sale_order_items.*.product.string' => 'This field must be a string.',
            'sale_order_items.*.product.max' => 'This field must have a maximum of 200 characters.',
            'sale_order_items.*.price.required' => 'This field is required',
            'sale_order_items.*.price.max' => 'This field must have a maximum of 20 digits',
            'sale_order_items.*.price.decimal' => 'This field must have only 2 decimal points.',
            'sale_order_items.*.exchanged_price.required' => 'This field is required',
            'sale_order_items.*.exchanged_price.max' => 'This field must have a maximum of 20 digits',
            'sale_order_items.*.exchanged_price.decimal' => 'This field must have only 2 decimal points.',
            'sale_order_items.*.quantity.required' => 'This field is required',
            'sale_order_items.*.quantity.max' => 'This field must have a maximum of 20 digits',
            'sale_order_items.*.quantity.decimal' => 'This field must have only 2 decimal points.',
            'sale_order_items.*.subtotal.required' => 'This field is required',
            'sale_order_items.*.subtotal.max' => 'This field must have a maximum of 20 digits',
            'sale_order_items.*.subtotal.decimal' => 'This field must have only 2 decimal points.',
            'sale_order_items.*.commission_rate.required' => 'This field is required',
            'sale_order_items.*.commission_rate.max' => 'This field must have a maximum of 20 digits',
            'sale_order_items.*.commission_rate.decimal' => 'This field must have only 2 decimal points.',
            'sale_order_items.*.commission.required' => 'This field is required',
            'sale_order_items.*.commission.max' => 'This field must have a maximum of 20 digits',
            'sale_order_items.*.commission.decimal' => 'This field must have only 2 decimal points.',
            'sale_order_items.*.total_exchanged_price.required' => 'This field is required',
            'sale_order_items.*.total_exchanged_price.max' => 'This field must have a maximum of 20 digits',
            'sale_order_items.*.total_exchanged_price.decimal' => 'This field must have only 2 decimal points.',
            'sale_order_items.*.total_price.required' => 'This field is required',
            'sale_order_items.*.total_price.max' => 'This field must have a maximum of 20 digits',
            'sale_order_items.*.total_price.decimal' => 'This field must have only 2 decimal points.',
        ];
    }
}
