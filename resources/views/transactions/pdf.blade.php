<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>{{ $transaction->invoice_number }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; color: #111827; font-size: 12px; }
        .wrapper { border: 1px solid #e5e7eb; border-radius: 16px; padding: 20px; }
        .center { text-align: center; }
        .muted { color: #6b7280; }
        table { width: 100%; border-collapse: collapse; margin-top: 16px; }
        th, td { padding: 8px 4px; border-bottom: 1px dashed #d1d5db; text-align: left; }
        th:last-child, td:last-child { text-align: right; }
        .summary { margin-top: 16px; }
        .summary-row { margin-bottom: 6px; }
        .summary-row span:last-child { float: right; }
        .footer { margin-top: 18px; text-align: center; font-size: 11px; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="center">
            <h2 style="margin: 0 0 6px;">TokoApp POS</h2>
            <div class="muted">Jl. Contoh No. 123</div>
            <div>{{ $transaction->invoice_number }}</div>
            <div>{{ $transaction->transacted_at->format('d M Y H:i') }}</div>
            <div>{{ strtoupper($transaction->transaction_mode) }} | {{ strtoupper($transaction->payment_status) }}</div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Qty</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($transaction->details as $detail)
                    <tr>
                        <td>{{ $detail->product_name }}<br><span class="muted">{{ $detail->product_code }} | {{ ucfirst($detail->unit_type) }} @ Rp {{ number_format($detail->unit_price, 0, ',', '.') }}</span></td>
                        <td>{{ $detail->quantity }}</td>
                        <td>Rp {{ number_format($detail->line_total, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="summary">
            @if($transaction->customer_name)
                <div class="summary-row"><span>Pelanggan</span><span>{{ $transaction->customer_name }}</span></div>
            @endif
            @if($transaction->due_date)
                <div class="summary-row"><span>Jatuh Tempo</span><span>{{ $transaction->due_date->format('d M Y H:i') }}</span></div>
            @endif
            <div class="summary-row"><span>Subtotal</span><span>Rp {{ number_format($transaction->subtotal, 0, ',', '.') }}</span></div>
            <div class="summary-row"><span>Diskon</span><span>Rp {{ number_format($transaction->discount_amount, 0, ',', '.') }}</span></div>
            <div class="summary-row"><span>Total</span><span><strong>Rp {{ number_format($transaction->total, 0, ',', '.') }}</strong></span></div>
            <div class="summary-row"><span>Bayar</span><span>Rp {{ number_format($transaction->paid_amount, 0, ',', '.') }}</span></div>
            <div class="summary-row"><span>Kembalian</span><span>Rp {{ number_format($transaction->change_amount, 0, ',', '.') }}</span></div>
        </div>

        <div class="footer">
            <div>Kasir: {{ $transaction->cashier->name }}</div>
            <div>Terima kasih telah berbelanja.</div>
        </div>
    </div>
</body>
</html>
