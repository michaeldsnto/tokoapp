@csrf
<div class="form-grid">
    <div class="field">
        <label for="name">Nama Barang</label>
        <input type="text" id="name" name="name" value="{{ old('name', $product->name) }}" required>
        @error('name') <div class="error">{{ $message }}</div> @enderror
    </div>
    <div class="field">
        <label for="code">Kode / Barcode</label>
        <input type="text" id="code" name="code" value="{{ old('code', $product->code) }}" required>
        @error('code') <div class="error">{{ $message }}</div> @enderror
    </div>
    <div class="field">
        <label for="category_id">Kategori</label>
        <select id="category_id" name="category_id" required>
            <option value="">Pilih kategori</option>
            @foreach($categories as $category)
                <option value="{{ $category->id }}" @selected(old('category_id', $product->category_id) == $category->id)>{{ $category->name }}</option>
            @endforeach
        </select>
        @error('category_id') <div class="error">{{ $message }}</div> @enderror
    </div>
    <div class="field">
        <label for="price">Harga</label>
        <input type="number" id="price" name="price" min="0" step="0.01" value="{{ old('price', $product->price) }}" required>
        @error('price') <div class="error">{{ $message }}</div> @enderror
    </div>
    <div class="field">
        <label for="stock">Stok</label>
        <input type="number" id="stock" name="stock" min="0" value="{{ old('stock', $product->stock ?? 0) }}" required>
        @error('stock') <div class="error">{{ $message }}</div> @enderror
    </div>
    <div class="field">
        <label for="low_stock_threshold">Batas Stok Menipis</label>
        <input type="number" id="low_stock_threshold" name="low_stock_threshold" min="0" value="{{ old('low_stock_threshold', $product->low_stock_threshold ?? 5) }}" required>
        @error('low_stock_threshold') <div class="error">{{ $message }}</div> @enderror
    </div>
    <div class="field">
        <label for="image">Gambar</label>
        <input type="file" id="image" name="image" accept="image/*">
        @error('image') <div class="error">{{ $message }}</div> @enderror
    </div>
    <div class="field" style="justify-content:end;">
        <label style="display:flex; align-items:center; gap:10px; margin-top:34px;">
            <input type="checkbox" name="is_active" value="1" style="width:auto;" @checked(old('is_active', $product->exists ? $product->is_active : true))>
            <span>Barang aktif</span>
        </label>
    </div>
</div>
<div style="margin-top:20px; display:flex; gap:10px;">
    <button class="btn btn-primary" type="submit">{{ $submitLabel }}</button>
    <a href="{{ route('products.index') }}" class="btn">Batal</a>
</div>
