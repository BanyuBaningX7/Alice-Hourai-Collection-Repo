@extends('layouts.app')
@section('title', 'Katalog - Alice Hourai Collection')

@section('content')
<div class="container">
    <div class="page-head"><h1>Katalog</h1></div>

    {{-- Chip kategori (menyimpan kata kunci pencarian) --}}
    <div class="chips">
        <a href="{{ route('katalog.index', array_filter(['q' => request('q')])) }}" class="chip {{ request('category') ? '' : 'active' }}">
            @unless (request('category')) <i class="fa-solid fa-check"></i> @endunless Semua
        </a>
        @foreach ($categories as $c)
            <a href="{{ route('katalog.index', array_filter(['category' => $c->id, 'q' => request('q')])) }}"
               class="chip {{ request('category') == $c->id ? 'active' : '' }}">{{ $c->name }}</a>
        @endforeach
    </div>

    <form method="GET" action="{{ route('katalog.index') }}" class="search mt" role="search">
        <i class="fa-solid fa-magnifying-glass"></i>
        <input type="search" name="q" value="{{ request('q') }}" placeholder="Cari judul, penulis, atau ISBN..." aria-label="Cari buku">
        @if (request('category')) <input type="hidden" name="category" value="{{ request('category') }}"> @endif
        <button class="btn btn-primary btn-sm">Cari</button>
    </form>

    <div class="row between mt2">
        <div><h2 style="margin:0">Daftar Buku</h2>
            <span class="muted small">Menampilkan {{ $books->count() }} dari {{ $books->total() }} buku</span></div>
        <a class="chip ghost" href="{{ route('katalog.index', array_filter(['q' => request('q'), 'category' => request('category'), 'tersedia' => request('tersedia') ? null : 1])) }}">
            <i class="fa-regular {{ request('tersedia') ? 'fa-square-check' : 'fa-square' }}"></i> Hanya yang tersedia
        </a>
    </div>

    <div class="mt">
        @forelse ($books as $book)
            <x-book-card :book="$book" />
        @empty
            <div class="empty card">
                <i class="fa-solid fa-book-open"></i>
                <h3>Buku tidak ditemukan</h3>
                <p>Coba kata kunci atau kategori lain.</p>
                <a href="{{ route('katalog.index') }}" class="btn btn-outline btn-sm">Reset Filter</a>
            </div>
        @endforelse
    </div>

    @include('partials.pagination', ['p' => $books])
</div>
@endsection
