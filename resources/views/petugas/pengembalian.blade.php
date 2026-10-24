@extends('layouts.app')
@section('title', 'Pengembalian')

@section('content')
<div class="container">
    <div class="page-head"><h1>Pengembalian</h1></div>
    <form method="GET" class="search"><i class="fa-solid fa-magnifying-glass"></i>
        <input name="q" value="{{ request('q') }}" placeholder="Cari nama / username anggota atau ID pinjam..."><button class="btn btn-primary btn-sm">Cari</button></form>

    <div class="mt">
    @forelse ($loans as $loan)
        <div class="card book">
            <div class="info">
                <div class="title">#{{ $loan->id }} &middot; {{ $loan->member->name }}</div>
                <div class="meta">{{ $loan->details->map(fn ($d) => $d->book->title)->implode(', ') }}</div>
                <div class="meta">Jatuh tempo {{ $loan->due_date->format('d/m/Y') }}</div>
            </div>
            <x-badge :status="$loan->displayStatus()" />
            <a href="{{ route('petugas.pengembalian.show', $loan) }}" class="btn btn-primary btn-sm">Proses</a>
        </div>
    @empty
        <div class="empty card"><i class="fa-solid fa-rotate-left"></i><p>Tidak ada transaksi aktif yang cocok.</p></div>
    @endforelse
    </div>
    @include('partials.pagination', ['p' => $loans])
</div>
@endsection
