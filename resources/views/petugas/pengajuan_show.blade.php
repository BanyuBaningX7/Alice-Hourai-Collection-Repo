@extends('layouts.app')
@section('title', 'Detail Pengajuan')

@section('content')
<div class="container">
    <p class="muted small"><a href="{{ route('petugas.pengajuan') }}" class="accent">Antrean</a> / Pengajuan #{{ $loan->id }}</p>
    <h1>Detail Pengajuan #{{ $loan->id }}</h1>

    <div class="grid" style="grid-template-columns:1fr 2fr 1fr;align-items:start">
        <div class="card">
            <h3>Anggota</h3>
            <p><b>{{ $member->name }}</b><br><span class="muted small">&#64;{{ $member->username }}<br>{{ $member->email }}<br>{{ $member->phone ?? '-' }}</span></p>
            <p class="small">Buku aktif: <b>{{ $member->activeBookCount() }}/{{ config('library.max_books') }}</b></p>
            @if ($member->isSuspended()) <x-badge status="ditangguhkan" /> @else <x-badge status="aktif" label="Akses aktif" /> @endif
        </div>

        <div>
            <h3>Buku diajukan</h3>
            @foreach ($loan->details as $d)
                <div class="card book">
                    <x-cover :book="$d->book" />
                    <div class="info"><div class="title">{{ $d->book->title }}</div><div class="meta">{{ $d->book->author }}</div></div>
                    @if ($d->book->stock_available > 0) <span class="badge b-tersedia">Stok {{ $d->book->stock_available }}</span> @else <span class="badge b-habis">Stok habis</span> @endif
                </div>
            @endforeach
        </div>

        <div class="card">
            <h3>Keputusan</h3>
            <form method="POST" action="{{ route('petugas.approve', $loan) }}" data-confirm="Setujui pengajuan ini? Jatuh tempo otomatis {{ config('library.loan_days') }} hari." data-yes="Setujui">@csrf
                <button class="btn btn-primary btn-block">Setujui</button>
            </form>
            <p class="muted small mt">Jatuh tempo otomatis {{ config('library.loan_days') }} hari sejak disetujui.</p>
            <hr style="border-color:rgba(255,255,255,.1)">
            <form method="POST" action="{{ route('petugas.reject', $loan) }}">@csrf
                <div class="field"><label for="reason">Alasan penolakan</label>
                    <textarea class="textarea" id="reason" name="reason" required>{{ old('reason') }}</textarea>
                    @error('reason') <div class="err-text">{{ $message }}</div> @enderror</div>
                <button class="btn btn-danger btn-block">Tolak</button>
            </form>
        </div>
    </div>
</div>
@endsection
