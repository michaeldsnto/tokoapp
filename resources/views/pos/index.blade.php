@extends('layouts.app', ['title' => 'POS', 'heading' => 'Point of Sale', 'subheading' => 'Pilih barang, atur jumlah beli, hitung diskon, lalu simpan transaksi.'])

@section('content')
<div class="cart-layout">
    <div class="panel">
        <div class="field full" style="margin-bottom:18px;">
            <label for="barcode-input">Scan Barcode / Cari Kode</label>
            <input type="text" id="barcode-input" placeholder="Scan barcode lalu tekan Enter">
            <div class="muted">Input ini kompatibel dengan barcode scanner keyboard wedge.</div>
        </div>
        <div class="product-grid">
            @foreach($products as $product)
                <button type="button" class="product-card add-product" data-product='@json(["id" => $product->id, "name" => $product->name, "code" => $product->code, "price" => (float) $product->price, "stock" => $product->stock])' style="text-align:left; cursor:pointer;">
                    <div class="product-image">{{ strtoupper(substr($product->name, 0, 2)) }}</div>
                    <div class="product-body">
                        <strong>{{ $product->name }}</strong><br>
                        <span class="muted">{{ $product->code }} • {{ $product->category->name }}</span>
                        <div class="stat-value" style="font-size:1.15rem;">Rp {{ number_format($product->price, 0, ',', '.') }}</div>
                        <div class="muted">Stok {{ $product->stock }}</div>
                    </div>
                </button>
            @endforeach
        </div>
    </div>
    <div class="panel">
        <form action="{{ route('pos.store') }}" method="POST" id="pos-form">
            @csrf
            <h3>Keranjang</h3>
            <p class="muted">Klik barang atau scan barcode untuk menambahkan ke transaksi.</p>
            <div id="cart-empty" class="muted">Belum ada barang di keranjang.</div>
            <div class="table-wrap">
                <table id="cart-table" style="display:none;">
                    <thead><tr><th>Barang</th><th>Qty</th><th>Total</th><th></th></tr></thead>
                    <tbody></tbody>
                </table>
            </div>
            <div class="field" style="margin-top:18px;">
                <label for="discount_amount">Diskon</label>
                <input type="number" id="discount_amount" name="discount_amount" min="0" value="0" step="0.01">
            </div>
            <div class="field">
                <label for="paid_amount">Bayar</label>
                <input type="number" id="paid_amount" name="paid_amount" min="0" step="0.01" required>
                @error('paid_amount') <div class="error">{{ $message }}</div> @enderror
            </div>
            <div class="field">
                <label for="notes">Catatan</label>
                <textarea id="notes" name="notes" placeholder="Opsional">{{ old('notes') }}</textarea>
            </div>
            <div class="panel" style="padding:16px; margin-top:18px; background:var(--surface-strong);">
                <div style="display:flex; justify-content:space-between; margin-bottom:8px;"><span class="muted">Subtotal</span><strong id="subtotal-text">Rp 0</strong></div>
                <div style="display:flex; justify-content:space-between; margin-bottom:8px;"><span class="muted">Total</span><strong id="total-text">Rp 0</strong></div>
                <div style="display:flex; justify-content:space-between;"><span class="muted">Kembalian</span><strong id="change-text">Rp 0</strong></div>
            </div>
            @error('items') <div class="error" style="margin-top:10px;">{{ $message }}</div> @enderror
            <div style="display:flex; gap:10px; margin-top:18px;">
                <button class="btn btn-primary" type="submit">Simpan Transaksi</button>
                <button class="btn" type="button" id="clear-cart">Reset</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
const cart=new Map(),cartTable=document.querySelector('#cart-table'),cartBody=cartTable.querySelector('tbody'),emptyState=document.querySelector('#cart-empty'),discountInput=document.querySelector('#discount_amount'),paidInput=document.querySelector('#paid_amount'),barcodeInput=document.querySelector('#barcode-input');
const formatCurrency=(value)=>`Rp ${new Intl.NumberFormat('id-ID').format(value||0)}`;
function addItem(product){const current=cart.get(product.id)||{...product,quantity:0}; if(current.quantity>=product.stock){alert(`Stok ${product.name} tidak mencukupi.`); return;} current.quantity+=1; cart.set(product.id,current); renderCart();}
function renderCart(){cartBody.innerHTML=''; const items=Array.from(cart.values()); emptyState.style.display=items.length?'none':'block'; cartTable.style.display=items.length?'table':'none'; items.forEach((item,index)=>{const tr=document.createElement('tr'); const total=item.quantity*item.price; tr.innerHTML=`<td><strong>${item.name}</strong><br><span class="muted">${item.code}</span><input type="hidden" name="items[${index}][product_id]" value="${item.id}"></td><td style="min-width:110px;"><input type="number" name="items[${index}][quantity]" value="${item.quantity}" min="1" max="${item.stock}" data-id="${item.id}" class="qty-input"></td><td>${formatCurrency(total)}</td><td><button type="button" class="btn btn-danger remove-item" data-id="${item.id}">X</button></td>`; cartBody.appendChild(tr);}); bindCartActions(); updateTotals();}
function updateTotals(){const subtotal=Array.from(cart.values()).reduce((sum,item)=>sum+(item.price*item.quantity),0),discount=Number(discountInput.value||0),total=Math.max(subtotal-discount,0),paid=Number(paidInput.value||0),change=Math.max(paid-total,0); document.querySelector('#subtotal-text').textContent=formatCurrency(subtotal); document.querySelector('#total-text').textContent=formatCurrency(total); document.querySelector('#change-text').textContent=formatCurrency(change);}
function bindCartActions(){document.querySelectorAll('.qty-input').forEach((input)=>{input.addEventListener('input',(event)=>{const id=Number(event.target.dataset.id),item=cart.get(id),qty=Number(event.target.value||1); if(!item)return; item.quantity=Math.min(Math.max(qty,1),item.stock); cart.set(id,item); renderCart();});}); document.querySelectorAll('.remove-item').forEach((button)=>{button.addEventListener('click',()=>{cart.delete(Number(button.dataset.id)); renderCart();});});}
document.querySelectorAll('.add-product').forEach((button)=>button.addEventListener('click',()=>addItem(JSON.parse(button.dataset.product))));
[discountInput,paidInput].forEach((input)=>input.addEventListener('input',updateTotals));
document.querySelector('#clear-cart').addEventListener('click',()=>{cart.clear(); renderCart(); document.querySelector('#pos-form').reset(); discountInput.value=0; updateTotals();});
barcodeInput.addEventListener('keydown',async(event)=>{if(event.key!=='Enter')return; event.preventDefault(); const code=barcodeInput.value.trim(); if(!code)return; const response=await fetch(`{{ route('pos.lookup') }}?code=${encodeURIComponent(code)}`); if(!response.ok){alert('Barcode tidak ditemukan.'); return;} const product=await response.json(); addItem(product); barcodeInput.value='';});
renderCart();
</script>
@endpush
