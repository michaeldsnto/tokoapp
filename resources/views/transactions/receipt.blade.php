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
    <div style="text-align:center; margin-bottom:20px;">
        <strong style="font-size:1.3rem;">TokoApp POS</strong><br>
        <span>Jl. Contoh No. 123</span><br>
        <span>{{ $transaction->invoice_number }}</span><br>
        <span>{{ $transaction->transacted_at->format('d M Y H:i') }}</span><br>
        <span>{{ strtoupper($transaction->transaction_mode) }} | {{ strtoupper($transaction->payment_status) }}</span>
    </div>
    <table>
        <thead><tr><th>Item</th><th>Qty</th><th>Total</th></tr></thead>
        <tbody>
        @foreach($transaction->details as $detail)
            <tr>
                <td>{{ $detail->product_name }}<br><small>{{ $detail->product_code }} | {{ ucfirst($detail->unit_type) }} @ Rp {{ number_format($detail->unit_price, 0, ',', '.') }}</small></td>
                <td>{{ $detail->quantity }}</td>
                <td>Rp {{ number_format($detail->line_total, 0, ',', '.') }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
    <div style="margin-top:18px;">
        @if($transaction->customer_name)
            <div style="display:flex; justify-content:space-between; margin-bottom:8px;"><span>Pelanggan</span><strong>{{ $transaction->customer_name }}</strong></div>
        @endif
        @if($transaction->due_date)
            <div style="display:flex; justify-content:space-between; margin-bottom:8px;"><span>Jatuh Tempo</span><strong>{{ $transaction->due_date->format('d M Y H:i') }}</strong></div>
        @endif
        <div style="display:flex; justify-content:space-between; margin-bottom:8px;"><span>Subtotal</span><strong>Rp {{ number_format($transaction->subtotal, 0, ',', '.') }}</strong></div>
        <div style="display:flex; justify-content:space-between; margin-bottom:8px;"><span>Diskon</span><strong>Rp {{ number_format($transaction->discount_amount, 0, ',', '.') }}</strong></div>
        <div style="display:flex; justify-content:space-between; margin-bottom:8px;"><span>Total</span><strong>Rp {{ number_format($transaction->total, 0, ',', '.') }}</strong></div>
        <div style="display:flex; justify-content:space-between; margin-bottom:8px;"><span>Bayar</span><strong>Rp {{ number_format($transaction->paid_amount, 0, ',', '.') }}</strong></div>
        <div style="display:flex; justify-content:space-between;"><span>Kembalian</span><strong>Rp {{ number_format($transaction->change_amount, 0, ',', '.') }}</strong></div>
    </div>
    <div style="margin-top:18px; text-align:center;">
        <small>Kasir: {{ $transaction->cashier->name }}</small><br>
        <small>Terima kasih telah berbelanja.</small>
    </div>
</div>
@endsection
