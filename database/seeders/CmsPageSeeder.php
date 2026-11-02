<?php

namespace Database\Seeders;

use App\Models\CmsPage;
use Illuminate\Database\Seeder;

class CmsPageSeeder extends Seeder
{
    public function run(): void
    {
        foreach (['press-releases', 'faq', 'events'] as $slug) {
            $defaults = CmsPage::defaultPage($slug);
            if (! $defaults) {
                continue;
            }
            CmsPage::query()->updateOrCreate(
                ['slug' => $slug],
                collect($defaults->getAttributes())
                    ->except(['id', 'created_at', 'updated_at'])
                    ->merge(['is_published' => true])
                    ->all()
            );
        }
    }
}
