@extends('layouts.app')
@section('title', 'Alice Hourai Collection')

@section('content')
<div class="container">

    {{-- ===== Hero + pencarian ===== --}}
    <section class="hero">
        <div class="label">PERPUSTAKAAN DIGITAL</div>
        <h1>Alice Hourai Collection</h1>
        <p class="sub">Temukan, pinjam, dan kelola koleksi buku dengan mudah.</p>

        <form action="{{ route('katalog.index') }}" method="GET" class="search" role="search">
            <i class="fa-solid fa-magnifying-glass"></i>
            <input type="search" name="q" placeholder="Cari judul, penulis, atau ISBN..." aria-label="Cari buku" autocomplete="off">
            <button class="btn btn-primary btn-sm" type="submit">Cari</button>
        </form>

        <div class="chips">
            @foreach ($categories->take(6) as $c)
                <a class="chip ghost" href="{{ route('katalog.index', ['category' => $c->id]) }}">{{ $c->name }}</a>
            @endforeach
        </div>

        <div class="cta">
            <a href="{{ route('katalog.index') }}" class="btn btn-primary">Lihat Katalog</a>
            <a href="#cara-meminjam" class="btn btn-outline">Cara Meminjam</a>
        </div>

        <div class="stats-strip">
            <div><b>{{ $stats['buku'] }}</b><span class="muted small">Judul buku</span></div>
            <div><b>{{ $stats['kategori'] }}</b><span class="muted small">Kategori</span></div>
            <div><b>{{ $stats['anggota'] }}</b><span class="muted small">Anggota</span></div>
        </div>
    </section>

    {{-- ===== Kategori ===== --}}
    <section class="section">
        <div class="row between"><h2>Jelajahi Kategori</h2><a href="{{ route('katalog.index') }}" class="accent small">Lihat semua</a></div>
        @if ($categories->isEmpty())
            <div class="empty card"><i class="fa-solid fa-tags"></i><p>Belum ada kategori.</p></div>
        @else
            <div class="grid g4">
                @foreach ($categories as $c)
                    <a href="{{ route('katalog.index', ['category' => $c->id]) }}" class="card cat-card">
                        <span class="ico"><i class="fa-solid fa-book-bookmark"></i></span>
                        <span><b>{{ $c->name }}</b><br><span class="muted small">{{ $c->books_count }} buku</span></span>
                    </a>
                @endforeach
            </div>
        @endif
    </section>

    {{-- ===== Buku unggulan ===== --}}
    <section class="section">
        <div class="row between"><h2>Buku Unggulan</h2><a href="{{ route('katalog.index') }}" class="accent small">Lihat katalog</a></div>
        @forelse ($featured as $book)
            @if ($loop->first) <div class="grid g2"> @endif
                <x-book-card :book="$book" />
            @if ($loop->last) </div> @endif
        @empty
            <div class="empty card"><i class="fa-solid fa-book"></i><p>Belum ada buku.</p></div>
        @endforelse
    </section>

    {{-- ===== Cara meminjam ===== --}}
    <section class="section" id="cara-meminjam">
        <h2>Cara Meminjam</h2>
        <div class="grid g3">
            <div class="card step"><div class="no">1</div><b>Cari buku</b><p class="muted small">Temukan buku favorit dari katalog digital kami.</p></div>
            <div class="card step"><div class="no">2</div><b>Ajukan pinjam</b><p class="muted small">Pilih maksimal {{ config('library.max_books') }} buku dan kirim pengajuan.</p></div>
            <div class="card step"><div class="no">3</div><b>Ambil di petugas</b><p class="muted small">Setelah disetujui, ambil buku. Masa pinjam maksimal {{ config('library.loan_days') }} hari.</p></div>
        </div>
    </section>

    @guest
        <section class="section">
            <div class="card cta-card">
                <h2>Belum punya akun?</h2>
                <p class="muted">Daftar gratis sebagai anggota untuk mulai meminjam buku.</p>
                <a href="{{ route('register') }}" class="btn btn-primary">Daftar Sekarang</a>
            </div>
        </section>
    @endguest
</div>
@endsection
