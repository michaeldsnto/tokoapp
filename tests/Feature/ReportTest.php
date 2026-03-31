<?php

namespace Tests\Feature;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportTest extends TestCase
{
    use RefreshDatabase;

    public function test_average_order_uses_all_filtered_transactions_not_only_current_page(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $date = now()->startOfDay();

        foreach (range(1, 16) as $index) {
            Transaction::create([
                'invoice_number' => sprintf('INV-%04d', $index),
                'transaction_mode' => 'pos',
                'payment_status' => 'paid',
                'user_id' => $admin->id,
                'customer_name' => null,
                'subtotal' => $index * 100,
                'discount_amount' => 0,
                'total' => $index * 100,
                'paid_amount' => $index * 100,
                'change_amount' => 0,
                'item_count' => 1,
                'notes' => null,
                'transacted_at' => $date->copy()->addMinutes($index),
                'due_date' => null,
            ]);
        }

        $response = $this->actingAs($admin)->get(route('reports.index', [
            'period' => 'daily',
            'date' => $date->format('Y-m-d'),
        ]));

        $response->assertOk();
        $response->assertViewHas('summary', function (array $summary): bool {
            return $summary['transaction_count'] === 16
                && (float) $summary['average_order'] === 850.0
                && (float) $summary['revenue'] === 13600.0;
        });
    }
}
