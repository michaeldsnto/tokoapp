@extends('layouts.app', ['title' => 'Tambah Barang', 'heading' => 'Tambah Barang', 'subheading' => 'Masukkan detail barang baru untuk dijual di kasir.'])

@section('content')
<div class="panel">
    <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data">
        @include('products._form', ['submitLabel' => 'Simpan Barang'])
    </form>
</div>
@endsection
