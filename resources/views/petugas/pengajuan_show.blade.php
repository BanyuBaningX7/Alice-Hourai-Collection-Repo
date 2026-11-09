@extends('layouts.app')
@section('title', 'Detail Pengajuan')

@section('content')
<div class="container">
    <p class="muted small"><a href="{{ route('petugas.pengajuan') }}" class="accent">Antrean</a> / Pengajuan #{{ $loan->id }}</p>
    <h1>Detail Pengajuan #{{ $loan->id }}</h1>

    <div class="grid" style="grid-template-columns:1fr 2fr 1fr;align-items:start">
        <div class="card">
            <h3>Anggota & Jaminan ID</h3>
            <p><b>{{ $member->name }}</b><br><span class="muted small">&#64;{{ $member->username }}<br>{{ $member->email }}<br>{{ $member->phone ?? '-' }}</span></p>
            <hr style="border-color:rgba(255,255,255,.1)">
            <p class="small">
                <b><i class="fa-solid fa-id-card accent"></i> Jaminan Identitas:</b><br>
                Jenis: <b>{{ $member->identity_type ?? 'KTP' }}</b><br>
                Nomor: <b>{{ $member->identity_number ?? 'Belum diisi' }}</b>
                @if ($member->identity_card_path)
                    <br><a class="accent small" href="{{ asset('storage/' . $member->identity_card_path) }}" target="_blank"><i class="fa-solid fa-eye"></i> Lihat Foto Kartu Identitas</a>
                @endif
            </p>
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
            <h3>Keputusan & Verifikasi KTP</h3>
            <form method="POST" action="{{ route('petugas.approve', $loan) }}" data-confirm="Setujui pengajuan ini setelah memverifikasi KTP/Kartu Identitas asli anggota?" data-yes="Ya, Setujui">@csrf
                <label class="check small" style="margin-bottom:12px;display:flex;gap:8px;align-items:flex-start">
                    <input type="checkbox" name="identity_verified" value="1" required style="margin-top:3px">
                    <span>Saya telah memeriksa & memverifikasi <b>{{ $member->identity_type ?? 'KTP' }} ({{ $member->identity_number ?? '-' }})</b> asli milik anggota sebagai jaminan keamanan peminjaman.</span>
                </label>
                @error('identity_verified') <div class="err-text" style="margin-bottom:8px">{{ $message }}</div> @enderror
                <button class="btn btn-primary btn-block">Verifikasi ID & Setujui</button>
            </form>
            <p class="muted small mt">Jatuh tempo otomatis {{ config('library.loan_days') }} hari sejak disetujui.</p>
            <hr style="border-color:rgba(255,255,255,.1)">
            <form method="POST" action="{{ route('petugas.reject', $loan) }}">@csrf
                <div class="field"><label for="reason">Alasan penolakan</label>
                    <textarea class="textarea" id="reason" name="reason" required placeholder="Misal: KTP/Kartu identitas tidak sesuai atau tidak dibawa">{{ old('reason') }}</textarea>
                    @error('reason') <div class="err-text">{{ $message }}</div> @enderror</div>
                <button class="btn btn-danger btn-block">Tolak</button>
            </form>
        </div>
    </div>
</div>
@endsection
