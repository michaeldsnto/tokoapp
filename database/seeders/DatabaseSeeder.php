<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
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
        ])->map(fn (array $category) => Category::query()->firstOrCreate(['name' => $category['name']], $category));

        $products = [
            ['name' => 'Air Mineral 600ml', 'code' => '899100100001', 'price' => 4000, 'stock' => 30, 'category' => 'Minuman'],
            ['name' => 'Kopi Instan', 'code' => '899100100002', 'price' => 2500, 'stock' => 40, 'category' => 'Minuman'],
            ['name' => 'Keripik Kentang', 'code' => '899100100003', 'price' => 12000, 'stock' => 12, 'category' => 'Makanan'],
            ['name' => 'Sabun Mandi', 'code' => '899100100004', 'price' => 8500, 'stock' => 8, 'category' => 'Kebutuhan Harian'],
        ];

        foreach ($products as $product) {
            $category = $categories->firstWhere('name', $product['category']);

            Product::query()->updateOrCreate(
                ['code' => $product['code']],
                [
                    'category_id' => $category->id,
                    'name' => $product['name'],
                    'price' => $product['price'],
                    'stock' => $product['stock'],
                    'low_stock_threshold' => 5,
                    'is_active' => true,
                ]
            );
        }
    }
}
