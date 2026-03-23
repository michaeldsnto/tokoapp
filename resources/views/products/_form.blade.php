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
        <label for="price_per_unit">Harga Satuan</label>
        <input type="number" id="price_per_unit" name="price_per_unit" min="0" step="0.01" value="{{ old('price_per_unit', $product->price_per_unit ?? $product->price) }}" required>
        @error('price_per_unit') <div class="error">{{ $message }}</div> @enderror
    </div>
    <div class="field">
        <label for="price_per_pack">Harga Pak</label>
        <input type="number" id="price_per_pack" name="price_per_pack" min="0" step="0.01" value="{{ old('price_per_pack', $product->price_per_pack ?? 0) }}" required>
        @error('price_per_pack') <div class="error">{{ $message }}</div> @enderror
    </div>
    <div class="field">
        <label for="price_per_dozen">Harga Lusin</label>
        <input type="number" id="price_per_dozen" name="price_per_dozen" min="0" step="0.01" value="{{ old('price_per_dozen', $product->price_per_dozen ?? 0) }}" required>
        @error('price_per_dozen') <div class="error">{{ $message }}</div> @enderror
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
