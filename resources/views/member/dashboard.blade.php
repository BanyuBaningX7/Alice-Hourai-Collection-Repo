@extends('layouts.app')
@section('title', 'Dashboard - Alice Hourai Collection')

@section('content')
<div class="container">
    <div class="page-head">
        <div><h1>Halo, {{ $user->name }}</h1><span class="muted">Ringkasan peminjaman Anda.</span></div>
        <a href="{{ route('katalog.index') }}" class="btn btn-primary btn-sm"><i class="fa-solid fa-book"></i> Cari Buku</a>
    </div>

    @if ($suspended)
        <div class="alert alert-warn">
            <i class="fa-solid fa-triangle-exclamation"></i>
            <div><b>Akses peminjaman ditangguhkan.</b> Kembalikan buku yang terlambat atau lunasi denda untuk membuka kembali.
                <a href="{{ route('member.denda') }}" class="accent">Lihat tagihan</a></div>
        </div>
    @endif

    <div class="grid g3">
        <div class="card stat"><div class="num">{{ $activeBooks }}/{{ config('library.max_books') }}</div><div class="lbl">Buku aktif</div></div>
        <div class="card stat"><div class="num">{{ $pending->count() }}</div><div class="lbl">Pengajuan menunggu</div></div>
        <div class="card stat">
            <div class="num" style="font-size:22px">{{ $nearestDue ? $nearestDue->format('d M Y') : '-' }}</div>
            <div class="lbl">Jatuh tempo terdekat @if ($nearestDue && $nearestDue->isPast()) <span class="danger">(terlambat)</span> @endif</div>
        </div>
    </div>

    <section class="section">
        <h2>Pinjaman Aktif</h2>
        @forelse ($active as $loan)
            <div class="card book">
                <div class="info">
                    <div class="title">{{ $loan->details->map(fn ($d) => $d->book->title)->implode(', ') }}</div>
                    <div class="meta">Jatuh tempo {{ $loan->due_date->format('d M Y') }}</div>
                </div>
                <x-badge :status="$loan->displayStatus()" />
            </div>
        @empty
            <div class="empty card"><i class="fa-solid fa-book-open"></i><p>Tidak ada pinjaman aktif.</p></div>
        @endforelse
    </section>

    <section class="section">
        <h2>Pengajuan Menunggu</h2>
        @forelse ($pending as $loan)
            <div class="card book">
                <div class="info">
                    <div class="title">{{ $loan->details->map(fn ($d) => $d->book->title)->implode(', ') }}</div>
                    <div class="meta">Diajukan {{ $loan->requested_at->format('d M Y H:i') }}</div>
                </div>
                <x-badge status="menunggu" />
            </div>
        @empty
            <div class="empty card"><i class="fa-regular fa-clock"></i><p>Tidak ada pengajuan yang menunggu.</p></div>
        @endforelse
    </section>
</div>
@endsection
