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
            ->whereDate('transacted_at', today())
            ->sum('total');

        $monthSales = Transaction::query()
            ->whereBetween('transacted_at', [now()->startOfMonth(), now()->endOfMonth()])
            ->sum('total');

        $lowStockProducts = Product::query()
            ->with('category')
            ->whereColumn('stock', '<=', 'low_stock_threshold')
            ->orderBy('stock')
            ->get();

        $topProducts = DB::table('transaction_details')
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
            'lowStockProducts' => $lowStockProducts,
            'topProducts' => $topProducts,
        ]);
    }
}
