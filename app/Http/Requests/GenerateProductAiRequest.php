<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GenerateProductAiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:500'],
            'target_keyword' => ['nullable', 'string', 'max:80'],
            'specs' => ['nullable', 'string', 'max:12000'],
            'category' => ['nullable', 'string', 'max:160'],
            'provider' => ['nullable', 'string', 'in:smart,openai,gemini,gemini_flash,gemini_pro,auto'],
        ];
    }
}
