<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>{{ $transaction->invoice_number }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; color: #111827; font-size: 12px; }
        .wrapper { border: 1px solid #e5e7eb; border-radius: 16px; padding: 20px; }
        .center { text-align: center; }
        table { width: 100%; border-collapse: collapse; margin-top: 16px; }
        th, td { padding: 8px 4px; border-bottom: 1px dashed #d1d5db; text-align: left; }
        th:last-child, td:last-child { text-align: right; }
        .footer { margin-top: 16px; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="center">
            <h2 style="margin: 0 0 6px;">KASIMURA</h2>
            <div>Jln. Bengkunis, Wuring</div>
            <div>{{ $transaction->transacted_at->format('d M Y H:i') }}</div>
        </div>

        @if($transaction->customer_name)
            <div class="center" style="margin-top: 12px;">
                <strong>Pelanggan: {{ $transaction->customer_name }}</strong>
            </div>
        @endif

        <table>
            <thead>
                <tr>
                    <th>Barang</th>
                    <th>Qty</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($transaction->details as $detail)
                    <tr>
                        <td>{{ $detail->product_name }}</td>
                        <td>{{ $detail->quantity }} {{ ucfirst($detail->unit_type) }}</td>
                        <td>Rp {{ number_format($detail->line_total, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="footer">
            <div style="display:flex; justify-content:space-between;">
                <span><strong>Total</strong></span>
                <strong>Rp {{ number_format($transaction->total, 0, ',', '.') }}</strong>
            </div>
        </div>
    </div>
</body>
</html>
