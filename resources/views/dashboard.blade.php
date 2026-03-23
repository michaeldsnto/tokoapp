@extends('layouts.app', ['title' => 'Dashboard POS', 'heading' => 'Dashboard', 'subheading' => 'Pantau transaksi, stok, dan performa barang terlaris.'])

@section('top_actions')
<a href="{{ route('pos.index') }}" class="btn btn-primary">Mulai Transaksi</a>
@endsection

@section('content')
<div class="grid cols-4">
    <div class="panel"><div class="muted">Jumlah Barang</div><div class="stat-value">{{ $stats['products'] }}</div><div class="badge">Data produk aktif</div></div>
    <div class="panel"><div class="muted">Total Transaksi</div><div class="stat-value">{{ $stats['transactions'] }}</div><div class="badge">Keseluruhan penjualan</div></div>
    <div class="panel"><div class="muted">Pendapatan Hari Ini</div><div class="stat-value">Rp {{ number_format($stats['today_sales'], 0, ',', '.') }}</div><div class="badge">Update real-time</div></div>
    <div class="panel"><div class="muted">Pendapatan Bulan Ini</div><div class="stat-value">Rp {{ number_format($stats['month_sales'], 0, ',', '.') }}</div><div class="badge">Ringkasan bulanan</div></div>
</div>
<div class="grid cols-2" style="margin-top:18px;">
    <div class="panel">
        <h3>Stok Menipis</h3><p class="muted">Barang dengan stok berada di bawah batas minimum.</p>
        <div class="table-wrap"><table><thead><tr><th>Barang</th><th>Kategori</th><th>Stok</th><th>Status</th></tr></thead><tbody>
            @forelse($lowStockProducts as $product)
                <tr><td>{{ $product->name }}</td><td>{{ $product->category->name }}</td><td>{{ $product->stock }}</td><td><span class="badge warning">Perlu Restock</span></td></tr>
            @empty
                <tr><td colspan="4" class="muted">Belum ada stok menipis.</td></tr>
            @endforelse
        </tbody></table></div>
    </div>
    <div class="panel">
        <h3>Barang Terlaris</h3><p class="muted">Top 5 produk paling sering terjual.</p>
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
