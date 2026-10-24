@extends('layouts.app')
@section('title', 'Ajukan Pinjam - Alice Hourai Collection')

@section('content')
<div class="container">
    <div class="page-head"><h1>Ajukan Peminjaman</h1></div>

    @if (auth()->user()->isSuspended())
        <div class="alert alert-warn"><i class="fa-solid fa-triangle-exclamation"></i> Akses peminjaman ditangguhkan, pengajuan akan ditolak.</div>
    @endif

    <div class="grid" style="grid-template-columns:2fr 1fr;align-items:start">
        <div>
            <h3>Buku dipilih ({{ $books->count() }}/{{ $max - $active }})</h3>
            @forelse ($books as $book)
                <div class="card book">
                    <x-cover :book="$book" />
                    <div class="info"><div class="title">{{ $book->title }}</div><div class="meta">{{ $book->author }}</div></div>
                    <form method="POST" action="{{ route('cart.remove', $book) }}">@csrf @method('DELETE')
                        <button class="link-btn danger" title="Hapus dari pengajuan"><i class="fa-solid fa-trash"></i></button>
                    </form>
                </div>
            @empty
                <div class="empty card"><i class="fa-solid fa-cart-shopping"></i><p>Belum ada buku dipilih.</p></div>
            @endforelse
            <a href="{{ route('katalog.index') }}" class="btn btn-outline btn-sm">+ Tambah buku dari katalog</a>
        </div>

        <div class="card">
            <h3>Ringkasan Pengajuan</h3>
            <p class="muted small">Jumlah buku: <b class="accent">{{ $books->count() }}</b><br>
               Buku aktif saat ini: {{ $active }}/{{ $max }}<br>
               Masa pinjam maksimal {{ config('library.loan_days') }} hari.</p>
            <form method="POST" action="{{ route('ajukan.store') }}">@csrf
                <button class="btn btn-primary btn-block" {{ $books->isEmpty() ? 'disabled' : '' }}>Kirim Pengajuan</button>
            </form>
        </div>
    </div>
</div>
@endsection
