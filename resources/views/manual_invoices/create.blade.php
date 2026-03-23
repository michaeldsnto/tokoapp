@extends('layouts.app', ['title' => 'Nota Manual', 'heading' => 'Buat Nota Manual', 'subheading' => 'Gunakan untuk pelanggan yang belum bayar langsung atau pembayaran menyusul.'])

@section('top_actions')
    <span class="badge">Status Unpaid</span>
    <a href="{{ route('manual-invoices.index') }}" class="btn">Daftar Nota</a>
@endsection

@section('content')
    <style>
        .manual-shell { display:grid; gap:20px; grid-template-columns:minmax(0,1.2fr) minmax(360px,.85fr); align-items:start; }
        .manual-grid { display:grid; gap:16px; grid-template-columns:repeat(auto-fit, minmax(220px,1fr)); margin-top:20px; }
        .manual-card { border:1px solid var(--border); border-radius:20px; padding:18px; background:var(--surface-strong); cursor:pointer; transition:.18s ease; }
        .manual-card:hover { transform:translateY(-3px); box-shadow:0 18px 32px rgba(15,23,42,.12); }
        .manual-card strong { display:block; margin-bottom:6px; }
        .manual-price { display:grid; gap:6px; margin-top:12px; }
        .manual-row { display:flex; justify-content:space-between; gap:8px; font-size:.92rem; }
        .manual-cart { display:grid; gap:12px; }
        .manual-item { padding:14px; border:1px solid var(--border); border-radius:16px; background:var(--surface-strong); display:grid; gap:12px; grid-template-columns:minmax(0,1.1fr) 120px 90px; }
        .manual-item-total { grid-column:1 / -1; display:flex; justify-content:space-between; align-items:center; padding-top:10px; border-top:1px dashed var(--border); }
        .manual-empty { padding:24px; text-align:center; border:1px dashed var(--border); border-radius:16px; color:var(--muted); }
        @media (max-width:1100px) { .manual-shell { grid-template-columns:1fr; } }
        @media (max-width:768px) { .manual-item { grid-template-columns:1fr; } .manual-item-total { flex-direction:column; align-items:flex-start; gap:10px; } }
    </style>

    <div class="manual-shell">
        <section class="panel">
            <div class="form-grid">
                <div class="field">
                    <label for="manual-search">Cari barang</label>
                    <input type="text" id="manual-search" placeholder="Cari nama, kode, atau kategori">
                </div>
                <div class="field">
                    <label for="manual-category">Filter kategori</label>
                    <select id="manual-category">
                        <option value="">Semua kategori</option>
                        @foreach($products->pluck('category.name')->unique()->sort()->values() as $categoryName)
                            <option value="{{ $categoryName }}">{{ $categoryName }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="manual-grid" id="manual-grid">
                @foreach($products as $product)
                    <button type="button" class="manual-card add-manual-product" data-search="{{ strtolower($product->name.' '.$product->code.' '.$product->category->name) }}" data-category="{{ $product->category->name }}" data-product='{{ json_encode(['id' => $product->id, 'name' => $product->name, 'code' => $product->code, 'price_per_unit' => (float) $product->price_per_unit, 'price_per_pack' => (float) $product->price_per_pack, 'price_per_dozen' => (float) $product->price_per_dozen, 'category' => $product->category->name]) }}'>
                        <strong>{{ $product->name }}</strong>
                        <div class="muted">{{ $product->code }} | {{ $product->category->name }}</div>
                        <div class="manual-price">
                            <div class="manual-row"><span>Satuan</span><strong>Rp {{ number_format($product->price_per_unit, 0, ',', '.') }}</strong></div>
                            <div class="manual-row"><span>Lusin</span><strong>Rp {{ number_format($product->price_per_dozen, 0, ',', '.') }}</strong></div>
                            <div class="manual-row"><span>Pak</span><strong>Rp {{ number_format($product->price_per_pack, 0, ',', '.') }}</strong></div>
                        </div>
                    </button>
                @endforeach
            </div>
        </section>

        <aside class="panel">
            <form action="{{ route('manual-invoices.store') }}" method="POST" id="manual-form">
                @csrf
                <div class="field">
                    <label for="customer_name">Nama Pelanggan</label>
                    <input type="text" id="customer_name" name="customer_name" value="{{ old('customer_name') }}" required>
                    @error('customer_name') <div class="error">{{ $message }}</div> @enderror
                </div>
                <div class="field">
                    <label for="notes">Catatan Nota</label>
                    <textarea id="notes" name="notes">{{ old('notes') }}</textarea>
                </div>

                <div id="manual-empty" class="manual-empty">Belum ada barang pada nota manual.</div>
                <div id="manual-cart" class="manual-cart"></div>
                @error('items') <div class="error" style="margin-top:10px;">{{ $message }}</div> @enderror

                <div class="summary-stack">
                    <div class="summary-row"><span class="muted">Jumlah item</span><strong id="manual-item-count">0 item</strong></div>
                    <div class="summary-row"><span class="muted">Total nota</span><strong id="manual-total">Rp 0</strong></div>
                </div>

                <div style="display:grid; gap:12px; grid-template-columns:1fr 1fr; margin-top:18px;">
                    <button class="btn btn-primary" type="submit">Simpan Nota</button>
                    <button class="btn" type="button" id="manual-clear">Reset</button>
                </div>
            </form>
        </aside>
    </div>
@endsection

@push('scripts')
<script>
const manualCart = new Map();
const manualCartList = document.querySelector('#manual-cart');
const manualEmpty = document.querySelector('#manual-empty');
const manualSearch = document.querySelector('#manual-search');
const manualCategory = document.querySelector('#manual-category');
const manualCards = Array.from(document.querySelectorAll('.manual-card'));
const formatCurrency = (value) => `Rp ${new Intl.NumberFormat('id-ID').format(value || 0)}`;

function manualPriceForUnit(product, unitType) {
    if (unitType === 'pak') return Number(product.price_per_pack || 0);
    if (unitType === 'lusin') return Number(product.price_per_dozen || 0);
    return Number(product.price_per_unit || 0);
}

function addManualItem(product) {
    const current = manualCart.get(product.id) || { ...product, quantity: 0, unit_type: 'satuan' };
    current.quantity += 1;
    manualCart.set(product.id, current);
    renderManualCart();
}

function renderManualCart() {
    manualCartList.innerHTML = '';
    const items = Array.from(manualCart.values());
    manualEmpty.style.display = items.length ? 'none' : 'block';

    items.forEach((item, index) => {
        const unitPrice = manualPriceForUnit(item, item.unit_type);
        const total = unitPrice * item.quantity;
        const row = document.createElement('div');
        row.className = 'manual-item';
        row.innerHTML = `
            <div>
                <strong>${item.name}</strong>
                <div class="muted">${item.code} | ${item.category}</div>
                <input type="hidden" name="items[${index}][product_id]" value="${item.id}">
            </div>
            <div>
                <label class="muted">Satuan</label>
                <select name="items[${index}][unit_type]" data-id="${item.id}" class="manual-unit-input">
                    <option value="satuan" ${item.unit_type === 'satuan' ? 'selected' : ''}>Satuan</option>
                    <option value="lusin" ${item.unit_type === 'lusin' ? 'selected' : ''}>Lusin</option>
                    <option value="pak" ${item.unit_type === 'pak' ? 'selected' : ''}>Pak</option>
                </select>
            </div>
            <div>
                <label class="muted">Jumlah</label>
                <input type="number" name="items[${index}][quantity]" value="${item.quantity}" min="1" data-id="${item.id}" class="manual-qty-input">
            </div>
            <div class="manual-item-total">
                <div>
                    <div class="muted">Total item</div>
                    <strong>${formatCurrency(total)}</strong>
                </div>
                <button type="button" class="btn btn-danger manual-remove-item" data-id="${item.id}">Hapus</button>
            </div>
        `;
        manualCartList.appendChild(row);
    });

    bindManualCartActions();
    updateManualTotals();
}

function bindManualCartActions() {
    document.querySelectorAll('.manual-qty-input').forEach((input) => {
        input.addEventListener('input', (event) => {
            const id = Number(event.target.dataset.id);
            const item = manualCart.get(id);
            if (!item) return;
            item.quantity = Math.max(Number(event.target.value || 1), 1);
            manualCart.set(id, item);
            renderManualCart();
        });
    });

    document.querySelectorAll('.manual-unit-input').forEach((input) => {
        input.addEventListener('change', (event) => {
            const id = Number(event.target.dataset.id);
            const item = manualCart.get(id);
            if (!item) return;
            item.unit_type = event.target.value;
            manualCart.set(id, item);
            renderManualCart();
        });
    });

    document.querySelectorAll('.manual-remove-item').forEach((button) => {
        button.addEventListener('click', () => {
            manualCart.delete(Number(button.dataset.id));
            renderManualCart();
        });
    });
}

function updateManualTotals() {
    const items = Array.from(manualCart.values());
    const total = items.reduce((sum, item) => sum + (manualPriceForUnit(item, item.unit_type) * item.quantity), 0);
    const itemCount = items.reduce((sum, item) => sum + item.quantity, 0);

    document.querySelector('#manual-item-count').textContent = `${itemCount} item`;
    document.querySelector('#manual-total').textContent = formatCurrency(total);
}

function filterManualProducts() {
    const search = manualSearch.value.trim().toLowerCase();
    const category = manualCategory.value;
    manualCards.forEach((card) => {
        const matchesSearch = card.dataset.search.includes(search);
        const matchesCategory = !category || card.dataset.category === category;
        card.style.display = matchesSearch && matchesCategory ? 'block' : 'none';
    });
}

document.querySelectorAll('.add-manual-product').forEach((button) => {
    button.addEventListener('click', () => addManualItem(JSON.parse(button.dataset.product)));
});

manualSearch.addEventListener('input', filterManualProducts);
manualCategory.addEventListener('input', filterManualProducts);
document.querySelector('#manual-clear').addEventListener('click', () => {
    manualCart.clear();
    renderManualCart();
    document.querySelector('#manual-form').reset();
    updateManualTotals();
});

renderManualCart();
</script>
@endpush
