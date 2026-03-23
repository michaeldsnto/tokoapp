@extends('layouts.app', ['title' => 'Dashboard POS', 'heading' => 'Dashboard', 'subheading' => 'Pantau transaksi, nota manual belum lunas, dan performa barang terlaris.'])

@section('top_actions')
<a href="{{ route('pos.index') }}" class="btn btn-primary">Mulai Transaksi</a>
<a href="{{ route('manual-invoices.create') }}" class="btn">Buat Nota Manual</a>
@endsection

@section('content')
<div class="grid cols-4">
    <div class="panel"><div class="muted">Jumlah Barang</div><div class="stat-value">{{ $stats['products'] }}</div><div class="badge">Data produk aktif</div></div>
    <div class="panel"><div class="muted">Total Transaksi</div><div class="stat-value">{{ $stats['transactions'] }}</div><div class="badge">POS + nota manual</div></div>
    <div class="panel"><div class="muted">Pendapatan Hari Ini</div><div class="stat-value">Rp {{ number_format($stats['today_sales'], 0, ',', '.') }}</div><div class="badge">Hanya transaksi paid</div></div>
    <div class="panel"><div class="muted">Pendapatan Bulan Ini</div><div class="stat-value">Rp {{ number_format($stats['month_sales'], 0, ',', '.') }}</div><div class="badge">Ringkasan bulanan</div></div>
</div>
<div class="grid cols-2" style="margin-top:18px;">
    <div class="panel">
        <h3>Nota Manual Belum Lunas</h3><p class="muted">Tagihan pelanggan yang dibuat manual dan belum dibayar.</p>
        <div class="table-wrap"><table><thead><tr><th>Invoice</th><th>Pelanggan</th><th>Jatuh Tempo</th><th>Total</th></tr></thead><tbody>
            @forelse($pendingManualInvoices as $transaction)
                <tr>
                    <td><a href="{{ route('transactions.receipt', $transaction) }}">{{ $transaction->invoice_number }}</a></td>
                    <td>{{ $transaction->customer_name ?: '-' }}</td>
                    <td>{{ $transaction->due_date?->format('d M Y H:i') ?: '-' }}</td>
                    <td>Rp {{ number_format($transaction->total, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr><td colspan="4" class="muted">Belum ada nota manual yang belum lunas.</td></tr>
            @endforelse
        </tbody></table></div>
    </div>
    <div class="panel">
        <h3>Barang Terlaris</h3><p class="muted">Top 5 produk paling sering terjual dari transaksi yang sudah dibayar.</p>
        <div class="table-wrap"><table><thead><tr><th>Barang</th><th>Terjual</th></tr></thead><tbody>
            @forelse($topProducts as $item)
                <tr><td>{{ $item->product_name }}</td><td>{{ $item->qty_sold }} pcs</td></tr>
            @empty
                <tr><td colspan="2" class="muted">Belum ada data penjualan.</td></tr>
            @endforelse
        </tbody></table></div>
    </div>
</div>
@endsection
