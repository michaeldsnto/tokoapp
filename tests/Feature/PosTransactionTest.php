<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PosTransactionTest extends TestCase
{
    use RefreshDatabase;

    public function test_cashier_can_create_pos_transaction_with_discount_and_change(): void
    {
        $cashier = User::factory()->create(['role' => 'cashier']);
        $product = $this->createProduct();

        $response = $this->actingAs($cashier)->post(route('pos.store'), [
            'discount_amount' => 1000,
            'paid_amount' => 15000,
            'notes' => 'Pembayaran tunai',
            'items' => [
                [
                    'product_id' => $product->id,
                    'unit_type' => 'satuan',
                    'quantity' => 2,
                ],
            ],
        ]);

        $transaction = Transaction::query()->with('details')->first();

        $response->assertRedirect(route('transactions.receipt', $transaction));
        $this->assertNotNull($transaction);
        $this->assertSame('pos', $transaction->transaction_mode);
        $this->assertSame('paid', $transaction->payment_status);
        $this->assertSame('7000.00', $transaction->subtotal);
        $this->assertSame('1000.00', $transaction->discount_amount);
        $this->assertSame('6000.00', $transaction->total);
        $this->assertSame('15000.00', $transaction->paid_amount);
        $this->assertSame('9000.00', $transaction->change_amount);
        $this->assertSame(2, $transaction->item_count);
        $this->assertCount(1, $transaction->details);
        $this->assertSame('satuan', $transaction->details->first()->unit_type);
        $this->assertSame('3500.00', $transaction->details->first()->unit_price);
        $this->assertSame('7000.00', $transaction->details->first()->line_total);
    }

    private function createProduct(): Product
    {
        $category = Category::create([
            'name' => 'Minuman',
            'description' => 'Produk minuman',
        ]);

        return Product::create([
            'category_id' => $category->id,
            'name' => 'Air Mineral 600ml',
            'code' => '899100100001',
            'price' => 3500,
            'price_per_unit' => 3500,
            'price_per_pack' => 42000,
            'price_per_dozen' => 39000,
            'is_active' => true,
        ]);
    }
}
