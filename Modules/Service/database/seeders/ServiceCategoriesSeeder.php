<?php

namespace Modules\Service\Database\Seeders;

use Carbon\Carbon;
use DB;
use Illuminate\Database\Seeder;
use Str;

class ServiceCategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Venues',
                'description' => 'Indoor and outdoor locations for weddings, conferences, and parties, including hotels, halls, and gardens.',
            ],
            [
                'name' => 'Catering & Food',
                'description' => 'Professional food and beverage services, including buffet styling, custom cakes, and bartending.',
            ],
            [
                'name' => 'Photography & Videography',
                'description' => 'Capture your special moments with professional photo shoots, drone coverage, and cinematic video editing.',
            ],
            [
                'name' => 'Entertainment & Music',
                'description' => 'DJs, live bands, solo musicians, magicians, and performance artists to keep your guests entertained.',
            ],
            [
                'name' => 'Decoration & Styling',
                'description' => 'Event styling services including floral arrangements, lighting setup, furniture rental, and thematic decor.',
            ],
        ];

        // Prepare data for insertion
        $data = array_map(function ($category) {
            return [
                'name'        => $category['name'],
                'slug'        => Str::slug($category['name']), // Auto-generate slug (e.g., 'catering-food')
                'description' => $category['description'],
                'created_at'  => Carbon::now(),
                'updated_at'  => Carbon::now(),
            ];
        }, $categories);

        // Insert into database
        DB::table('service_categories')->insert($data);
    }
}
