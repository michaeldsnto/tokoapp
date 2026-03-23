@extends('layouts.app', ['title' => 'Barang', 'heading' => 'Manajemen Barang', 'subheading' => 'Tambah, cari, filter, dan pantau stok barang toko.'])

@section('top_actions')
<a href="{{ route('products.create') }}" class="btn btn-primary">Tambah Barang</a>
@endsection

@section('content')
<div class="panel" style="margin-bottom:18px;">
    <form method="GET" class="form-grid">
        <div class="field">
            <label for="search">Pencarian</label>
            <input type="text" id="search" name="search" value="{{ request('search') }}" placeholder="Cari nama atau kode barang">
        </div>
        <div class="field">
            <label for="category">Kategori</label>
            <select id="category" name="category">
                <option value="">Semua kategori</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" @selected(($filters['categoryId'] ?? null) == $category->id)>{{ $category->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="field">
            <label for="stock">Filter Stok</label>
            <select id="stock" name="stock">
                <option value="">Semua</option>
                <option value="low" @selected(($filters['stock'] ?? '') === 'low')>Stok menipis</option>
            </select>
        </div>
        <div class="field" style="justify-content:end;">
            <button class="btn btn-primary" style="margin-top:32px;">Terapkan Filter</button>
        </div>
    </form>
</div>

<div class="product-grid">
    @forelse($products as $product)
        <div class="product-card">
            <div class="product-image">
                @if($product->image_path)
                    <img src="{{ asset('storage/'.$product->image_path) }}" alt="{{ $product->name }}" style="width:100%; height:100%; object-fit:cover;">
                @else
                    {{ strtoupper(substr($product->name, 0, 2)) }}
                @endif
            </div>
            <div class="product-body">
                <div style="display:flex; justify-content:space-between; gap:8px;">
                    <div><strong>{{ $product->name }}</strong><br><span class="muted">{{ $product->code }}</span></div>
                    @if($product->isLowStock()) <span class="badge warning">Stok Menipis</span> @endif
                </div>
                <div style="margin-top:12px;" class="muted">{{ $product->category->name }}</div>
                <div class="stat-value" style="font-size:1.3rem;">Rp {{ number_format($product->price, 0, ',', '.') }}</div>
                <div class="muted">Stok: {{ $product->stock }}</div>
                <div style="margin-top:16px; display:flex; gap:10px; flex-wrap:wrap;">
                    <a href="{{ route('products.edit', $product) }}" class="btn">Edit</a>
                    <form action="{{ route('products.destroy', $product) }}" method="POST" class="inline" onsubmit="return confirm('Hapus barang ini?')">
                        @csrf @method('DELETE')
                        <button class="btn btn-danger" type="submit">Hapus</button>
                    </form>
                </div>
            </div>
        </div>
    @empty
        <div class="panel">Belum ada data barang.</div>
    @endforelse
</div>

<div class="pagination">{{ $products->links() }}</div>
@endsection
