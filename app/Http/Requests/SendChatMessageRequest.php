<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SendChatMessageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'session_id' => ['nullable', 'string', 'max:64'],
            'customer_name' => ['nullable', 'string', 'max:120'],
            'customer_phone' => ['nullable', 'string', 'max:40'],
            'message' => ['required', 'string', 'max:2000'],
            'client_message_id' => ['nullable', 'string', 'max:64'],
        ];
    }
}
