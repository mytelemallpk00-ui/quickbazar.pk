<?php

namespace Database\Seeders;

use App\Models\BlogPost;
use Illuminate\Database\Seeder;

class BlogPostSeeder extends Seeder
{
    public function run(): void
    {
        BlogPost::query()->updateOrCreate(
            ['slug' => '7-spectrum-led-therapy-routine'],
            [
                'title' => 'The Science of 7-Spectrum LED Light Therapy',
                'category' => 'Beauty Tech',
                'read_time' => '6 min read',
                'published_at' => '2026-10-20',
                'image' => 'https://images.unsplash.com/photo-1598440947619-2c35fc9aa908?auto=format&fit=crop&q=80&w=1000',
                'author' => 'Dr. Aliza Thorne',
                'author_role' => 'Chief Dermatological Researcher',
                'author_avatar' => 'Dr',
                'likes' => 342,
                'snippet' => 'Explore photon wavelength technology for collagen synthesis and blemish control.',
                'content' => '<p>LED therapy utilizes multiple spectrums for targeted skin rejuvenation.</p>',
                'is_featured' => true,
            ],
        );
    }
}
