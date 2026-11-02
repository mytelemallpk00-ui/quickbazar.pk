<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CareerOpening extends Model
{
    protected $fillable = [
        'title',
        'department',
        'location',
        'employment_type',
        'description',
        'requirements',
        'apply_url',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }
}
