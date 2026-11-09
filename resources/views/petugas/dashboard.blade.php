@extends('layouts.app')
@section('title', 'Dashboard Petugas')

@section('content')
<div class="container">
    <div class="page-head"><div><h1>Dashboard Petugas</h1><span class="muted">Verifikasi jaminan identitas (KTP), persetujuan pinjam, dan pantauan jatuh tempo.</span></div></div>

    <div class="grid g4">
        <a href="{{ route('petugas.pengajuan') }}" class="card stat"><div class="num warn">{{ $stats['menunggu'] }}</div><div class="lbl">Pengajuan menunggu</div></a>
        <a href="{{ route('petugas.aktif') }}" class="card stat"><div class="num accent">{{ $stats['aktif'] }}</div><div class="lbl">Pinjaman aktif</div></a>
        <a href="{{ route('petugas.aktif') }}" class="card stat"><div class="num warn">{{ $stats['segera_tempo'] }}</div><div class="lbl">Segera jatuh tempo (H-2)</div></a>
        <a href="{{ route('petugas.aktif') }}" class="card stat"><div class="num danger">{{ $stats['terlambat'] }}</div><div class="lbl">Terlambat</div></a>
    </div>

    <section class="section">
        <h2>Deteksi Jatuh Tempo & Keterlambatan (Otomatis H-2 s/d Terlambat)</h2>
        @if ($dueAlerts->isEmpty())
            <div class="empty card"><i class="fa-solid fa-bell-slash"></i><p>Tidak ada pinjaman yang mendekati atau melewati jatuh tempo saat ini.</p></div>
        @else
            <div class="tbl-wrap"><table class="tbl">
                <thead><tr><th>ID</th><th>Anggota & Jaminan ID</th><th>Buku</th><th>Jatuh Tempo</th><th>Deteksi Waktu</th><th>Aksi Peringatan</th></tr></thead>
                <tbody>@foreach ($dueAlerts as $loan)
                    <tr>
                        <td>#{{ $loan->id }}</td>
                        <td>
                            {{ $loan->member->name }} <span class="muted small">(&#64;{{ $loan->member->username }})</span><br>
                            <span class="muted small">{{ $loan->member->identity_type ?? 'KTP' }}: {{ $loan->member->identity_number ?? '-' }}</span>
                        </td>
                        <td>{{ $loan->details->map(fn ($d) => $d->book->title)->implode(', ') }}</td>
                        <td>{{ $loan->due_date->format('d/m/Y') }}</td>
                        <td><b class="{{ $loan->isOverdue() ? 'danger' : 'warn' }}">{{ $loan->dueStatusLabel() }}</b></td>
                        <td>
                            <form method="POST" action="{{ route('petugas.remind', $loan) }}" style="display:inline">@csrf
                                <button class="btn btn-outline btn-sm"><i class="fa-solid fa-bell"></i> {{ $loan->due_notified_at ? 'Kirim Ulang Peringatan' : 'Kirim Peringatan' }}</button>
                            </form>
                        </td>
                    </tr>
                @endforeach</tbody>
            </table></div>
        @endif
    </section>

    <section class="section">
        <h2>Antrean Pengajuan Terbaru</h2>
        @if ($queue->isEmpty())
            <div class="empty card"><i class="fa-solid fa-inbox"></i><p>Tidak ada pengajuan menunggu.</p></div>
        @else
            <div class="tbl-wrap"><table class="tbl">
                <thead><tr><th>Anggota</th><th>Jaminan Identitas</th><th>Buku</th><th>Diajukan</th><th></th></tr></thead>
                <tbody>@foreach ($queue as $loan)
                    <tr>
                        <td>{{ $loan->member->name }} <span class="muted small">&#64;{{ $loan->member->username }}</span></td>
                        <td><span class="small">{{ $loan->member->identity_type ?? 'KTP' }}: <b>{{ $loan->member->identity_number ?? '-' }}</b></span></td>
                        <td>{{ $loan->details->map(fn ($d) => $d->book->title)->implode(', ') }}</td>
                        <td>{{ $loan->requested_at->format('d/m/Y H:i') }}</td>
                        <td><a href="{{ route('petugas.pengajuan.show', $loan) }}" class="btn btn-outline btn-sm">Verifikasi & Periksa</a></td>
                    </tr>
                @endforeach</tbody>
            </table></div>
        @endif
    </section>
</div>
@endsection
