@extends('layouts.app', ['title' => 'POS', 'heading' => 'Point of Sale', 'subheading' => 'Kasir cepat dengan tampilan premium, barcode ready, dan checkout yang nyaman di layar tablet maupun laptop.'])

@section('top_actions')
    <span class="badge">Scanner Ready</span>
    <span class="badge" id="product-counter">{{ $products->count() }} SKU</span>
@endsection

@section('content')
    <style>
        .pos-shell {
            display: grid;
            gap: 20px;
            grid-template-columns: minmax(0, 1.35fr) minmax(360px, 0.9fr);
            align-items: start;
        }
        .pos-stage {
            position: relative;
            overflow: hidden;
            background:
                radial-gradient(circle at top left, rgba(34, 197, 94, 0.22), transparent 28%),
                radial-gradient(circle at bottom right, rgba(14, 165, 233, 0.18), transparent 34%),
                var(--surface);
        }
        .pos-stage::after {
            content: "";
            position: absolute;
            inset: auto -70px -70px auto;
            width: 180px;
            height: 180px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.08);
            filter: blur(4px);
        }
        .pos-hero {
            display: grid;
            gap: 18px;
            grid-template-columns: 1.15fr 0.85fr;
            position: relative;
            z-index: 1;
        }
        .hero-copy h2 {
            font-size: 2rem;
            margin: 10px 0 12px;
            line-height: 1.1;
        }
        .hero-copy p { margin: 0; max-width: 52ch; }
        .hero-metrics {
            display: grid;
            gap: 14px;
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
        .metric-tile {
            padding: 18px;
            border-radius: 18px;
            border: 1px solid var(--border);
            background: rgba(255, 255, 255, 0.10);
            backdrop-filter: blur(12px);
        }
        .metric-tile strong {
            display: block;
            font-size: 1.5rem;
            margin-top: 10px;
        }
        .toolbar-grid {
            display: grid;
            gap: 16px;
            grid-template-columns: minmax(0, 1fr) 220px 170px;
            margin-top: 22px;
        }
        .toolbar-card {
            padding: 18px;
            border-radius: 18px;
            border: 1px solid var(--border);
            background: var(--surface-strong);
        }
        .toolbar-card input,
        .toolbar-card select {
            background: transparent;
        }
        .inventory-grid {
            display: grid;
            gap: 18px;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            margin-top: 22px;
        }
        .inventory-card {
            position: relative;
            border: 1px solid var(--border);
            border-radius: 22px;
            background: linear-gradient(180deg, rgba(255, 255, 255, 0.22), rgba(255, 255, 255, 0.06));
            overflow: hidden;
            text-align: left;
            cursor: pointer;
            transition: transform .18s ease, box-shadow .18s ease, border-color .18s ease;
        }
        .inventory-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 24px 44px rgba(15, 23, 42, 0.14);
            border-color: rgba(34, 197, 94, 0.28);
        }
        .inventory-card-header {
            padding: 20px 20px 12px;
            display: flex;
            justify-content: space-between;
            gap: 12px;
        }
        .inventory-card-avatar {
            width: 54px;
            height: 54px;
            border-radius: 18px;
            display: grid;
            place-items: center;
            background: linear-gradient(135deg, rgba(34, 197, 94, 0.18), rgba(14, 165, 233, 0.18));
            font-weight: 900;
            font-size: 1.1rem;
        }
        .inventory-card-body {
            padding: 0 20px 20px;
        }
        .inventory-name {
            font-weight: 800;
            font-size: 1.05rem;
            margin-bottom: 6px;
        }
        .inventory-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin: 12px 0;
        }
        .pill {
            display: inline-flex;
            align-items: center;
            padding: 6px 10px;
            border-radius: 999px;
            font-size: 0.83rem;
            background: rgba(148, 163, 184, 0.12);
            color: var(--muted);
        }
        .inventory-price {
            font-size: 1.25rem;
            font-weight: 900;
            margin: 14px 0 6px;
        }
        .checkout-panel {
            position: sticky;
            top: 24px;
            padding: 0;
            overflow: hidden;
        }
        .checkout-head {
            padding: 24px;
            background: linear-gradient(135deg, rgba(22, 101, 52, 0.98), rgba(21, 94, 117, 0.96));
            color: white;
        }
        .checkout-head h3 { margin: 0; font-size: 1.5rem; }
        .checkout-body { padding: 22px; }
        .cart-list {
            display: grid;
            gap: 12px;
            margin-bottom: 18px;
        }
        .cart-item {
            display: grid;
            gap: 12px;
            grid-template-columns: minmax(0, 1fr) 90px auto;
            align-items: center;
            padding: 14px;
            border-radius: 18px;
            border: 1px solid var(--border);
            background: var(--surface-strong);
        }
        .cart-item strong { display: block; margin-bottom: 4px; }
        .cart-empty {
            padding: 24px;
            text-align: center;
            border: 1px dashed var(--border);
            border-radius: 18px;
            color: var(--muted);
            background: rgba(148, 163, 184, 0.04);
        }
        .summary-stack {
            margin-top: 18px;
            padding: 18px;
            border-radius: 20px;
            background: linear-gradient(180deg, rgba(15, 23, 42, 0.06), rgba(15, 23, 42, 0.02));
            border: 1px solid var(--border);
        }
        .summary-row {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 10px;
        }
        .summary-row:last-child { margin-bottom: 0; }
        .change-highlight {
            margin-top: 16px;
            padding: 18px;
            border-radius: 18px;
            background: rgba(34, 197, 94, 0.10);
            border: 1px solid rgba(34, 197, 94, 0.22);
        }
        .change-highlight strong {
            display: block;
            font-size: 1.6rem;
            margin-top: 6px;
        }
        .pos-actions {
            display: grid;
            gap: 12px;
            grid-template-columns: 1fr 1fr;
            margin-top: 18px;
        }
        .hidden-row { display: none; }

        @media (max-width: 1200px) {
            .pos-shell, .pos-hero, .toolbar-grid, .pos-actions {
                grid-template-columns: 1fr;
            }
            .checkout-panel { position: static; }
        }
    </style>

    <div class="pos-shell">
        <section class="panel pos-stage">
            <div class="pos-hero">
                <div class="hero-copy">
                    <span class="badge">Cashier Workspace</span>
                    <h2>Transaksi cepat, visual rapi, dan siap dipakai di jam ramai.</h2>
                    <p class="muted">Cari produk, scan barcode, dan lihat total belanja berubah secara instan tanpa membuat kasir kehilangan fokus.</p>
                </div>
                <div class="hero-metrics">
                    <div class="metric-tile">
                        <div class="muted">Produk tersedia</div>
                        <strong>{{ $products->count() }}</strong>
                    </div>
                    <div class="metric-tile">
                        <div class="muted">Kategori aktif</div>
                        <strong>{{ $products->pluck('category.name')->unique()->count() }}</strong>
                    </div>
                    <div class="metric-tile">
                        <div class="muted">Stok menipis</div>
                        <strong>{{ $products->filter(fn ($product) => $product->isLowStock())->count() }}</strong>
                    </div>
                    <div class="metric-tile">
                        <div class="muted">Mode input</div>
                        <strong>Barcode</strong>
                    </div>
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
                    <button
                        type="button"
                        class="inventory-card add-product"
                        data-search="{{ strtolower($product->name.' '.$product->code.' '.$product->category->name) }}"
                        data-category="{{ $product->category->name }}"
                        data-product='@json(["id" => $product->id, "name" => $product->name, "code" => $product->code, "price" => (float) $product->price, "stock" => $product->stock, "category" => $product->category->name])'
                    >
                        <div class="inventory-card-header">
                            <div class="inventory-card-avatar">{{ strtoupper(substr($product->name, 0, 2)) }}</div>
                            @if($product->isLowStock())
                                <span class="badge warning">Restock</span>
                            @else
                                <span class="badge">Ready</span>
                            @endif
                        </div>
                        <div class="inventory-card-body">
                            <div class="inventory-name">{{ $product->name }}</div>
                            <div class="muted">{{ $product->code }}</div>
                            <div class="inventory-meta">
                                <span class="pill">{{ $product->category->name }}</span>
                                <span class="pill">Stok {{ $product->stock }}</span>
                            </div>
                            <div class="inventory-price">Rp {{ number_format($product->price, 0, ',', '.') }}</div>
                            <div class="muted">Klik untuk tambah ke keranjang</div>
                        </div>
                    </button>
                @endforeach
            </div>
        </section>

        <aside class="panel checkout-panel">
            <div class="checkout-head">
                <div class="badge" style="background: rgba(255,255,255,0.16); color: white;">Live Checkout</div>
                <h3>Keranjang Kasir</h3>
                <p style="margin: 10px 0 0; color: rgba(255,255,255,0.78);">Hitung total, diskon, dan kembalian secara otomatis sebelum menyimpan transaksi.</p>
            </div>

            <div class="checkout-body">
                <form action="{{ route('pos.store') }}" method="POST" id="pos-form">
                    @csrf

                    <div id="cart-empty" class="cart-empty">
                        Belum ada barang di keranjang.
                    </div>
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
                        <textarea id="notes" name="notes" placeholder="Opsional. Misalnya: pelanggan langganan atau catatan pesanan.">{{ old('notes') }}</textarea>
                    </div>

                    <div class="summary-stack">
                        <div class="summary-row">
                            <span class="muted">Jumlah item</span>
                            <strong id="item-count-text">0 item</strong>
                        </div>
                        <div class="summary-row">
                            <span class="muted">Subtotal</span>
                            <strong id="subtotal-text">Rp 0</strong>
                        </div>
                        <div class="summary-row">
                            <span class="muted">Total bayar</span>
                            <strong id="total-text">Rp 0</strong>
                        </div>
                    </div>

                    <div class="change-highlight">
                        <div class="muted">Kembalian</div>
                        <strong id="change-text">Rp 0</strong>
                    </div>

                    @error('items') <div class="error" style="margin-top: 10px;">{{ $message }}</div> @enderror

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

function addItem(product) {
    const current = cart.get(product.id) || { ...product, quantity: 0 };
    if (current.quantity >= product.stock) {
        alert(`Stok ${product.name} tidak mencukupi.`);
        return;
    }

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
        const total = item.quantity * item.price;

        row.className = 'cart-item';
        row.innerHTML = `
            <div>
                <strong>${item.name}</strong>
                <div class="muted">${item.code} · ${item.category}</div>
                <input type="hidden" name="items[${index}][product_id]" value="${item.id}">
            </div>
            <div>
                <input type="number" name="items[${index}][quantity]" value="${item.quantity}" min="1" max="${item.stock}" data-id="${item.id}" class="qty-input">
            </div>
            <div style="display:flex; flex-direction:column; gap:8px; align-items:end;">
                <strong>${formatCurrency(total)}</strong>
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
    const subtotal = items.reduce((sum, item) => sum + (item.price * item.quantity), 0);
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

            if (!item) {
                return;
            }

            item.quantity = Math.min(Math.max(qty, 1), item.stock);
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
    if (event.key !== 'Enter') {
        return;
    }

    event.preventDefault();
    const code = barcodeInput.value.trim();

    if (!code) {
        return;
    }

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
