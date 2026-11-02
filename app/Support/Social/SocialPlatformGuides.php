<?php

namespace App\Support\Social;

class SocialPlatformGuides
{
    /** @return array<string, string> */
    public static function all(): array
    {
        return [
            'facebook' => 'Facebook Page se Page ID aur long-lived Page Access Token copy karein (Meta Developer → Graph API).',
            'instagram' => 'Instagram Business account ID + same Meta token jo Facebook Page se linked ho.',
            'linkedin' => 'LinkedIn Developer app se access token aur company Organization URN (urn:li:organization:…).',
            'youtube' => 'Google Cloud project → YouTube Data API → OAuth refresh token. Video post ke liye scheduler par video upload zaroori hai.',
            'telegram' => '@BotFather se bot token banayein, phir channel/chat ID (@channelusername ya numeric ID).',
        ];
    }

    public static function for(string $platform): string
    {
        $fromCatalog = SocialChannelCatalog::guideFor($platform);
        if ($fromCatalog !== '') {
            return $fromCatalog;
        }

        return static::all()[$platform] ?? '';
    }
}
