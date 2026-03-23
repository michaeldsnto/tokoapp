<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\View\View;

class ReceiptController extends Controller
{
    public function show(Transaction $transaction): View
    {
        $transaction->load(['details', 'cashier']);

        return view('transactions.receipt', compact('transaction'));
    }
}
