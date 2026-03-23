<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $todaySales = Transaction::query()
            ->where('payment_status', 'paid')
            ->whereDate('transacted_at', today())
            ->sum('total');

        $monthSales = Transaction::query()
            ->where('payment_status', 'paid')
            ->whereBetween('transacted_at', [now()->startOfMonth(), now()->endOfMonth()])
            ->sum('total');

        $pendingManualInvoices = Transaction::query()
            ->where('transaction_mode', 'manual')
            ->where('payment_status', 'unpaid')
            ->latest('transacted_at')
            ->limit(5)
            ->get();

        $topProducts = DB::table('transaction_details')
            ->join('transactions', 'transactions.id', '=', 'transaction_details.transaction_id')
            ->where('transactions.payment_status', '=', 'paid')
            ->select('product_name', DB::raw('SUM(quantity) as qty_sold'))
            ->groupBy('product_name')
            ->orderByDesc('qty_sold')
            ->limit(5)
            ->get();

        return view('dashboard', [
            'stats' => [
                'products' => Product::count(),
                'transactions' => Transaction::count(),
                'today_sales' => $todaySales,
                'month_sales' => $monthSales,
            ],
            'pendingManualInvoices' => $pendingManualInvoices,
            'topProducts' => $topProducts,
        ]);
    }
}
