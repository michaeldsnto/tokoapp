@extends('layouts.app', ['title' => 'Daftar Nota Manual', 'heading' => 'Daftar Nota Manual', 'subheading' => 'Kelola semua nota pelanggan yang dibuat manual dan pantau status pembayarannya.'])

@section('top_actions')
<a href="{{ route('manual-invoices.create') }}" class="btn btn-primary">Buat Nota Manual</a>
@endsection

@section('content')
<div class="panel">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Invoice</th>
                    <th>Pelanggan</th>
                    <th>Status</th>
                    <th>Tanggal</th>
                    <th>Total</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
            @forelse($manualInvoices as $invoice)
                <tr>
                    <td><a href="{{ route('transactions.receipt', $invoice) }}">{{ $invoice->invoice_number }}</a></td>
                    <td>{{ $invoice->customer_name ?: '-' }}</td>
                    <td>
                        @if($invoice->payment_status === 'paid')
                            <span class="badge">PAID</span>
                        @else
                            <span class="badge warning">UNPAID</span>
                        @endif
                    </td>
                    <td>{{ $invoice->transacted_at->format('d M Y H:i') }}</td>
                    <td>Rp {{ number_format($invoice->total, 0, ',', '.') }}</td>
                    <td style="white-space: nowrap; display:flex; gap:8px; flex-wrap:wrap;">
                        <a href="{{ route('transactions.receipt', $invoice) }}" class="btn">Lihat Nota</a>
                        @if($invoice->payment_status !== 'paid')
                            <form action="{{ route('manual-invoices.mark-paid', $invoice) }}" method="POST" class="inline" onsubmit="return confirm('Tandai nota ini sebagai lunas?')">
                                @csrf
                                @method('PATCH')
                                <button class="btn btn-primary" type="submit">Tandai Lunas</button>
                            </form>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="muted">Belum ada nota manual.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div class="pagination">{{ $manualInvoices->links() }}</div>
</div>
@endsection
