<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductImportBatch extends Model
{
    public const SOURCE_DARAZ = 'daraz';

    public const SOURCE_AMAZON = 'amazon';

    public const SOURCE_SHOPIFY = 'shopify';

    public const SOURCE_WALMART = 'walmart';

    /** @return array<string, string> */
    public static function sourceLabels(): array
    {
        return [
            self::SOURCE_DARAZ => 'Daraz.pk',
            self::SOURCE_AMAZON => 'Amazon.com',
            self::SOURCE_SHOPIFY => 'Shopify store',
            self::SOURCE_WALMART => 'Walmart',
        ];
    }

    protected $fillable = [
        'user_id',
        'source',
        'mode',
        'file_name',
        'total_rows',
        'created_count',
        'updated_count',
        'failed_count',
        'failures',
    ];

    protected function casts(): array
    {
        return [
            'failures' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
