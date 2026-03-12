<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tag;
use Carbon\Carbon;

class TagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tags = [
            'skincare',
            'makeup',
            'fashion',
            'beauty',
            'electronics',
            'home',
            'sports',
            'toys',
            'books',
            'music',
            'stationery',
            'vintage',
            '90s',
            'watch',
            'bag',
            'shoes',
            'accessory',
        ];

        foreach ($tags as $name) {
            Tag::firstOrCreate(
                ['name' => $name],
                [
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ]
            );
        }
    }
}
