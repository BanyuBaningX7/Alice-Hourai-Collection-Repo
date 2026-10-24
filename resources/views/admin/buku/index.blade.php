@extends('layouts.admin')
@section('title', 'Data Buku')
@section('heading', 'Data Buku')

@section('content')
<div class="page-head">
    <form method="GET" class="row">
        <div class="search"><i class="fa-solid fa-magnifying-glass"></i><input name="q" value="{{ request('q') }}" placeholder="Cari judul, penulis, ISBN"></div>
        <select name="category" class="select" style="width:auto" onchange="this.form.submit()">
            <option value="">Semua kategori</option>
            @foreach ($categories as $c) <option value="{{ $c->id }}" @selected(request('category') == $c->id)>{{ $c->name }}</option> @endforeach
        </select>
        <select name="status" class="select" style="width:auto" onchange="this.form.submit()">
            <option value="">Semua status</option>
            <option value="aktif" @selected(request('status') === 'aktif')>Aktif</option>
            <option value="nonaktif" @selected(request('status') === 'nonaktif')>Nonaktif</option>
        </select>
    </form>
    <a href="{{ route('admin.buku.create') }}" class="btn btn-primary btn-sm">+ Tambah Buku</a>
</div>

<div class="tbl-wrap"><table class="tbl">
    <thead><tr><th>Sampul</th><th>Judul</th><th>Penulis</th><th>Kategori</th><th>Harga</th><th>Stok</th><th>Status</th><th>Aksi</th></tr></thead>
    <tbody>@forelse ($books as $b)
        <tr>
            <td><x-cover :book="$b" /></td><td><b>{{ $b->title }}</b><div class="muted small">{{ $b->isbn }}</div></td>
            <td>{{ $b->author }}</td><td>{{ $b->category->name ?? '-' }}</td>
            <td>Rp {{ number_format($b->price, 0, ',', '.') }}</td><td>{{ $b->stock_available }}/{{ $b->stock_total }}</td>
            <td><x-badge :status="$b->status" /></td>
            <td><div class="actions">
                <a href="{{ route('admin.buku.edit', $b) }}" class="link-btn" title="Ubah"><i class="fa-solid fa-pen"></i></a>
                <form method="POST" action="{{ route('admin.buku.destroy', $b) }}" data-confirm="Arsipkan buku ini? Buku disembunyikan dari katalog, riwayat peminjaman tetap aman." data-yes="Arsipkan">@csrf @method('DELETE')
                    <button class="link-btn danger" title="Arsipkan"><i class="fa-solid fa-trash"></i></button></form>
            </div></td>
        </tr>
    @empty <tr><td colspan="8" class="empty">Buku tidak ditemukan.</td></tr> @endforelse</tbody>
</table></div>
@include('partials.pagination', ['p' => $books])
@endsection
