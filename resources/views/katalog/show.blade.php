@extends('layouts.app')
@section('title', $book->title . ' - Alice Hourai Collection')

@section('content')
@php
    $user = auth()->user();
    $suspended = $user && $user->isSuspended();
    $inCart = in_array($book->id, session('cart', []));
@endphp
<div class="container">
    <p class="muted small"><a href="{{ route('katalog.index') }}" class="accent">Katalog</a> / Detail Buku</p>

    <div class="detail">
        <div><x-cover :book="$book" :large="true" /></div>

        <div>
            <h1 style="font-size:32px">{{ $book->title }}</h1>
            <p class="muted">oleh {{ $book->author }}</p>
            <div class="row" style="margin-bottom:16px">
                <span class="badge b-dipinjam">{{ $book->category->name ?? '-' }}</span>
                @if ($book->stock_available > 0)
                    <span class="badge b-tersedia">Tersedia {{ $book->stock_available }} dari {{ $book->stock_total }}</span>
                @else
                    <span class="badge b-habis">Habis</span>
                @endif
            </div>

            <div class="card">
                <dl class="info-grid" style="margin:0">
                    <div><dt>Penerbit</dt><dd>{{ $book->publisher ?? '-' }}</dd></div>
                    <div><dt>Tahun</dt><dd>{{ $book->year ?? '-' }}</dd></div>
                    <div><dt>ISBN</dt><dd>{{ $book->isbn ?? '-' }}</dd></div>
                    <div><dt>Lokasi rak</dt><dd>{{ $book->location ?? '-' }}</dd></div>
                    <div><dt>Harga buku</dt><dd>Rp {{ number_format($book->price, 0, ',', '.') }} <span class="muted small">(dasar denda bila hilang/rusak)</span></dd></div>
                </dl>
            </div>

            @if ($book->description) <p class="mt">{{ $book->description }}</p> @endif

            <div class="alert alert-info mt">
                <i class="fa-solid fa-circle-info"></i>
                <span>Masa pinjam maksimal {{ config('library.loan_days') }} hari. Maksimal {{ config('library.max_books') }} buku aktif per anggota.</span>
            </div>

            {{-- Tombol pinjam menyesuaikan kondisi pengguna --}}
            @guest
                <a href="{{ route('login') }}" class="btn btn-primary">Login untuk meminjam</a>
            @else
                @if ($user->isAnggota())
                    @if ($suspended)
                        <div class="alert alert-warn"><i class="fa-solid fa-triangle-exclamation"></i> Akses peminjaman ditangguhkan. Kembalikan buku yang terlambat atau lunasi denda.</div>
                        <button class="btn btn-primary" disabled>Ajukan Pinjam</button>
                    @elseif ($book->stock_available < 1)
                        <button class="btn btn-primary" disabled>Stok Habis</button>
                    @elseif ($inCart)
                        <a href="{{ route('ajukan') }}" class="btn btn-outline">Sudah di pengajuan - Lihat</a>
                    @else
                        <form method="POST" action="{{ route('cart.add', $book) }}">@csrf
                            <button class="btn btn-primary"><i class="fa-solid fa-book-medical"></i> Ajukan Pinjam</button>
                        </form>
                    @endif
                @else
                    <p class="muted small">Hanya akun anggota yang dapat meminjam buku.</p>
                @endif
            @endguest
        </div>
    </div>

    @if ($related->isNotEmpty())
        <section class="section">
            <h2>Buku Terkait</h2>
            @foreach ($related as $r) <x-book-card :book="$r" /> @endforeach
        </section>
    @endif
</div>
@endsection
