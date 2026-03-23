<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Barryvdh\DomPDF\Facade\Pdf;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\View\View;

class ReceiptController extends Controller
{
    public function show(Transaction $transaction): View
    {
        $transaction->load(['details', 'cashier']);

        return view('transactions.receipt', compact('transaction'));
    }

    public function downloadPdf(Transaction $transaction): Response
    {
        $transaction->load(['details', 'cashier']);

        $pdf = Pdf::loadView('transactions.pdf', [
            'transaction' => $transaction,
        ])->setPaper('a5', 'portrait');

        return $pdf->download($transaction->invoice_number.'.pdf');
    }
}
