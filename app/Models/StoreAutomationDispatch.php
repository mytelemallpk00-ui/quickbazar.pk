<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StoreAutomationDispatch extends Model
{
    protected $fillable = [
        'automation_type',
        'reference_key',
        'step_index',
        'recipient',
        'subject',
        'sent_at',
    ];

    protected function casts(): array
    {
        return [
            'sent_at' => 'datetime',
        ];
    }

    public static function wasSent(string $type, string $referenceKey, int $stepIndex = 0): bool
    {
        return static::query()
            ->where('automation_type', $type)
            ->where('reference_key', $referenceKey)
            ->where('step_index', $stepIndex)
            ->exists();
    }

    public static function record(string $type, string $referenceKey, int $stepIndex, string $recipient, string $subject): void
    {
        static::query()->create([
            'automation_type' => $type,
            'reference_key' => $referenceKey,
            'step_index' => $stepIndex,
            'recipient' => $recipient,
            'subject' => $subject,
            'sent_at' => now(),
        ]);
    }
}
