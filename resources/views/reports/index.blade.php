@extends('layouts.app', ['title' => 'Laporan Penjualan', 'heading' => 'Laporan Penjualan', 'subheading' => 'Pantau penjualan, nota manual, dan export CSV.'])

@section('top_actions')
<a href="{{ route('reports.export.csv', request()->query()) }}" class="btn btn-primary">Export CSV</a>
@endsection

@section('content')
<div class="panel" style="margin-bottom:18px;">
    <form method="GET" class="form-grid">
        <div class="field">
            <label for="period">Periode</label>
            <select id="period" name="period">
                <option value="daily" @selected($period === 'daily')>Harian</option>
                <option value="monthly" @selected($period === 'monthly')>Bulanan</option>
            </select>
        </div>
        <div class="field">
            <label for="date">Tanggal</label>
            <input type="date" id="date" name="date" value="{{ $date->format('Y-m-d') }}">
        </div>
        <div class="field">
            <label for="month">Bulan</label>
            <input type="month" id="month" name="month" value="{{ $month }}">
        </div>
        <div class="field" style="justify-content:end;">
            <button class="btn btn-primary" style="margin-top:32px;">Tampilkan</button>
        </div>
    </form>
</div>
<div class="grid cols-3" style="margin-bottom:18px;">
    <div class="panel"><div class="muted">Pendapatan</div><div class="stat-value">Rp {{ number_format($summary['revenue'], 0, ',', '.') }}</div><div class="muted">Hanya transaksi paid</div></div>
    <div class="panel"><div class="muted">Jumlah Transaksi</div><div class="stat-value">{{ $summary['transaction_count'] }}</div></div>
    <div class="panel"><div class="muted">Rata-rata Order</div><div class="stat-value">Rp {{ number_format($summary['average_order'], 0, ',', '.') }}</div></div>
</div>
<div class="grid cols-2">
    <div class="panel">
        <h3>Daftar Transaksi</h3>
        <div class="table-wrap"><table><thead><tr><th>Invoice</th><th>Mode</th><th>Status</th><th>Pelanggan</th><th>Total</th></tr></thead><tbody>
            @forelse($transactions as $transaction)
                <tr>
                    <td><a href="{{ route('transactions.receipt', $transaction) }}">{{ $transaction->invoice_number }}</a></td>
                    <td>{{ strtoupper($transaction->transaction_mode) }}</td>
                    <td>{{ strtoupper($transaction->payment_status) }}</td>
                    <td>{{ $transaction->customer_name ?: '-' }}</td>
                    <td>Rp {{ number_format($transaction->total, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr><td colspan="5" class="muted">Belum ada data transaksi.</td></tr>
            @endforelse
        </tbody></table></div>
        <div class="pagination">{{ $transactions->links() }}</div>
    </div>
    <div class="panel">
        <h3>Barang Terlaris</h3>
        <div class="table-wrap"><table><thead><tr><th>Barang</th><th>Qty Terjual</th></tr></thead><tbody>
            @forelse($bestSelling as $item)
                <tr><td>{{ $item->product_name }}</td><td>{{ $item->qty_sold }} pcs</td></tr>
            @empty
                <tr><td colspan="2" class="muted">Belum ada data penjualan paid.</td></tr>
            @endforelse
        </tbody></table></div>
    </div>
</div>
@endsection
