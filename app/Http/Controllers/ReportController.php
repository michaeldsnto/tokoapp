<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        $period = $request->string('period')->toString() ?: 'daily';
        $date = $request->date('date') ?? today();
        $month = $request->string('month')->toString() ?: now()->format('Y-m');

        $baseQuery = Transaction::query()
            ->with('cashier')
            ->when($period === 'daily', fn ($query) => $query->whereDate('transacted_at', $date))
            ->when($period === 'monthly', function ($query) use ($month): void {
                [$year, $monthValue] = explode('-', $month);
                $query->whereYear('transacted_at', $year)->whereMonth('transacted_at', $monthValue);
            });

        $transactions = (clone $baseQuery)
            ->latest('transacted_at')
            ->paginate(15)
            ->withQueryString();

        $paidBaseQuery = (clone $baseQuery)->where('payment_status', 'paid');

        $summary = [
            'revenue' => (clone $paidBaseQuery)->sum('total'),
        ];

        $summary['transaction_count'] = $transactions->total();
        $summary['average_order'] = $summary['transaction_count'] > 0
            ? $transactions->getCollection()->avg('total')
            : 0;

        $bestSelling = DB::table('transaction_details')
            ->join('transactions', 'transactions.id', '=', 'transaction_details.transaction_id')
            ->where('transactions.payment_status', '=', 'paid')
            ->select('transaction_details.product_name', DB::raw('SUM(transaction_details.quantity) as qty_sold'))
            ->when($period === 'daily', fn ($query) => $query->whereDate('transactions.transacted_at', $date))
            ->when($period === 'monthly', function ($query) use ($month): void {
                [$year, $monthValue] = explode('-', $month);
                $query->whereYear('transactions.transacted_at', $year)
                    ->whereMonth('transactions.transacted_at', $monthValue);
            })
            ->groupBy('transaction_details.product_name')
            ->orderByDesc('qty_sold')
            ->limit(10)
            ->get();

        return view('reports.index', compact('transactions', 'summary', 'bestSelling', 'period', 'date', 'month'));
    }

    public function exportCsv(Request $request): StreamedResponse
    {
        $period = $request->string('period')->toString() ?: 'daily';
        $date = $request->date('date') ?? today();
        $month = $request->string('month')->toString() ?: now()->format('Y-m');

        $transactions = Transaction::query()
            ->with('cashier')
            ->when($period === 'daily', fn ($query) => $query->whereDate('transacted_at', $date))
            ->when($period === 'monthly', function ($query) use ($month): void {
                [$year, $monthValue] = explode('-', $month);
                $query->whereYear('transacted_at', $year)->whereMonth('transacted_at', $monthValue);
            })
            ->latest('transacted_at')
            ->get();

        return response()->streamDownload(function () use ($transactions): void {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Invoice', 'Mode', 'Status', 'Pelanggan', 'Tanggal', 'Kasir', 'Subtotal', 'Diskon', 'Total', 'Bayar', 'Kembalian']);

            foreach ($transactions as $transaction) {
                fputcsv($handle, [
                    $transaction->invoice_number,
                    $transaction->transaction_mode,
                    $transaction->payment_status,
                    $transaction->customer_name,
                    $transaction->transacted_at,
                    $transaction->cashier->name,
                    $transaction->subtotal,
                    $transaction->discount_amount,
                    $transaction->total,
                    $transaction->paid_amount,
                    $transaction->change_amount,
                ]);
            }

            fclose($handle);
        }, 'sales-report.csv', ['Content-Type' => 'text/csv']);
    }
}
