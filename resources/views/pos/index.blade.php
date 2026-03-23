@extends('layouts.app', ['title' => 'POS', 'heading' => 'Point of Sale', 'subheading' => 'Transaksi cepat dengan harga berbeda untuk satuan, pak, dan lusin.'])

@section('top_actions')
    <span class="badge">Multi Unit Pricing</span>
    <span class="badge" id="product-counter">{{ $products->count() }} SKU</span>
@endsection

@section('content')
    <style>
        .pos-shell { display:grid; gap:20px; grid-template-columns:minmax(0,1.35fr) minmax(380px,0.9fr); align-items:start; }
        .pos-stage { position:relative; overflow:hidden; background:radial-gradient(circle at top left, rgba(34,197,94,.22), transparent 28%), radial-gradient(circle at bottom right, rgba(14,165,233,.18), transparent 34%), var(--surface); }
        .pos-hero { display:grid; gap:18px; grid-template-columns:1.15fr .85fr; position:relative; z-index:1; }
        .hero-copy h2 { font-size:2rem; margin:10px 0 12px; line-height:1.1; }
        .hero-copy p { margin:0; }
        .hero-metrics { display:grid; gap:14px; grid-template-columns:repeat(2, minmax(0,1fr)); }
        .metric-tile { padding:18px; border-radius:18px; border:1px solid var(--border); background:rgba(255,255,255,.10); }
        .metric-tile strong { display:block; font-size:1.5rem; margin-top:10px; }
        .toolbar-grid { display:grid; gap:16px; grid-template-columns:minmax(0,1fr) 220px 170px; margin-top:22px; }
        .toolbar-card { padding:18px; border-radius:18px; border:1px solid var(--border); background:var(--surface-strong); }
        .inventory-grid { display:grid; gap:18px; grid-template-columns:repeat(auto-fit, minmax(235px,1fr)); margin-top:22px; }
        .inventory-card { border:1px solid var(--border); border-radius:22px; background:linear-gradient(180deg, rgba(255,255,255,.22), rgba(255,255,255,.06)); overflow:hidden; text-align:left; cursor:pointer; transition:transform .18s ease, box-shadow .18s ease, border-color .18s ease; }
        .inventory-card:hover { transform:translateY(-4px); box-shadow:0 24px 44px rgba(15,23,42,.14); border-color:rgba(34,197,94,.28); }
        .inventory-card-header { padding:20px 20px 12px; display:flex; justify-content:space-between; gap:12px; align-items:center; }
        .inventory-card-avatar { width:54px; height:54px; border-radius:18px; display:grid; place-items:center; background:linear-gradient(135deg, rgba(34,197,94,.18), rgba(14,165,233,.18)); font-weight:900; font-size:1.1rem; }
        .inventory-card-body { padding:0 20px 20px; }
        .inventory-name { font-weight:800; font-size:1.05rem; margin-bottom:6px; }
        .inventory-meta { display:flex; flex-wrap:wrap; gap:8px; margin:12px 0 14px; }
        .pill { display:inline-flex; align-items:center; padding:6px 10px; border-radius:999px; font-size:.83rem; background:rgba(148,163,184,.12); color:var(--muted); }
        .unit-price-list { display:grid; gap:8px; }
        .unit-price-row { display:flex; justify-content:space-between; gap:10px; padding:10px 12px; border-radius:14px; background:rgba(148,163,184,.08); }
        .unit-price-row strong { font-size:.95rem; }
        .checkout-panel { position:sticky; top:24px; padding:0; overflow:hidden; }
        .checkout-head { padding:24px; background:linear-gradient(135deg, rgba(22,101,52,.98), rgba(21,94,117,.96)); color:#fff; }
        .checkout-body { padding:22px; }
        .cart-list { display:grid; gap:12px; margin-bottom:18px; }
        .cart-item { display:grid; gap:14px; grid-template-columns:minmax(0,1.25fr) 120px 90px; padding:16px; border-radius:18px; border:1px solid var(--border); background:var(--surface-strong); }
        .cart-item-main strong { display:block; margin-bottom:5px; }
        .cart-item-meta { color:var(--muted); font-size:.9rem; }
        .cart-item-side { display:grid; gap:8px; }
        .cart-item-total { display:flex; justify-content:space-between; align-items:center; padding-top:10px; border-top:1px dashed var(--border); grid-column:1 / -1; }
        .cart-empty { padding:24px; text-align:center; border:1px dashed var(--border); border-radius:18px; color:var(--muted); background:rgba(148,163,184,.04); }
        .summary-stack { margin-top:18px; padding:18px; border-radius:20px; background:linear-gradient(180deg, rgba(15,23,42,.06), rgba(15,23,42,.02)); border:1px solid var(--border); }
        .summary-row { display:flex; justify-content:space-between; gap:12px; margin-bottom:10px; }
        .change-highlight { margin-top:16px; padding:18px; border-radius:18px; background:rgba(34,197,94,.10); border:1px solid rgba(34,197,94,.22); }
        .change-highlight strong { display:block; font-size:1.6rem; margin-top:6px; }
        .pos-actions { display:grid; gap:12px; grid-template-columns:1fr 1fr; margin-top:18px; }
        @media (max-width:1200px) { .pos-shell,.pos-hero,.toolbar-grid,.pos-actions { grid-template-columns:1fr; } .checkout-panel { position:static; } }
        @media (max-width:768px) { .inventory-grid { grid-template-columns:1fr; } .hero-copy h2 { font-size:1.45rem; } .cart-item,.pos-actions,.hero-metrics { grid-template-columns:1fr; } .checkout-head,.checkout-body,.toolbar-card { padding:16px; } .cart-item-total { gap:10px; align-items:flex-start; flex-direction:column; } }
    </style>

    <div class="pos-shell">
        <section class="panel pos-stage">
            <div class="pos-hero">
                <div class="hero-copy">
                    <span class="badge">Cashier Workspace</span>
                    <h2>Jual produk dengan harga berbeda berdasarkan satuan belinya.</h2>
                    <p class="muted">Kasir bisa memilih satuan, pak, atau lusin langsung di keranjang tanpa perlu mengubah data barang.</p>
                </div>
                <div class="hero-metrics">
                    <div class="metric-tile"><div class="muted">Produk tersedia</div><strong>{{ $products->count() }}</strong></div>
                    <div class="metric-tile"><div class="muted">Kategori aktif</div><strong>{{ $products->pluck('category.name')->unique()->count() }}</strong></div>
                    <div class="metric-tile"><div class="muted">Mode jual</div><strong>3 Unit</strong></div>
                    <div class="metric-tile"><div class="muted">Urutan harga</div><strong>Pak > Lusin</strong></div>
                </div>
            </div>

            <div class="toolbar-grid">
                <div class="toolbar-card">
                    <label for="product-search">Cari barang</label>
                    <input type="text" id="product-search" placeholder="Cari nama, kode, atau kategori">
                </div>
                <div class="toolbar-card">
                    <label for="category-filter">Filter kategori</label>
                    <select id="category-filter">
                        <option value="">Semua kategori</option>
                        @foreach($products->pluck('category.name')->unique()->sort()->values() as $categoryName)
                            <option value="{{ $categoryName }}">{{ $categoryName }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="toolbar-card">
                    <label for="barcode-input">Scan barcode</label>
                    <input type="text" id="barcode-input" placeholder="Scan lalu Enter">
                </div>
            </div>

            <div class="inventory-grid" id="inventory-grid">
                @foreach($products as $product)
                    <button type="button" class="inventory-card add-product" data-search="{{ strtolower($product->name.' '.$product->code.' '.$product->category->name) }}" data-category="{{ $product->category->name }}" data-product='{{ json_encode(['id' => $product->id, 'name' => $product->name, 'code' => $product->code, 'price_per_unit' => (float) $product->price_per_unit, 'price_per_pack' => (float) $product->price_per_pack, 'price_per_dozen' => (float) $product->price_per_dozen, 'category' => $product->category->name]) }}'>
                        <div class="inventory-card-header">
                            <div class="inventory-card-avatar">{{ strtoupper(substr($product->name, 0, 2)) }}</div>
                            <span class="badge">3 Harga</span>
                        </div>
                        <div class="inventory-card-body">
                            <div class="inventory-name">{{ $product->name }}</div>
                            <div class="muted">{{ $product->code }}</div>
                            <div class="inventory-meta"><span class="pill">{{ $product->category->name }}</span></div>
                            <div class="unit-price-list">
                                <div class="unit-price-row"><span>Satuan</span><strong>Rp {{ number_format($product->price_per_unit, 0, ',', '.') }}</strong></div>
                                <div class="unit-price-row"><span>Lusin</span><strong>Rp {{ number_format($product->price_per_dozen, 0, ',', '.') }}</strong></div>
                                <div class="unit-price-row"><span>Pak</span><strong>Rp {{ number_format($product->price_per_pack, 0, ',', '.') }}</strong></div>
                            </div>
                        </div>
                    </button>
                @endforeach
            </div>
        </section>

        <aside class="panel checkout-panel">
            <div class="checkout-head">
                <div class="badge" style="background: rgba(255,255,255,0.16); color: white;">Live Checkout</div>
                <h3>Keranjang Kasir</h3>
                <p style="margin:10px 0 0; color:rgba(255,255,255,.78);">Pilih unit jual tiap barang dan total akan menyesuaikan otomatis.</p>
            </div>

            <div class="checkout-body">
                <form action="{{ route('pos.store') }}" method="POST" id="pos-form">
                    @csrf
                    <div id="cart-empty" class="cart-empty">Belum ada barang di keranjang.</div>
                    <div id="cart-list" class="cart-list"></div>

                    <div class="field">
                        <label for="discount_amount">Diskon</label>
                        <input type="number" id="discount_amount" name="discount_amount" min="0" value="0" step="0.01">
                    </div>

                    <div class="field">
                        <label for="paid_amount">Bayar</label>
                        <input type="number" id="paid_amount" name="paid_amount" min="0" step="0.01" required>
                        @error('paid_amount') <div class="error">{{ $message }}</div> @enderror
                    </div>

                    <div class="field">
                        <label for="notes">Catatan Transaksi</label>
                        <textarea id="notes" name="notes" placeholder="Opsional">{{ old('notes') }}</textarea>
                    </div>

                    <div class="summary-stack">
                        <div class="summary-row"><span class="muted">Jumlah item</span><strong id="item-count-text">0 item</strong></div>
                        <div class="summary-row"><span class="muted">Subtotal</span><strong id="subtotal-text">Rp 0</strong></div>
                        <div class="summary-row"><span class="muted">Total bayar</span><strong id="total-text">Rp 0</strong></div>
                    </div>

                    <div class="change-highlight"><div class="muted">Kembalian</div><strong id="change-text">Rp 0</strong></div>
                    @error('items') <div class="error" style="margin-top:10px;">{{ $message }}</div> @enderror

                    <div class="pos-actions">
                        <button class="btn btn-primary" type="submit">Simpan Transaksi</button>
                        <button class="btn" type="button" id="clear-cart">Reset Keranjang</button>
                    </div>
                </form>
            </div>
        </aside>
    </div>
@endsection

@push('scripts')
<script>
const cart = new Map();
const cartList = document.querySelector('#cart-list');
const emptyState = document.querySelector('#cart-empty');
const discountInput = document.querySelector('#discount_amount');
const paidInput = document.querySelector('#paid_amount');
const barcodeInput = document.querySelector('#barcode-input');
const searchInput = document.querySelector('#product-search');
const categoryFilter = document.querySelector('#category-filter');
const inventoryCards = Array.from(document.querySelectorAll('.inventory-card'));

const formatCurrency = (value) => `Rp ${new Intl.NumberFormat('id-ID').format(value || 0)}`;

function priceForUnit(product, unitType) {
    if (unitType === 'pak') return Number(product.price_per_pack || 0);
    if (unitType === 'lusin') return Number(product.price_per_dozen || 0);
    return Number(product.price_per_unit || 0);
}

function addItem(product) {
    const current = cart.get(product.id) || { ...product, quantity: 0, unit_type: 'satuan' };
    current.quantity += 1;
    cart.set(product.id, current);
    renderCart();
}

function renderCart() {
    cartList.innerHTML = '';
    const items = Array.from(cart.values());
    emptyState.style.display = items.length ? 'none' : 'block';

    items.forEach((item, index) => {
        const row = document.createElement('div');
        const unitPrice = priceForUnit(item, item.unit_type);
        const total = item.quantity * unitPrice;
        row.className = 'cart-item';
        row.innerHTML = `
            <div class="cart-item-main">
                <strong>${item.name}</strong>
                <div class="cart-item-meta">${item.code} | ${item.category}</div>
                <input type="hidden" name="items[${index}][product_id]" value="${item.id}">
            </div>
            <div class="cart-item-side">
                <label class="muted">Satuan</label>
                <select name="items[${index}][unit_type]" data-id="${item.id}" class="unit-input">
                    <option value="satuan" ${item.unit_type === 'satuan' ? 'selected' : ''}>Satuan</option>
                    <option value="lusin" ${item.unit_type === 'lusin' ? 'selected' : ''}>Lusin</option>
                    <option value="pak" ${item.unit_type === 'pak' ? 'selected' : ''}>Pak</option>
                </select>
            </div>
            <div class="cart-item-side">
                <label class="muted">Jumlah</label>
                <input type="number" name="items[${index}][quantity]" value="${item.quantity}" min="1" data-id="${item.id}" class="qty-input">
            </div>
            <div class="cart-item-total">
                <div>
                    <div class="muted">Harga ${item.unit_type}</div>
                    <strong>${formatCurrency(unitPrice)}</strong>
                </div>
                <div style="text-align:right;">
                    <div class="muted">Line total</div>
                    <strong>${formatCurrency(total)}</strong>
                </div>
                <button type="button" class="btn btn-danger remove-item" data-id="${item.id}">Hapus</button>
            </div>
        `;
        cartList.appendChild(row);
    });

    bindCartActions();
    updateTotals();
}

function updateTotals() {
    const items = Array.from(cart.values());
    const subtotal = items.reduce((sum, item) => sum + (priceForUnit(item, item.unit_type) * item.quantity), 0);
    const itemCount = items.reduce((sum, item) => sum + item.quantity, 0);
    const discount = Number(discountInput.value || 0);
    const total = Math.max(subtotal - discount, 0);
    const paid = Number(paidInput.value || 0);
    const change = Math.max(paid - total, 0);

    document.querySelector('#item-count-text').textContent = `${itemCount} item`;
    document.querySelector('#subtotal-text').textContent = formatCurrency(subtotal);
    document.querySelector('#total-text').textContent = formatCurrency(total);
    document.querySelector('#change-text').textContent = formatCurrency(change);
}

function bindCartActions() {
    document.querySelectorAll('.qty-input').forEach((input) => {
        input.addEventListener('input', (event) => {
            const id = Number(event.target.dataset.id);
            const item = cart.get(id);
            const qty = Number(event.target.value || 1);
            if (!item) return;
            item.quantity = Math.max(qty, 1);
            cart.set(id, item);
            renderCart();
        });
    });

    document.querySelectorAll('.unit-input').forEach((select) => {
        select.addEventListener('change', (event) => {
            const id = Number(event.target.dataset.id);
            const item = cart.get(id);
            if (!item) return;
            item.unit_type = event.target.value;
            cart.set(id, item);
            renderCart();
        });
    });

    document.querySelectorAll('.remove-item').forEach((button) => {
        button.addEventListener('click', () => {
            cart.delete(Number(button.dataset.id));
            renderCart();
        });
    });
}

function filterInventory() {
    const search = searchInput.value.trim().toLowerCase();
    const category = categoryFilter.value;
    inventoryCards.forEach((card) => {
        const matchesSearch = card.dataset.search.includes(search);
        const matchesCategory = !category || card.dataset.category === category;
        card.style.display = matchesSearch && matchesCategory ? 'block' : 'none';
    });
}

document.querySelectorAll('.add-product').forEach((button) => {
    button.addEventListener('click', () => addItem(JSON.parse(button.dataset.product)));
});

[discountInput, paidInput].forEach((input) => input.addEventListener('input', updateTotals));
[searchInput, categoryFilter].forEach((input) => input.addEventListener('input', filterInventory));

document.querySelector('#clear-cart').addEventListener('click', () => {
    cart.clear();
    renderCart();
    document.querySelector('#pos-form').reset();
    discountInput.value = 0;
    updateTotals();
});

barcodeInput.addEventListener('keydown', async (event) => {
    if (event.key !== 'Enter') return;
    event.preventDefault();
    const code = barcodeInput.value.trim();
    if (!code) return;

    const response = await fetch(`{{ route('pos.lookup') }}?code=${encodeURIComponent(code)}`);
    if (!response.ok) {
        alert('Barcode tidak ditemukan.');
        return;
    }

    const product = await response.json();
    addItem(product);
    barcodeInput.value = '';
});

renderCart();
</script>
@endpush
