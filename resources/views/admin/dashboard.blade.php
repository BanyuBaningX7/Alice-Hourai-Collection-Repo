@extends('layouts.admin')
@section('title', 'Dashboard')
@section('heading', 'Dashboard')

@section('content')
@php $maxMonth = max(1, $perMonth->max('total')); $maxCat = max(1, $perCategory->max('books_count')); @endphp
<div class="grid g4">
    <div class="card stat"><div class="num">{{ $stats['buku'] }}</div><div class="lbl">Total buku</div></div>
    <div class="card stat"><div class="num">{{ $stats['anggota'] }}</div><div class="lbl">Anggota</div></div>
    <div class="card stat"><div class="num accent">{{ $stats['aktif'] }}</div><div class="lbl">Pinjaman aktif</div></div>
    <div class="card stat"><div class="num danger">{{ $stats['terlambat'] }}</div><div class="lbl">Terlambat</div></div>
</div>

<div class="grid g2 mt2">
    <div class="card">
        <h3>Peminjaman per bulan</h3>
        <div class="bars">
            @foreach ($perMonth as $m)
                <div class="bar"><b>{{ $m['total'] }}</b><span class="fill" style="height:{{ ($m['total'] / $maxMonth) * 100 }}%"></span><span class="muted">{{ $m['label'] }}</span></div>
            @endforeach
        </div>
    </div>
    <div class="card">
        <h3>Buku per kategori</h3>
        @forelse ($perCategory as $c)
            <div class="hbar"><div class="row between small"><span>{{ $c->name }}</span><b>{{ $c->books_count }}</b></div>
                <div class="track"><div class="fill" style="width:{{ ($c->books_count / $maxCat) * 100 }}%"></div></div></div>
        @empty <p class="muted">Belum ada kategori.</p> @endforelse
    </div>
</div>

<div class="card mt2">
    <div class="row between"><h3 style="margin:0">Aktivitas terbaru</h3><a href="{{ route('admin.log') }}" class="accent small">Lihat semua</a></div>
    @forelse ($logs as $l)
        <div class="row between" style="padding:8px 0;border-top:1px solid rgba(255,255,255,.08)">
            <span><b>{{ $l->user->username ?? 'sistem' }}</b> <span class="muted">{{ $l->action }}</span></span>
            <span class="muted small">{{ $l->created_at?->diffForHumans() }}</span>
        </div>
    @empty <p class="muted">Belum ada aktivitas.</p> @endforelse
</div>
@endsection
