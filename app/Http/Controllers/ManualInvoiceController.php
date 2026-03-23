<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreManualInvoiceRequest;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ManualInvoiceController extends Controller
{
    public function index(): View
    {
        return view('manual_invoices.index', [
            'manualInvoices' => Transaction::query()
                ->with('cashier')
                ->where('transaction_mode', 'manual')
                ->latest('transacted_at')
                ->paginate(15),
        ]);
    }

    public function create(): View
    {
        return view('manual_invoices.create', [
            'products' => Product::query()
                ->with('category')
                ->where('is_active', true)
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function store(StoreManualInvoiceRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $transaction = DB::transaction(function () use ($validated, $request) {
            $lines = collect($validated['items'])->map(function (array $item) {
                $product = Product::query()->lockForUpdate()->findOrFail($item['product_id']);
                $quantity = (int) $item['quantity'];
                $unitType = $item['unit_type'];
                $unitPrice = $product->getPriceForUnit($unitType);
                $lineTotal = $unitPrice * $quantity;

                return compact('product', 'quantity', 'unitType', 'unitPrice', 'lineTotal');
            });

            $subtotal = $lines->sum('lineTotal');

            $transaction = Transaction::create([
                'invoice_number' => 'INV-MNL-'.now()->format('YmdHis').'-'.Str::upper(Str::random(4)),
                'transaction_mode' => 'manual',
                'payment_status' => 'unpaid',
                'user_id' => $request->user()->id,
                'customer_name' => $validated['customer_name'],
                'subtotal' => $subtotal,
                'discount_amount' => 0,
                'total' => $subtotal,
                'paid_amount' => 0,
                'change_amount' => 0,
                'item_count' => $lines->sum('quantity'),
                'notes' => $validated['notes'] ?? null,
                'transacted_at' => now(),
                'due_date' => null,
            ]);

            foreach ($lines as $line) {
                TransactionDetail::create([
                    'transaction_id' => $transaction->id,
                    'product_id' => $line['product']->id,
                    'product_name' => $line['product']->name,
                    'product_code' => $line['product']->code,
                    'unit_type' => $line['unitType'],
                    'unit_price' => $line['unitPrice'],
                    'quantity' => $line['quantity'],
                    'line_total' => $line['lineTotal'],
                ]);
            }

            return $transaction;
        });

        return redirect()->route('transactions.receipt', $transaction)->with('success', 'Nota manual berhasil dibuat.');
    }

    public function markPaid(Transaction $transaction): RedirectResponse
    {
        abort_unless($transaction->transaction_mode === 'manual', 404);

        $transaction->update([
            'payment_status' => 'paid',
            'paid_amount' => $transaction->total,
            'change_amount' => 0,
        ]);

        return redirect()->route('manual-invoices.index')->with('success', 'Nota manual berhasil ditandai lunas.');
    }
}
