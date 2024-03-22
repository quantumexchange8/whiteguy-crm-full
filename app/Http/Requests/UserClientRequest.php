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
                'last_login' => 'nullable|date_format:Y-m-d H:i:s.uP',
                'is_superuser' => 'required|boolean',
                'first_name' => 'required|string|max:150',
                'last_name' => 'required|string|max:150',
                'is_staff' => 'required|boolean',
                'is_active' => 'required|boolean',
                'date_joined' => 'required|date_format:Y-m-d H:i:s.uP',
                'username' => [
                    'required',
                    'string',
                    'max:150',
                    Rule::unique('core_user')->ignore($this->input('id')),
                ],
                'full_name' => 'required|string|max:200',
                'email' => [
                    'required',
                    'string',
                    'email:rfc,dns',
                    'max:254',
                    Rule::unique('core_user')->ignore($this->input('id')),
                ],
                'phone_number' => [
                    'required',
                    'string',
                    'max:50',
                    Rule::unique('core_user')->ignore($this->input('id')),
                ],
                'profile_picture' => 'required|string|max:200',
                'is_email_verified' => 'required|boolean',
                'timezone' => 'required|string|max:32',
                'country' => 'required|string|max:2',
                'address' => 'required|string',
                'account_type' => 'required|integer',
                'account_holder' => 'required|string|max:200',
                'customer_type' => 'required|string|max:200',
                'rank' => 'required|integer',
                'remark' => 'required|string',
                'wallet_balance' => 'required|decimal:0,2|max:20',
                'account_manager_id' => 'nullable|integer',
                'site_id' => 'required|integer',
                'has_crm_access' => 'required|boolean',
                'lead_status' => 'required|string|max:200',
                'client_stage' => 'nullable|integer',
                'has_leads_access' => 'required|boolean',
                'identification_document_1' => 'required|string|max:200',
                'identification_document_2' => 'required|string|max:200',
                'identification_document_3' => 'required|string|max:200',
                'kyc_status' => 'nullable|integer',
                'proof_of_address_document_1' => 'required|string|max:200',
                'proof_of_address_document_2' => 'required|string|max:200',
                'account_id' => [
                    'required',
                    'string',
                    'max:200',
                    Rule::unique('core_user')->ignore($this->input('id')),
                ],
                'previous_broker_name' => 'required|string|max:250',
            ];
        } else {
            return [
                'password' => [
                    'required', 
                    Password::min(8)->mixedCase()->numbers()->symbols()->uncompromised(),
                    'confirmed',
                ],
                'last_login' => 'nullable|date_format:Y-m-d H:i:s.uO',
                'is_superuser' => 'required|boolean',
                'first_name' => 'required|string|max:150',
                'last_name' => 'required|string|max:150',
                'is_staff' => 'required|boolean',
                'is_active' => 'required|boolean',
                'date_joined' => 'required|date_format:Y-m-d H:i:s.uO',
                'username' => 'required|string|max:150',
                'full_name' => 'required|string|max:200',
                'email' => 'required|string|email:rfc,dns|max:254|unique:core_user',
                'phone_number' => 'required|string||max:50|unique:core_user',
                // 'profile_picture' => 'required|string|max:200',
                'is_email_verified' => 'required|boolean',
                // 'timezone' => 'required|string|max:32',
                'country' => 'required|string|max:2',
                // 'address' => 'required|string',
                'account_type' => 'required|integer',
                'account_holder' => 'required|string|max:200',
                'customer_type' => 'required|string|max:200',
                'rank' => 'required|integer',
                // 'remark' => 'required|string',
                // 'wallet_balance' => 'required|decimal:0,2|max:20',
                'account_manager_id' => 'nullable|integer',
                'site_id' => 'required|integer',
                'has_crm_access' => 'required|boolean',
                'lead_status' => 'required|string|max:200',
                'client_stage' => 'nullable|integer',
                'has_leads_access' => 'required|boolean',
                // 'identification_document_1' => 'required|string|max:200',
                // 'identification_document_2' => 'required|string|max:200',
                // 'identification_document_3' => 'required|string|max:200',
                'kyc_status' => 'nullable|integer',
                // 'proof_of_address_document_1' => 'required|string|max:200',
                // 'proof_of_address_document_2' => 'required|string|max:200',
                'account_id' => 'required|string|max:200|unique:core_user',
                'previous_broker_name' => 'required|string|max:250',
            ];
        }
    }

    public function attributes(): array
    {
        return [
            'password' => 'Password',
            'last_login' => 'Last Login',
            'is_superuser' => 'Superuser status',
            'first_name' => 'First Name',
            'last_name' => 'Last Name',
            'is_staff' => 'Staff status',
            'is_active' => 'Active',
            'date_joined' => 'Date Joined',
            'username' => 'Username',
            'full_name' => 'Full Name',
            'email' => 'Email',
            'phone_number' => 'Phone Number',
            'profile_picture' => 'Profile Pitcure',
            'is_email_verified' => 'Is Email Verified',
            'timezone' => 'Timezone',
            'country' => 'Country of Citizenship',
            'address' => 'Address',
            'account_type' => 'Account Type',
            'account_holder' => 'Account Holder',
            'customer_type' => 'Customer Type',
            'rank' => 'Rank',
            'remark' => 'Remark',
            'wallet_balance' => 'Wallet Balance',
            'account_manager_id' => 'Account Manager',
            'site_id' => 'Site',
            'has_crm_access' => 'CRM Access',
            'lead_status' => 'Lead Status',
            'client_stage' => 'Client Stage',
            'has_leads_access' => 'Lead Management Access',
            'identification_document_1' => 'Identification Document 1',
            'identification_document_2' => 'Identification Document 2',
            'identification_document_3' => 'Identification Document 3',
            'kyc_status' => 'KYC Status',
            'proof_of_address_document_1' => 'Proof of Address Document 1',
            'proof_of_address_document_2' => 'Proof of Address Document 2',
            'account_id' => 'Account ID',
            'previous_broker_name' => 'Previous Broker Name',
        ];
    }
}
