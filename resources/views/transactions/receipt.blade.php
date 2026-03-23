@extends('layouts.app', ['title' => 'Nota '.$transaction->invoice_number, 'heading' => 'Nota Transaksi', 'subheading' => 'Cetak atau simpan nota setelah transaksi berhasil.'])

@section('top_actions')
<button class="btn btn-primary no-print" onclick="window.print()">Print Nota</button>
<a href="{{ route('transactions.receipt.pdf', $transaction) }}" class="btn btn-primary no-print">Export PDF</a>
@if($transaction->transaction_mode === 'manual' && $transaction->payment_status !== 'paid')
<form action="{{ route('manual-invoices.mark-paid', $transaction) }}" method="POST" class="inline no-print" onsubmit="return confirm('Tandai nota ini sebagai lunas?')">
    @csrf
    @method('PATCH')
    <button class="btn" type="submit">Tandai Lunas</button>
</form>
@endif
@if($transaction->transaction_mode === 'manual')
<a href="{{ route('manual-invoices.index') }}" class="btn no-print">Daftar Nota Manual</a>
@else
<a href="{{ route('pos.index') }}" class="btn no-print">Transaksi Baru</a>
@endif
@endsection

@section('content')
<div class="receipt">
    <div style="text-align:center; margin-bottom:18px;">
        <strong style="font-size:1.35rem;">KASIMURA</strong><br>
        <span>Jln. Bengkunis, Wuring</span><br>
        <span>{{ $transaction->transacted_at->format('d M Y H:i') }}</span>
    </div>

    @if($transaction->customer_name)
        <div style="margin-bottom:14px; text-align:center;">
            <strong>Pelanggan: {{ $transaction->customer_name }}</strong>
        </div>
    @endif

    <table>
        <thead><tr><th>Barang</th><th>Qty</th><th>Total</th></tr></thead>
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

    <div style="margin-top:16px; display:flex; justify-content:space-between;">
        <span><strong>Total</strong></span>
        <strong>Rp {{ number_format($transaction->total, 0, ',', '.') }}</strong>
    </div>
</div>
@endsection
