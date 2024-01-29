<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class LeadRequest extends FormRequest
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
                'first_name' => 'required|string|max:150',
                'last_name' => 'string|nullable|max:150',
                'country' => 'string|nullable|max:100',
                'address' => 'string|nullable|max:200', 
                'date_oppd_in' => 'date_format:Y-m-d H:i:s|nullable',
                'campaign_product' => 'string|nullable|max:100', 
                'sdm' => 'string|nullable|max:100', 
                'date_of_birth' => 'date_format:Y-m-d|nullable',
                'occupation' => 'string|nullable|max:150', 
                'agents_book' => 'string|nullable|max:150', 
                'account_manager' => 'string|nullable', 
                'vc' => 'required|string|max:100',
                'data_type' => 'string|nullable|max:100',
                'data_source' => 'string|nullable|max:100',
                'data_code' => 'string|nullable|max:100',
                'email' => [
                    'required',
                    'email:rfc,dns',
                    Rule::unique('leads')->ignore($this->input('id')),
                ],
                'email_alt_1' => [
                    'nullable',
                    'email:rfc,dns',
                    Rule::unique('leads')->ignore($this->input('id')),
                ],
                'email_alt_2' => [
                    'nullable',
                    'email:rfc,dns',
                    Rule::unique('leads')->ignore($this->input('id')),
                ], 
                'email_alt_3' => [
                    'nullable',
                    'email:rfc,dns',
                    Rule::unique('leads')->ignore($this->input('id')),
                ],
                'phone_number' => [
                    'required',
                    'integer',
                    Rule::unique('leads')->ignore($this->input('id')),
                ],
                'phone_number_alt_1' => [
                    'nullable',
                    'integer',
                    Rule::unique('leads')->ignore($this->input('id')),
                ],
                'phone_number_alt_2' => [
                    'nullable',
                    'integer',
                    Rule::unique('leads')->ignore($this->input('id')),
                ],
                'phone_number_alt_3' => [
                    'nullable',
                    'integer',
                    Rule::unique('leads')->ignore($this->input('id')),
                ],
                'private_remark' => 'string|nullable|max:1000', 
                'remark' => 'string|nullable|max:1000', 
                'appointment_start_at' => 'date_format:Y-m-d H:i:s|nullable',
                'appointment_end_at' => 'date_format:Y-m-d H:i:s|nullable',
                'last_called' => 'required|date_format:Y-m-d H:i:s',
                'assignee_read_at' => 'date_format:Y-m-d H:i:s|nullable',
                'give_up_at' => 'date_format:Y-m-d H:i:s|nullable',
                'appointment_label' => 'string|nullable', 
                'contact_outcome' => 'string|nullable', 
                'stage' => 'string|nullable', 
                'assignee' => 'required|string', 
                'created_by' => 'string|nullable', 
                'delete_at' => 'date_format:Y-m-d H:i:s|nullable',
            ];
        } else {
            return [
                'first_name' => 'required|string|max:150',
                'last_name' => 'string|nullable|max:150',
                'country' => 'string|nullable|max:100',
                'address' => 'string|nullable|max:200', 
                'date_oppd_in' => 'date_format:Y-m-d H:i:s|nullable',
                'campaign_product' => 'string|nullable|max:100', 
                'sdm' => 'string|nullable|max:100', 
                'date_of_birth' => 'date_format:Y-m-d|nullable',
                'occupation' => 'string|nullable|max:150', 
                'agents_book' => 'string|nullable|max:150', 
                'account_manager' => 'string|nullable', 
                'vc' => 'required|string|max:100',
                'data_type' => 'string|nullable|max:100',
                'data_source' => 'string|nullable|max:100',
                'data_code' => 'string|nullable|max:100',
                'email' => 'required|email:rfc,dns|unique:leads',
                'email_alt_1' => 'nullable|email|unique:leads', 
                'email_alt_2' => 'nullable|email|unique:leads', 
                'email_alt_3' => 'nullable|email|unique:leads', 
                'phone_number' => 'required|integer|unique:leads',
                'phone_number_alt_1' => 'integer|nullable|unique:leads',
                'phone_number_alt_2' => 'integer|nullable|unique:leads',
                'phone_number_alt_3' => 'integer|nullable|unique:leads',
                'private_remark' => 'string|nullable|max:1000', 
                'remark' => 'string|nullable|max:1000', 
                'appointment_start_at' => 'date_format:Y-m-d H:i:s|nullable',
                'appointment_end_at' => 'date_format:Y-m-d H:i:s|nullable',
                'last_called' => 'required|date_format:Y-m-d H:i:s',
                'assignee_read_at' => 'date_format:Y-m-d H:i:s|nullable',
                'give_up_at' => 'date_format:Y-m-d H:i:s|nullable',
                'appointment_label' => 'string|nullable', 
                'contact_outcome' => 'string|nullable', 
                'stage' => 'string|nullable', 
                'assignee' => 'required|string', 
                'created_by' => 'string|nullable', 
                'delete_at' => 'date_format:Y-m-d H:i:s|nullable',
            ];
        }
    }

    public function attributes(): array
    {
        return [
            'first_name' => 'First Name',
            'last_name' => 'Last Name',
            'country' => 'Country',
            'address' => 'Address', 
            'date_oppd_in' => 'Date Opp&rsquo;d In',
            'campaign_product' => 'Campaign Product', 
            'sdm' => 'Sdm', 
            'date_of_birth' => 'DOB',
            'occupation' => 'Occupation', 
            'agents_book' => 'Agents Book', 
            'account_manager' => 'Account Manager', 
            'vc' => 'VC',
            'data_type' => 'Data Type',
            'data_source' => 'Data Source',
            'data_code' => 'Data Code',
            'email' => 'Email',
            'email_alt_1' => 'Additional Email 1', 
            'email_alt_2' => 'Additional Email 2', 
            'email_alt_3' => 'Additional Email 3', 
            'phone_number' => 'Phone Number',
            'phone_number_alt_1' => 'Additional Phone Number 1',
            'phone_number_alt_2' => 'Additional Phone Number 2',
            'phone_number_alt_3' => 'Additional Phone Number 3',
            'private_remark' => 'Private Remark', 
            'remark' => 'Remark', 
            'appointment_start_at' => 'Appointment Start',
            'appointment_end_at' => 'Appointment End',
            'last_called' => 'Last Called',
            'assignee_read_at' => 'Assignee Read At',
            'give_up_at' => 'Give Up?',
            'appointment_label' => 'Appointment Label', 
            'contact_outcome' => 'Contact Outcome', 
            'stage' => 'Stage', 
            'assignee' => 'Assignee', 
            'created_by' => 'Created By', 
            'delete_at' => 'Delete At',
        ];
    }
}
