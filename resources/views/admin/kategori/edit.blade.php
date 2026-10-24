@extends('layouts.admin')
@section('title', 'Ubah Kategori')
@section('heading', 'Ubah Kategori')

@section('content')
<form method="POST" action="{{ route('admin.kategori.update', $category) }}" class="card form-card">@csrf @method('PUT')
    <div class="field"><label for="name">Nama</label><input class="input {{ $errors->has('name') ? 'is-error' : '' }}" id="name" name="name" value="{{ old('name', $category->name) }}" required>
        @error('name') <div class="err-text">{{ $message }}</div> @enderror</div>
    <div class="field"><label for="description">Deskripsi</label><textarea class="textarea" id="description" name="description">{{ old('description', $category->description) }}</textarea></div>
    <div class="form-actions"><button class="btn btn-primary">Simpan</button><a href="{{ route('admin.kategori.index') }}" class="btn btn-outline">Batal</a></div>
</form>
@endsection
