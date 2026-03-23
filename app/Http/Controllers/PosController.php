<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTransactionRequest;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PosController extends Controller
{
    public function index(): View
    {
        return view('pos.index', [
            'products' => Product::query()
                ->with('category')
                ->where('is_active', true)
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function productLookup(Request $request)
    {
        $request->validate([
            'code' => ['required', 'string'],
        ]);

        $product = Product::query()
            ->where('code', $request->string('code'))
            ->where('is_active', true)
            ->first();

        if (! $product) {
            return response()->json(['message' => 'Barang tidak ditemukan.'], 404);
        }

        return response()->json($product);
    }

    public function store(StoreTransactionRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $discountAmount = (float) ($validated['discount_amount'] ?? 0);

        $transaction = DB::transaction(function () use ($validated, $discountAmount, $request) {
            $lines = collect($validated['items'])->map(function (array $item) {
                $product = Product::query()->lockForUpdate()->findOrFail($item['product_id']);
                $quantity = (int) $item['quantity'];

                abort_if($product->stock < $quantity, 422, "Stok {$product->name} tidak mencukupi.");

                $lineTotal = $product->price * $quantity;

                return compact('product', 'quantity', 'lineTotal');
            });

            $subtotal = $lines->sum('lineTotal');
            $total = max($subtotal - $discountAmount, 0);
            $paidAmount = (float) $validated['paid_amount'];

            abort_if($paidAmount < $total, 422, 'Nominal pembayaran kurang dari total transaksi.');

            $transaction = Transaction::create([
                'invoice_number' => 'INV-'.now()->format('YmdHis').'-'.Str::upper(Str::random(4)),
                'user_id' => $request->user()->id,
                'subtotal' => $subtotal,
                'discount_amount' => $discountAmount,
                'total' => $total,
                'paid_amount' => $paidAmount,
                'change_amount' => $paidAmount - $total,
                'item_count' => $lines->sum('quantity'),
                'notes' => $validated['notes'] ?? null,
                'transacted_at' => now(),
            ]);

            foreach ($lines as $line) {
                $product = $line['product'];
                $quantity = $line['quantity'];

                TransactionDetail::create([
                    'transaction_id' => $transaction->id,
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'product_code' => $product->code,
                    'unit_price' => $product->price,
                    'quantity' => $quantity,
                    'line_total' => $line['lineTotal'],
                ]);

                $product->decrement('stock', $quantity);
            }

            return $transaction;
        });

        return redirect()->route('transactions.receipt', $transaction)->with('success', 'Transaksi berhasil disimpan.');
    }
}
