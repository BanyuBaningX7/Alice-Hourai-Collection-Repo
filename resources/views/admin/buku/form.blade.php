@extends('layouts.admin')
@php $edit = $book->exists; @endphp
@section('title', $edit ? 'Ubah Buku' : 'Tambah Buku')
@section('heading', $edit ? 'Ubah Buku' : 'Tambah Buku')

@section('content')
<form method="POST" enctype="multipart/form-data" action="{{ $edit ? route('admin.buku.update', $book) : route('admin.buku.store') }}" class="card form-card" style="max-width:820px">
    @csrf @if ($edit) @method('PUT') @endif

    @if ($errors->any())
        <div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation"></i>
            <ul style="margin:0;padding-left:18px">@foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach</ul></div>
    @endif

    <div class="field"><label for="cover">Sampul (jpg/png, maks 2 MB)</label>
        <div class="row">@if ($edit) <x-cover :book="$book" /> @endif <input class="input" type="file" id="cover" name="cover" accept="image/png,image/jpeg"></div></div>

    <div class="grid g2">
        <div class="field"><label for="title">Judul</label><input class="input" id="title" name="title" value="{{ old('title', $book->title) }}" required></div>
        <div class="field"><label for="author">Penulis</label><input class="input" id="author" name="author" value="{{ old('author', $book->author) }}" required></div>
        <div class="field"><label for="publisher">Penerbit</label><input class="input" id="publisher" name="publisher" value="{{ old('publisher', $book->publisher) }}"></div>
        <div class="field"><label for="year">Tahun</label><input class="input" type="number" id="year" name="year" value="{{ old('year', $book->year) }}"></div>
        <div class="field"><label for="isbn">ISBN</label><input class="input" id="isbn" name="isbn" value="{{ old('isbn', $book->isbn) }}"></div>
        <div class="field"><label for="category_id">Kategori</label>
            <select class="select" id="category_id" name="category_id" required>
                <option value="">Pilih kategori</option>
                @foreach ($categories as $c) <option value="{{ $c->id }}" @selected(old('category_id', $book->category_id) == $c->id)>{{ $c->name }}</option> @endforeach
            </select></div>
        <div class="field"><label for="price">Harga buku (Rp)</label><input class="input" type="number" min="0" step="1000" id="price" name="price" value="{{ old('price', $book->price) }}" required>
            <div class="hint">Dasar perhitungan denda bila buku hilang/rusak.</div></div>
        <div class="field"><label for="stock_total">Stok total</label><input class="input" type="number" min="0" id="stock_total" name="stock_total" value="{{ old('stock_total', $book->stock_total) }}" required>
            <div class="hint">Stok tersedia dihitung otomatis.</div></div>
        <div class="field"><label for="location">Lokasi rak</label><input class="input" id="location" name="location" value="{{ old('location', $book->location) }}"></div>
        <div class="field"><label for="status">Status</label>
            <select class="select" id="status" name="status">
                @foreach (['aktif', 'nonaktif'] as $s) <option value="{{ $s }}" @selected(old('status', $book->status) === $s)>{{ ucfirst($s) }}</option> @endforeach
            </select></div>
    </div>
    <div class="field"><label for="description">Deskripsi</label><textarea class="textarea" id="description" name="description">{{ old('description', $book->description) }}</textarea></div>

    <div class="form-actions"><button class="btn btn-primary">Simpan</button><a href="{{ route('admin.buku.index') }}" class="btn btn-outline">Batal</a></div>
</form>
@endsection
