<?php

namespace App\Http\Requests;

use App\Services\Courier\CourierRateService;
use App\Support\CheckoutSettings;
use App\Support\PromoCodes;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'customer.email' => ['required', 'email', 'max:255'],
            'customer.first_name' => ['required', 'string', 'max:120'],
            'customer.last_name' => ['nullable', 'string', 'max:120'],
            'customer.phone' => ['nullable', 'string', 'max:40'],
            'shipping.address' => ['required', 'string', 'max:500'],
            'shipping.city' => ['required', 'string', 'max:120'],
            'shipping.postal' => ['nullable', 'string', 'max:30'],
            'shipping.country' => ['nullable', 'string', 'max:80'],
            'payment_method' => ['required', 'string', 'max:120'],
            'shipping_method_id' => ['nullable', 'string', 'max:64'],
            'promo_code' => ['nullable', 'string', 'max:40'],
            'discount_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'shipping_total' => ['nullable', 'numeric', 'min:0'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.id' => ['nullable', 'integer'],
            'items.*.name' => ['required', 'string', 'max:500'],
            'items.*.price' => ['required', 'numeric', 'min:0'],
            'items.*.qty' => ['required', 'integer', 'min:1', 'max:99'],
            'items.*.image' => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $paymentMethod = (string) $this->input('payment_method', '');
            if ($paymentMethod !== '' && ! CheckoutSettings::isPaymentMethodAllowed($paymentMethod)) {
                $validator->errors()->add('payment_method', 'Selected payment method is not available.');
            }

            $methodId = (string) ($this->input('shipping_method_id') ?? '');
            if ($methodId === '') {
                return;
            }

            $items = $this->input('items', []);
            if (! is_array($items)) {
                return;
            }

            $subtotal = collect($items)->sum(fn (array $item): float => (float) ($item['price'] ?? 0) * (int) ($item['qty'] ?? 0));
            $promo = PromoCodes::resolve($this->input('promo_code'));
            $discountPercent = $promo['percent'] ?? (float) ($this->input('discount_percent') ?? 0);
            $discounted = max(0, $subtotal - round($subtotal * ($discountPercent / 100), 2));
            $city = (string) $this->input('shipping.city', '');
            $weight = app(CourierRateService::class)->estimateWeightKg(collect($items)->sum('qty'));

            $rates = app(CourierRateService::class);
            if (! $rates->isAllowedMethod($methodId, $city, $discounted, $weight)) {
                $validator->errors()->add('shipping_method_id', 'Selected delivery option is not available for this city.');
            }
        });
    }
}
