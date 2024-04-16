<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class NotificationRequest extends FormRequest
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
            'title' => 'required|string|max:250',
            'description' => 'required|string',
            'notification_type' => 'required|string|max:20',
            // 'attachment' => 'required|string|max:200',
            'is_read' => 'required|boolean',
            'edited_at' => 'required|date_format:Y-m-d H:i:s.uO',
            'created_at' => 'required|date_format:Y-m-d H:i:s.uO',
            'user_id' => 'required|integer',
        ];
    }

    public function attributes(): array
    {
        return [
            'title' => 'Title',
            'description' => 'Description',
            'notification_type' => 'Notification Type',
            'attachment' => 'Attachment',
            'is_read' => 'User Viewed',
            'user_id' => 'User',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'The title field is required.',
            'title.string' => 'The title field must be a string.',
            'title.max' => 'The title field must not exceed 250 characters.',
            'description.required' => 'The description field is required.',
            'description.string' => 'The description field must be a string.',
            'notification_type.required' => 'The notification type field is required.',
            'notification_type.string' => 'The notification type field must be a string.',
            'notification_type.max' => 'The notification type field must not exceed 20 characters.',
            'attachment.required' => 'The attachment type field is required.',
            'attachment.string' => 'The attachment type field must be a string.',
            'attachment.max' => 'The attachment type field must not exceed 200 characters.',
            'is_read.required' => 'The user viewed field is required.',
            'is_read.boolean' => 'The user viewed field must be a boolean.',
            'user_id.required' => 'The user field is required.',
            'user_id.integer' => 'The user field must be an integer.',
        ];
    }
}
