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
        <div style="display:grid; gap:12px;">
            @forelse($pendingManualInvoices as $transaction)
                <a href="{{ route('transactions.receipt', $transaction) }}" style="display:grid; gap:10px; padding:16px; border-radius:18px; border:1px solid var(--border); background:var(--surface-strong);">
                    <div style="display:flex; justify-content:space-between; gap:12px; align-items:flex-start;">
                        <strong>{{ $transaction->invoice_number }}</strong>
                        <span class="badge warning">UNPAID</span>
                    </div>
                    <div class="muted">{{ $transaction->customer_name ?: 'Tanpa nama pelanggan' }}</div>
                    <div style="display:flex; justify-content:space-between; gap:12px; flex-wrap:wrap;">
                        <span class="muted">{{ $transaction->transacted_at->format('d M Y H:i') }}</span>
                        <strong>Rp {{ number_format($transaction->total, 0, ',', '.') }}</strong>
                    </div>
                </a>
            @empty
                <div class="muted">Belum ada nota manual yang belum lunas.</div>
            @endforelse
        </div>
    </div>
    <div class="panel">
        <h3>Barang Terlaris</h3><p class="muted">Top 5 produk paling sering terjual dari transaksi yang sudah dibayar.</p>
        <div style="display:grid; gap:12px;">
            @forelse($topProducts as $item)
                <div style="display:flex; justify-content:space-between; gap:12px; align-items:center; padding:16px; border-radius:18px; border:1px solid var(--border); background:var(--surface-strong);">
                    <div>
                        <strong>{{ $item->product_name }}</strong>
                        <div class="muted">Produk terjual</div>
                    </div>
                    <span class="badge">{{ $item->qty_sold }} pcs</span>
                </div>
            @empty
                <div class="muted">Belum ada data penjualan.</div>
            @endforelse
        </div>
    </div>
</div>
@endsection
