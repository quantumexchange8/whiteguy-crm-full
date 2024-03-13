<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class UserClientRequest extends FormRequest
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
                'site' => 'required|string',
                'username' => [
                    'required',
                    'string',
                    Rule::unique('users_clients')->ignore($this->input('id')),
                ],
                // 'password' => [
                //     'required', 
                //     Password::min(8)->mixedCase()->numbers()->symbols()->uncompromised(),
                // ],
                'account_id' => [
                    'required',
                    'string',
                    'max:12',
                    Rule::unique('users_clients')->ignore($this->input('id')),
                ],
                'first_name' => 'required|string',
                'last_name' => 'nullable|string',
                'full_legal_name' => 'required|string',
                'email' => [
                    'required',
                    'string',
                    'email:rfc,dns',
                    'max:255',
                    Rule::unique('users_clients')->ignore($this->input('id')),
                ],
                'phone_number' => [
                    'nullable',
                    'integer',
                    Rule::unique('users_clients')->ignore($this->input('id')),
                ],
                'address' => 'nullable|string|max:1000',
                'country_of_citizenship' => 'nullable|string',
                'account_holder' => 'required|string',
                'account_type' => 'required|string',
                'customer_type' => 'nullable|string',
                'account_manager' => 'nullable|string',
                'lead_status' => 'nullable|string',
                'client_stage' => 'nullable|string',
                'rank' => 'required|string',
                'remark' => 'nullable|string|max:1000',
                'previous_broker_name' => 'nullable|string',
                'kyc_status' => 'nullable|string',
                'is_active' => 'required|boolean',
                'has_crm_access' => 'required|boolean',
                'has_leads_access' => 'required|boolean',
                'is_staff' => 'required|boolean',
                'is_superuser' => 'required|boolean',
                'last_login' => 'required|date_format:Y-m-d H:i:s',
            ];
        } else {
            return [
                'site' => 'required|string',
                'username' => 'required|string|unique:users_clients',
                'password' => [
                    'required', 
                    Password::min(8)->mixedCase()->numbers()->symbols()->uncompromised(),
                    'confirmed',
                ],
                'account_id' => 'required|string|max:12|unique:users_clients',
                'first_name' => 'required|string',
                'last_name' => 'nullable|string',
                'full_legal_name' => 'required|string',
                'email' => 'required|string|email:rfc,dns|max:255|unique:users_clients',
                'phone_number' => 'nullable|integer|unique:users_clients',
                'address' => 'nullable|string|max:1000',
                'country_of_citizenship' => 'nullable|string',
                'account_holder' => 'required|string',
                'account_type' => 'required|string',
                'customer_type' => 'nullable|string',
                'account_manager' => 'nullable|string',
                'lead_status' => 'nullable|string',
                'client_stage' => 'nullable|string',
                'rank' => 'required|string',
                'remark' => 'nullable|string|max:1000',
                'previous_broker_name' => 'nullable|string',
                'kyc_status' => 'nullable|string',
                'is_active' => 'required|boolean',
                'has_crm_access' => 'required|boolean',
                'has_leads_access' => 'required|boolean',
                'is_staff' => 'required|boolean',
                'is_superuser' => 'required|boolean',
                'last_login' => 'required|date_format:Y-m-d H:i:s',
            ];
        }
    }

    public function attributes(): array
    {
        return [
            'site' => 'Site',
            'username' => 'Username',
            'password' => 'Password',
            'account_id' => 'Account ID',
            'first_name' => 'First Name',
            'last_name' => 'Last Name',
            'full_legal_name' => 'Full Legal Name',
            'email' => 'Email',
            'phone_number' => 'Phone Number',
            'address' => 'Address',
            'country_of_citizenship' => 'Country of Citizenship',
            'account_holder' => 'Account Holder',
            'account_type' => 'Account Type',
            'customer_type' => 'Customer Type',
            'account_manager' => 'Account Manager',
            'lead_status' => 'Lead Status',
            'client_stage' => 'Client Stage',
            'rank' => 'Rank',
            'remark' => 'Remark',
            'previous_broker_name' => 'Previous Broker Name',
            'kyc_status' => 'KYC Status',
            'is_active' => 'Active',
            'has_crm_access' => 'CRM Access',
            'has_leads_access' => 'Lead Management Access',
            'is_staff' => 'Staff status',
            'is_superuser' => 'Superuser status',
            'last_login' => 'Last Login',
        ];
    }
}
