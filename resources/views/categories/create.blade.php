@extends('layouts.app', ['title' => 'Tambah Kategori', 'heading' => 'Tambah Kategori', 'subheading' => 'Buat kategori baru untuk pengelompokan barang.'])

@section('content')
<div class="panel">
    <form action="{{ route('categories.store') }}" method="POST">
        @include('categories._form', ['submitLabel' => 'Simpan Kategori'])
    </form>
</div>
@endsection
