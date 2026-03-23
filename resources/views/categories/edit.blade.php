@extends('layouts.app', ['title' => 'Edit Kategori', 'heading' => 'Edit Kategori', 'subheading' => 'Perbarui informasi kategori barang.'])

@section('content')
<div class="panel">
    <form action="{{ route('categories.update', $category) }}" method="POST">
        @method('PUT')
        @include('categories._form', ['submitLabel' => 'Update Kategori'])
    </form>
</div>
@endsection
