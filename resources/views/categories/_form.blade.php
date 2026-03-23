@csrf
<div class="form-grid">
    <div class="field">
        <label for="name">Nama Kategori</label>
        <input type="text" id="name" name="name" value="{{ old('name', $category->name) }}" required>
        @error('name') <div class="error">{{ $message }}</div> @enderror
    </div>
    <div class="field full">
        <label for="description">Deskripsi</label>
        <textarea id="description" name="description">{{ old('description', $category->description) }}</textarea>
        @error('description') <div class="error">{{ $message }}</div> @enderror
    </div>
</div>
<div style="margin-top:20px; display:flex; gap:10px;">
    <button class="btn btn-primary" type="submit">{{ $submitLabel }}</button>
    <a href="{{ route('categories.index') }}" class="btn">Batal</a>
</div>
