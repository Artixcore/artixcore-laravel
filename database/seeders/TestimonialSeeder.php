<?php

namespace Database\Seeders;

use App\Models\Testimonial;
use Illuminate\Database\Seeder;

class TestimonialSeeder extends Seeder
{
    public function run(): void
    {
        Testimonial::query()->updateOrCreate(
            ['author_name' => 'Jordan Patel', 'company' => 'Illustrative Logistics Co.'],
            [
                'role' => 'COO',
                'body' => 'Neutral testimonial stub—replace with an approved client quote after verification.',
                'rating' => 5,
                'sort_order' => 10,
                'is_published' => true,
                'featured' => true,
            ]
        );
    }
}
