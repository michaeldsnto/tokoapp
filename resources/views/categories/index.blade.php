@extends('layouts.app', ['title' => 'Kategori', 'heading' => 'Kategori', 'subheading' => 'Kelola klasifikasi barang agar pencarian lebih rapi.'])

@section('top_actions')
<a href="{{ route('categories.create') }}" class="btn btn-primary">Tambah Kategori</a>
@endsection

@section('content')
<div class="panel">
    <div class="table-wrap">
        <table>
            <thead><tr><th>Nama</th><th>Slug</th><th>Deskripsi</th><th>Aksi</th></tr></thead>
            <tbody>
            @forelse($categories as $category)
                <tr>
                    <td>{{ $category->name }}</td>
                    <td>{{ $category->slug }}</td>
                    <td>{{ $category->description ?: '-' }}</td>
                    <td style="white-space:nowrap;">
                        <a href="{{ route('categories.edit', $category) }}" class="btn">Edit</a>
                        <form action="{{ route('categories.destroy', $category) }}" method="POST" class="inline" onsubmit="return confirm('Hapus kategori ini?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-danger" type="submit">Hapus</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="4" class="muted">Belum ada kategori.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="pagination">{{ $categories->links() }}</div>
</div>
@endsection
