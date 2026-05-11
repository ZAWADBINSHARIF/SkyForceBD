<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = Category::all();

        foreach ($categories as $category) {

            for ($i = 1; $i <= 5; $i++) {

                $price = rand(100, 2000);

                Product::create([
                    'category_id' => $category->id,
                    'product_name' => $category->name . ' Product ' . $i,
                    'product_description' => 'Description for ' . $category->name . ' product ' . $i,
                    'slug' => Str::slug($category->name . '-product-' . $i),
                    'price' => $price,
                    'old_price' => $price + rand(50, 500),
                    'discount' => rand(5, 50),
                    'product_images' => [
                        "https://picsum.photos/600/600?random={$i}"
                    ],
                    'published' => true,
                ]);
            }
        }
    }
}
