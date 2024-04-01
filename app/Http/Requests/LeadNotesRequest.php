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
            // 'lead_notes.*.attachment' => 'required|string|max:200',
            'lead_notes.*.edited_at' => 'required|date_format:Y-m-d H:i:s.uO',
            'lead_notes.*.created_at' => 'required|date_format:Y-m-d H:i:s.uO',
            'lead_notes.*.created_by_id' => 'required|integer',
            'lead_notes.*.user_editable' => 'required|boolean',
        ];
    }

    public function attributes(): array
    {
        return [
            'lead_notes.*.note' => 'Note',
            'lead_notes.*.created_by_id' => 'Create By',
            'lead_notes.*.user_editable' => 'User Editable',
        ];
    }

    public function messages(): array
    {
        return [
            'lead_notes.*.note.required' => 'The note field is required.',
            'lead_notes.*.note.string' => 'The note field must be a string.',
            'lead_notes.*.created_by_id.required' => 'The created by field is required.',
            'lead_notes.*.created_by_id.integer' => 'The created by field must be an integer.',
        ];
    }
}
