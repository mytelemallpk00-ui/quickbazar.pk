<?php

namespace App\Http\Requests\Admin;

use App\Models\GoogleIndexLog;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GoogleIndexPublishRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->is_admin === true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'url' => ['nullable', 'string', 'max:2048', 'required_without:product_id'],
            'product_id' => ['nullable', 'integer', 'exists:products,id', 'required_without:url'],
            'action' => ['nullable', 'string', Rule::in([
                GoogleIndexLog::ACTION_UPDATED,
                GoogleIndexLog::ACTION_DELETED,
            ])],
        ];
    }
}
