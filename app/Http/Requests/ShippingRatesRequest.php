<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ShippingRatesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'city' => ['required', 'string', 'max:120'],
            'promo_code' => ['nullable', 'string', 'max:40'],
            'discount_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'items' => ['nullable', 'array'],
            'items.*.price' => ['required_with:items', 'numeric', 'min:0'],
            'items.*.qty' => ['required_with:items', 'integer', 'min:1', 'max:99'],
        ];
    }
}
