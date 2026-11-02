<?php

namespace App\Support;

class LiveChatSettings
{
    public static function isEnabled(): bool
    {
        return filter_var(SiteSettings::get('live_chat_enabled', true), FILTER_VALIDATE_BOOLEAN);
    }

    public static function welcomeMessage(): string
    {
        $msg = trim((string) SiteSettings::get('live_chat_welcome', ''));

        return $msg !== '' ? $msg : "Salam! Jawab live database se:\n• Order/tracking # → status\n• Product naam → price & stock\n• Delivery / payment\nBaqi: WhatsApp";
    }

    public static function whatsappE164(): string
    {
        $configured = preg_replace('/\D+/', '', (string) SiteSettings::get('chat_whatsapp_number', '')) ?? '';
        if ($configured !== '') {
            return $configured;
        }

        $support = preg_replace('/\D+/', '', (string) SiteSettings::get('support_phone', '')) ?? '';

        return $support !== '' ? $support : '923001234567';
    }

    public static function whatsappUrl(?string $prefill = null): string
    {
        $number = static::whatsappE164();
        $url = 'https://wa.me/'.$number;
        if (filled($prefill)) {
            $url .= '?text='.rawurlencode($prefill);
        }

        return $url;
    }

    public static function whatsappPrefill(): string
    {
        return trim((string) SiteSettings::get('chat_whatsapp_prefill', 'Hi Beautage, I need help with my order.'));
    }
}
