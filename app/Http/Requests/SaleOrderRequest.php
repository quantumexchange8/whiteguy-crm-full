<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SaleOrderRequest extends FormRequest
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
            'written' => 'required|date_format:Y-m-d',
            'vc' => 'required|string|max:200',
            'room_number' => 'required|string|max:200',
            'allo' => 'required|string|max:200',
            'allo_name' => 'required|string|max:200',
            'sm_number' => 'required|string|max:200',
            'gm_number' => 'required|string|max:200',
            'fm_number' => 'required|string|max:200',
            'lm_number' => 'required|string|max:200',
            'ao_1' => 'required|string|max:200',
            'ao_1_name' => 'required|string|max:200',
            'ao_2' => 'required|string|max:200',
            'ao_2_name' => 'required|string|max:200',
            'bonus_comment' => 'required|string',
            'currency_pair' => 'required|string|max:6',
            'exchange_rate' => 'nullable|max:99999999999999999999.99|decimal:0,2',
            'registered_name' => 'required|string|max:200',
            'contact_name' => 'required|string|max:200',
            'office_number_1' => 'required|string|max:200',
            'office_number_2' => 'required|string|max:200',
            'home_number' => 'required|string|max:200',
            'mobile_number' => 'required|string|max:200',
            'fax_number' => 'required|string|max:200',
            'date_of_birth' => 'required|date_format:Y-m-d',
            'email' => 'required|string|max:254',
            'address_1' => 'required|string',
            'address_2' => 'required|string',
            'city' => 'required|string|max:200',
            'country' => 'required|string|max:200',
            'exit_time_frame' => 'required|string|max:200',
            'sell_price' => 'required|string|max:200',
            'allocation_officer' => 'required|string|max:200',
            'trade_plus' => 'required|string|max:200',
            'settlement_date' => 'required|date_format:Y-m-d',
            'factory' => 'required|string|max:200',
            'pay_via' => 'required|string|max:200',
            'allo_comment' => 'required|string',
            'docs_received' => 'required|string|max:200',
            'tc_sent' => 'required|date_format:Y-m-d',
            'tt_received' => 'required|date_format:Y-m-d',
            'balance_due' => 'required|max:99999999999999999999.99|decimal:0,2',
            'exchanged_balance_due' => 'required|max:99999999999999999999.99|decimal:0,2',
            'site_id' => 'required|integer',
            'se_name' => 'required|string|max:200',
            'se_number' => 'required|string|max:200',
            'created_by_id' => 'required|integer',
        ];
    }

    public function attributes(): array
    {
        return [
            'written' => 'Written',
            'vc' => 'Vc',
            'room_number' => 'Room Number',
            'allo' => 'Allo',
            'allo_name' => 'Allo Name',
            'sm_number' => 'Sm Number',
            'gm_number' => 'Gm Number',
            'fm_number' => 'Fm Number',
            'lm_number' => 'Lm Number',
            'ao_1' => 'Ao 1',
            'ao_1_name' => 'Ao 1 Name',
            'ao_2' => 'Ao 2',
            'ao_2_name' => 'Ao 2 Name',
            'bonus_comment' => 'Bonus Comment',
            'currency_pair' => 'Currency Pair',
            'exchange_rate' => 'Exchange Rate',
            'registered_name' => 'Registered Name',
            'contact_name' => 'Contact Name',
            'office_number_1' => 'Office Number 1',
            'office_number_2' => 'Office Number 2',
            'home_number' => 'Home Number',
            'mobile_number' => 'Mobile Number',
            'fax_number' => 'Fax Number',
            'date_of_birth' => 'Date of Birth',
            'email' => 'Email',
            'address_1' => 'Address 1',
            'address_2' => 'Address 2',
            'city' => 'City',
            'country' => 'Country',
            'exit_time_frame' => 'Exit Time Frame',
            'sell_price' => 'Sell Price',
            'allocation_officer' => 'Allocation Officer',
            'trade_plus' => 'Trade Plus',
            'settlement_date' => 'Settlement Date',
            'factory' => 'Factory',
            'pay_via' => 'Pay Via',
            'allo_comment' => 'Allo Comment',
            'docs_received' => 'Docs Received',
            'tc_sent' => 'Tc Sent',
            'tt_received' => 'Tt Received',
            'balance_due' => 'Balance Due',
            'exchanged_balance_due' => 'Exchanged Balance Due',
            'site_id' => 'Site Id',
            'se_name' => 'Se Name',
            'se_number' => 'Se Number',
            'created_by_id' => 'Created By Id',
        ];
    }
}
