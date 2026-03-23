@extends('layouts.app', ['title' => 'Edit Barang', 'heading' => 'Edit Barang', 'subheading' => 'Perbarui harga, stok, kategori, dan barcode barang.'])

@section('content')
<div class="panel">
    <form action="{{ route('products.update', $product) }}" method="POST" enctype="multipart/form-data">
        @method('PUT')
        @include('products._form', ['submitLabel' => 'Update Barang'])
    </form>
</div>
@endsection
