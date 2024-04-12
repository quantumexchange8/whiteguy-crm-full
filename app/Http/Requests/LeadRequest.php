<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

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
        $rules = [
            'date' => 'required|string|max:250', //date_format:Y-m-d H:i:s
            'first_name' => 'required|string|max:250',
            'last_name' => 'required|string|max:250',
            'country' => 'required|string|max:250',
            'date_of_birth' => 'required|string|max:250', //date_format:Y-m-d
            'occupation' => 'required|string|max:250', 
            'vc' => 'required|string|max:250',
            'sdm' => 'required|string|max:250',
            // 'attachment' => 'required|string|max:250', 
            'private_remark' => 'required|string', 
            'remark' => 'required|string', 
            'data_source' => 'required|string|max:250',
            // 'appointment_start_at' => 'nullable|date_format:Y-m-d H:i:s.uO',
            // 'appointment_end_at' => 'nullable|date_format:Y-m-d H:i:s.uO',
            // 'contacted_at' => 'nullable|date_format:Y-m-d H:i:s.uO',
            // 'assignee_read_at' => 'nullable|date_format:Y-m-d H:i:s.uO',
            'edited_at' => 'required|date_format:Y-m-d H:i:s.uO',
            'created_at' => 'required|date_format:Y-m-d H:i:s.uO',
            'appointment_label_id' => 'nullable|integer',
            'assignee_id' => 'nullable|integer', 
            'contact_outcome_id' => 'nullable|integer', 
            'created_by_id' => 'required|integer', 
            'stage_id' => 'nullable|integer', 
            // 'give_up_at' => 'nullable|date_format:Y-m-d H:i:s.uO',
            'account_manager' => 'required|string|max:250', 
            'address' => 'required|string', 
            'agents_book' => 'required|string|max:250', 
            'campaign_product' => 'required|string|max:250', 
            'data_code' => 'required|string|max:250',
            'data_type' => 'required|string|max:250',
            // 'deleted_at' => 'nullable|date_format:Y-m-d H:i:s.uO',
            // 'deleted_note' => 'required|string|max:250',
            // 'sort_id' => 'required',
        ];

        // Conditional rules for email and phone number fields
        $conditionalFields = [
            'email',
            'phone_number',
            // 'email_alt_1',
            // 'phone_number_alt_1',
            // 'email_alt_2',
            // 'phone_number_alt_2',
            // 'email_alt_3',
            // 'phone_number_alt_3',
        ];

        foreach ($conditionalFields as $field) {
            if ($this->input('id')) {
                $rules[$field] = [
                    'required',
                    ($field === 'email' || Str::contains($field, 'email')) ? 'email:rfc,dns' : 'string',
                    ($field === 'email' || Str::contains($field, 'email')) ? 'max:254' : 'max:250',
                    Rule::unique('core_lead')->ignore($this->input('id')),
                ];
            } else {
                $rules[$field] = 'required|' . (($field === 'email' || Str::contains($field, 'email')) ? 'email:rfc,dns|max:254' : 'string|max:250') . '|unique:core_lead';
            }
        }

        // Conditional rules for nullable dates fields
        $nullableDatesConditionalRules = [
            'appointment_start_at',
            'appointment_end_at',
            'contacted_at',
            'assignee_read_at',
            'give_up_at',
            'deleted_at',
        ];
    
        foreach ($nullableDatesConditionalRules as $field) {
            $rules[$field] = $this->input($field)
                ? 'nullable|date_format:Y-m-d H:i:s.uO'
                : 'nullable';
        }

        return $rules;
    }

    public function attributes(): array
    {
        return [
            'date' => 'Date Opp&rsquo;d In',
            'first_name' => 'First Name',
            'last_name' => 'Last Name',
            'country' => 'Country',
            'date_of_birth' => 'DOB',
            'occupation' => 'Occupation', 
            'vc' => 'VC',
            'sdm' => 'Sdm', 
            'email' => 'Email',
            'email_alt_1' => 'Additional Email 1', 
            'email_alt_2' => 'Additional Email 2', 
            'email_alt_3' => 'Additional Email 3', 
            'phone_number' => 'Phone Number',
            'phone_number_alt_1' => 'Additional Phone Number 1',
            'phone_number_alt_2' => 'Additional Phone Number 2',
            'phone_number_alt_3' => 'Additional Phone Number 3',
            'attachment' => 'Attachment', 
            'private_remark' => 'Private Remark', 
            'remark' => 'Remark', 
            'data_source' => 'Data Source',
            'appointment_start_at' => 'Appointment Start',
            'appointment_end_at' => 'Appointment End',
            'contacted_at' => 'Last Called',
            'assignee_read_at' => 'Assignee Read At',
            'appointment_label_id' => 'Appointment Label', 
            'assignee_id' => 'Assignee', 
            'contact_outcome_id' => 'Contact Outcome', 
            'created_by_id' => 'Created By', 
            'stage_id' => 'Stage', 
            'give_up_at' => 'Give Up?',
            'account_manager' => 'Account Manager', 
            'address' => 'Address', 
            'agents_book' => 'Agents Book', 
            'campaign_product' => 'Campaign Product', 
            'data_code' => 'Data Code',
            'data_type' => 'Data Type',
            'deleted_at' => 'Delete At',
            'deleted_note' => 'Deleted Note',
            'sort_id' => 'Sort ID',
        ];
    }
}
