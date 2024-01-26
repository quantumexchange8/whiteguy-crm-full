<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LeadNotesRequest extends FormRequest
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
            'lead_notes.*.note' => 'required|string',
            'lead_notes.*.user_editable' => 'required|boolean',
            'lead_notes.*.created_by' => 'required|string',
        ];
    }

    public function attributes(): array
    {
        return [
            'lead_notes.*.note' => 'Note',
            'lead_notes.*.user_editable' => 'User Editable',
            'lead_notes.*.created_by' => 'Create By',
        ];
    }

    public function messages(): array
    {
        return [
            'lead_notes.*.note.required' => 'The Note field is required.',
            'lead_notes.*.created_by.required' => 'The Created By field is required.',
        ];
    }
}
