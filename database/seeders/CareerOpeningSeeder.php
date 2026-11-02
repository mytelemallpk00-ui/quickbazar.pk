<?php

namespace Database\Seeders;

use App\Models\CareerOpening;
use Illuminate\Database\Seeder;

class CareerOpeningSeeder extends Seeder
{
    public function run(): void
    {
        $jobs = [
            [
                'title' => 'Senior E-commerce Manager',
                'department' => 'Digital Retail',
                'location' => 'Karachi / Hybrid',
                'employment_type' => 'Full-time',
                'description' => '<p>Lead omnichannel growth, conversion optimization, and flagship campaign launches.</p>',
                'requirements' => '<ul><li>5+ years in beauty or luxury e-commerce</li><li>Strong analytics and team leadership</li></ul>',
                'apply_url' => 'mailto:careers@beautage.com.pk',
                'sort_order' => 1,
            ],
            [
                'title' => 'Skincare Formulation Specialist',
                'department' => 'Product R&D',
                'location' => 'Lahore',
                'employment_type' => 'Full-time',
                'description' => '<p>Develop botanical formulations and oversee clinical testing partnerships.</p>',
                'requirements' => '<ul><li>Degree in cosmetic science or related field</li><li>Portfolio of shipped SKUs</li></ul>',
                'apply_url' => 'mailto:careers@beautage.com.pk',
                'sort_order' => 2,
            ],
        ];

        foreach ($jobs as $job) {
            CareerOpening::query()->updateOrCreate(
                ['title' => $job['title']],
                array_merge(['is_active' => true], $job),
            );
        }
    }
}
