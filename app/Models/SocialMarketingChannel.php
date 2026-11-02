<?php

namespace App\Models;

use App\Support\Social\SocialChannelCatalog;
use Illuminate\Database\Eloquent\Model;

class SocialMarketingChannel extends Model
{
    protected static function booted(): void
    {
        static::saved(fn () => SocialChannelCatalog::flushCache());
        static::deleted(fn () => SocialChannelCatalog::flushCache());
    }

    protected $fillable = [
        'key',
        'label',
        'guide',
        'credential_fields',
        'sort_order',
        'is_enabled',
        'supports_scheduling',
    ];

    protected function casts(): array
    {
        return [
            'credential_fields' => 'array',
            'is_enabled' => 'boolean',
            'supports_scheduling' => 'boolean',
        ];
    }

    /** @return list<array{key: string, label: string, secret: bool, required?: bool}> */
    public function normalizedCredentialFields(): array
    {
        $fields = $this->credential_fields ?? [];
        if (! is_array($fields)) {
            return [];
        }

        $out = [];
        foreach ($fields as $field) {
            if (! is_array($field) || empty($field['key']) || empty($field['label'])) {
                continue;
            }
            $out[] = [
                'key' => (string) $field['key'],
                'label' => (string) $field['label'],
                'secret' => (bool) ($field['secret'] ?? true),
                'required' => (bool) ($field['required'] ?? true),
            ];
        }

        return $out;
    }
}
