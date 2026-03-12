<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Tag;
class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
   public function run(): void
{

    $products = [
        [
            'name'         => 'SKIN1004 Madagascar Centella Ampoule',
            'description'  => 'Apply skin',
            'price'        => 364.61 ,
            'stock_number' => 10,
            'image'        => 'https://encrypted-tbn2.gstatic.com/shopping?q=tbn:ANd9GcS9JwF-XL1RCMWWq7anWKQjn6XrTFD0raUF-3H0dFoSs5SlrRTWIYVBRNJb2PZ088u2sWJS5KfdFASlpUrCiiGAgtI7VGGJlAPhJy9e56W5e262f0lXCP03fw',
            'tags'         => ['skincare', 'beauty'],
        ],
        [
            'name'         => 'Juicy Pout Lip Gloss Grapefruit',
            'description'  => 'Makeup Revolution Juicy Pout Lip Gloss Grapefruit 4.6ml is a great addition to your makeup routine, designed with those who desire an all-day luminous sheen for their lips in mind.',
            'price'        => 235.11,
            'stock_number' => 5,
            'image'        => 'https://static.beautytocare.com/cdn-cgi/image/width=768,height=650,f=auto/media/catalog/product//m/a/makeup-revolution-juicy-pout-lip-gloss-grapefruit-4-6ml.png',
            'tags'         => ['makeup', 'beauty'],
        ],
        
    ];

    foreach ($products as $product) {
    $tags = $product['tags'];
    unset($product['tags']);
    $product = Product::create($product);
    $tagIds = Tag::whereIn('name', $tags)->pluck('id');
    $product->tags()->attach($tagIds);
    }
}
}
