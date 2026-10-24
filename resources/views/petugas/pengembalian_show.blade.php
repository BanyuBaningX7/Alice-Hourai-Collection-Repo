@extends('layouts.app')
@section('title', 'Proses Pengembalian')

@section('content')
<div class="container">
    <p class="muted small"><a href="{{ route('petugas.pengembalian') }}" class="accent">Pengembalian</a> / Transaksi #{{ $loan->id }}</p>
    <div class="page-head"><div><h1>Transaksi #{{ $loan->id }}</h1>
        <span class="muted">{{ $loan->member->name }} (&#64;{{ $loan->member->username }}) &middot; jatuh tempo {{ $loan->due_date?->format('d/m/Y') ?? '-' }}</span></div>
        <x-badge :status="$loan->displayStatus()" /></div>

    @if ($loan->isOverdue())
        <div class="alert alert-warn"><i class="fa-solid fa-triangle-exclamation"></i> Terlambat. Keterlambatan saja tidak didenda, tetapi akses peminjaman anggota ditangguhkan sampai buku kembali.</div>
    @endif

    @foreach ($loan->details as $d)
        <div class="card book" style="flex-wrap:wrap">
            <x-cover :book="$d->book" />
            <div class="info">
                <div class="title">{{ $d->book->title }}</div>
                <div class="meta">Harga buku Rp {{ number_format($d->book->price, 0, ',', '.') }}</div>
            </div>

            @if ($d->item_status === 'dipinjam')
                <form method="POST" action="{{ route('petugas.kembali', $d) }}" class="row">@csrf
                    <select name="kondisi" class="select" style="width:auto" aria-label="Kondisi buku">
                        <option value="utuh">Utuh</option>
                        <option value="rusak">Rusak (denda = harga buku)</option>
                        <option value="hilang">Hilang (denda = harga buku)</option>
                    </select>
                    <button class="btn btn-primary btn-sm">Konfirmasi</button>
                </form>
            @else
                <div class="row">
                    <x-badge :status="$d->item_status" />
                    @if ($d->kondisi) <x-badge :status="$d->kondisi" /> @endif
                    @if ($d->denda) <span class="small warn">Denda Rp {{ number_format($d->denda, 0, ',', '.') }}</span> @endif
                </div>
            @endif
        </div>
    @endforeach

    <div class="alert alert-info"><i class="fa-solid fa-circle-info"></i>
        Rusak atau hilang: denda sebesar harga buku, stok total berkurang 1. Kirim email sanksi dari menu <a class="accent" href="{{ route('petugas.denda') }}">Denda</a>.</div>
</div>
@endsection
