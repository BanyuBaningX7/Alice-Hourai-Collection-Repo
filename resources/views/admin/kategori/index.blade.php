@extends('layouts.admin')
@section('title', 'Kategori')
@section('heading', 'Kategori')

@section('content')
<div class="grid" style="grid-template-columns:2fr 1fr;align-items:start">
    <div class="tbl-wrap"><table class="tbl" style="min-width:0">
        <thead><tr><th>Nama</th><th>Deskripsi</th><th>Buku</th><th>Aksi</th></tr></thead>
        <tbody>@forelse ($categories as $c)
            <tr><td><b>{{ $c->name }}</b></td><td class="muted">{{ $c->description }}</td><td>{{ $c->books_count }}</td>
                <td><div class="actions">
                    <a href="{{ route('admin.kategori.edit', $c) }}" class="link-btn" title="Ubah"><i class="fa-solid fa-pen"></i></a>
                    <form method="POST" action="{{ route('admin.kategori.destroy', $c) }}" data-confirm="Hapus kategori {{ $c->name }}? (hanya bisa bila belum memiliki buku)" data-yes="Hapus">@csrf @method('DELETE')
                        <button class="link-btn danger" title="Hapus"><i class="fa-solid fa-trash"></i></button></form>
                </div></td></tr>
        @empty <tr><td colspan="4" class="empty">Belum ada kategori.</td></tr> @endforelse</tbody>
    </table></div>

    <form method="POST" action="{{ route('admin.kategori.store') }}" class="card">@csrf
        <h3>Tambah Kategori</h3>
        <div class="field"><label for="name">Nama</label><input class="input {{ $errors->has('name') ? 'is-error' : '' }}" id="name" name="name" value="{{ old('name') }}" required>
            @error('name') <div class="err-text">{{ $message }}</div> @enderror</div>
        <div class="field"><label for="description">Deskripsi</label><textarea class="textarea" id="description" name="description">{{ old('description') }}</textarea></div>
        <button class="btn btn-primary btn-block">Tambah</button>
    </form>
</div>
@endsection
