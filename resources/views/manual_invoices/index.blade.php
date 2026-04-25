@extends('layouts.app', ['title' => 'Daftar Nota Manual', 'heading' => 'Daftar Nota Manual', 'subheading' => 'Kelola semua nota pelanggan yang dibuat manual dan pantau status pembayarannya.'])

@section('top_actions')
<a href="{{ route('manual-invoices.create') }}" class="btn btn-primary">Buat Nota Manual</a>
@endsection

@section('content')
<div class="panel">
    <div style="display:grid; gap:14px;">
        @forelse($manualInvoices as $invoice)
            <div style="display:grid; gap:14px; padding:18px; border-radius:18px; border:1px solid var(--border); background:var(--surface-strong);">
                <div style="display:flex; justify-content:space-between; gap:12px; align-items:flex-start;">
                    <div>
                        <a href="{{ route('transactions.receipt', $invoice) }}"><strong>{{ $invoice->invoice_number }}</strong></a>
                        <div class="muted" style="margin-top:4px;">{{ $invoice->customer_name ?: 'Tanpa nama pelanggan' }}</div>
                    </div>
                    @if($invoice->payment_status === 'paid')
                        <span class="badge">PAID</span>
                    @else
                        <span class="badge warning">UNPAID</span>
                    @endif
                </div>
                <div style="display:flex; justify-content:space-between; gap:12px; flex-wrap:wrap;">
                    <span class="muted">{{ $invoice->transacted_at->format('d M Y H:i') }}</span>
                    <strong>Rp {{ number_format($invoice->total, 0, ',', '.') }}</strong>
                </div>
                <div style="display:grid; gap:10px;">
                    <a href="{{ route('transactions.receipt', $invoice) }}" class="btn">Lihat Nota</a>
                    @if($invoice->payment_status !== 'paid')
                        <form action="{{ route('manual-invoices.mark-paid', $invoice) }}" method="POST" class="inline" onsubmit="return confirm('Tandai nota ini sebagai lunas?')">
                            @csrf
                            @method('PATCH')
                            <button class="btn btn-primary" type="submit">Tandai Lunas</button>
                        </form>
                    @endif
                </div>
            </div>
        @empty
            <div class="muted">Belum ada nota manual.</div>
        @endforelse
    </div>

    <div class="pagination">{{ $manualInvoices->links() }}</div>
</div>
@endsection
