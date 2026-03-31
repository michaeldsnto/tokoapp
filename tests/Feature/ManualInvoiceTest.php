<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ManualInvoiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_cashier_can_create_manual_invoice_and_mark_it_paid(): void
    {
        $cashier = User::factory()->create(['role' => 'cashier']);
        $product = $this->createProduct();

        $createResponse = $this->actingAs($cashier)->post(route('manual-invoices.store'), [
            'customer_name' => 'Pelanggan Toko',
            'notes' => 'Bayar besok',
            'items' => [
                [
                    'product_id' => $product->id,
                    'unit_type' => 'pak',
                    'quantity' => 1,
                ],
            ],
        ]);

        $transaction = Transaction::query()->with('details')->first();

        $createResponse->assertRedirect(route('transactions.receipt', $transaction));
        $this->assertNotNull($transaction);
        $this->assertSame('manual', $transaction->transaction_mode);
        $this->assertSame('unpaid', $transaction->payment_status);
        $this->assertSame('Pelanggan Toko', $transaction->customer_name);
        $this->assertSame('48000.00', $transaction->total);
        $this->assertSame('0.00', $transaction->paid_amount);
        $this->assertCount(1, $transaction->details);
        $this->assertSame('pak', $transaction->details->first()->unit_type);
        $this->assertSame('48000.00', $transaction->details->first()->unit_price);

        $markPaidResponse = $this->actingAs($cashier)->patch(route('manual-invoices.mark-paid', $transaction));

        $markPaidResponse->assertRedirect(route('manual-invoices.index'));
        $transaction->refresh();
        $this->assertSame('paid', $transaction->payment_status);
        $this->assertSame('48000.00', $transaction->paid_amount);
        $this->assertSame('0.00', $transaction->change_amount);
    }

    private function createProduct(): Product
    {
        $category = Category::create([
            'name' => 'Kebutuhan Harian',
            'description' => 'Produk rumah tangga',
        ]);

        return Product::create([
            'category_id' => $category->id,
            'name' => 'Sabun Mandi',
            'code' => '899100100010',
            'price' => 4000,
            'price_per_unit' => 4000,
            'price_per_pack' => 48000,
            'price_per_dozen' => 42000,
            'is_active' => true,
        ]);
    }
}
