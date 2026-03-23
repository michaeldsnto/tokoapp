<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        User::query()->updateOrCreate(
            ['email' => 'admin@toko.test'],
            [
                'name' => 'Admin Toko',
                'role' => 'admin',
                'password' => 'password',
            ]
        );

        User::query()->updateOrCreate(
            ['email' => 'kasir@toko.test'],
            [
                'name' => 'Kasir Utama',
                'role' => 'cashier',
                'password' => 'password',
            ]
        );

        $categories = collect([
            ['name' => 'Minuman', 'description' => 'Produk minuman dingin dan hangat'],
            ['name' => 'Makanan', 'description' => 'Snack dan makanan ringan'],
            ['name' => 'Kebutuhan Harian', 'description' => 'Sabun, tissue, dan kebutuhan rumah tangga'],
        ])->map(function (array $category) {
            return Category::query()->firstOrCreate(
                ['name' => $category['name']],
                [
                    'slug' => Str::slug($category['name']),
                    'description' => $category['description'],
                ]
            );
        });

        $products = [
            ['name' => 'Air Mineral 600ml', 'code' => '899100100001', 'satuan' => 4000, 'pak' => 48000, 'lusin' => 42000, 'category' => 'Minuman'],
            ['name' => 'Kopi Instan', 'code' => '899100100002', 'satuan' => 2500, 'pak' => 30000, 'lusin' => 27000, 'category' => 'Minuman'],
            ['name' => 'Keripik Kentang', 'code' => '899100100003', 'satuan' => 12000, 'pak' => 145000, 'lusin' => 130000, 'category' => 'Makanan'],
            ['name' => 'Sabun Mandi', 'code' => '899100100004', 'satuan' => 8500, 'pak' => 98000, 'lusin' => 92000, 'category' => 'Kebutuhan Harian'],
        ];

        foreach ($products as $product) {
            $category = $categories->firstWhere('name', $product['category']);

            Product::query()->updateOrCreate(
                ['code' => $product['code']],
                [
                    'category_id' => $category->id,
                    'name' => $product['name'],
                    'price' => $product['satuan'],
                    'price_per_unit' => $product['satuan'],
                    'price_per_pack' => $product['pak'],
                    'price_per_dozen' => $product['lusin'],
                    'is_active' => true,
                ]
            );
        }
    }
}
