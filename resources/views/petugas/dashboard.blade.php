@extends('layouts.app')
@section('title', 'Dashboard Petugas')

@section('content')
<div class="container">
    <div class="page-head"><div><h1>Dashboard Petugas</h1><span class="muted">Hanya petugas yang dapat menyetujui peminjaman.</span></div></div>

    <div class="grid g4">
        <a href="{{ route('petugas.pengajuan') }}" class="card stat"><div class="num warn">{{ $stats['menunggu'] }}</div><div class="lbl">Pengajuan menunggu</div></a>
        <a href="{{ route('petugas.aktif') }}" class="card stat"><div class="num accent">{{ $stats['aktif'] }}</div><div class="lbl">Pinjaman aktif</div></a>
        <a href="{{ route('petugas.aktif') }}" class="card stat"><div class="num danger">{{ $stats['terlambat'] }}</div><div class="lbl">Terlambat</div></a>
        <a href="{{ route('petugas.denda', ['status' => 'belum']) }}" class="card stat"><div class="num warn">{{ $stats['denda'] }}</div><div class="lbl">Denda belum lunas</div></a>
    </div>

    <section class="section">
        <h2>Antrean Terbaru</h2>
        @if ($queue->isEmpty())
            <div class="empty card"><i class="fa-solid fa-inbox"></i><p>Tidak ada pengajuan menunggu.</p></div>
        @else
            <div class="tbl-wrap"><table class="tbl">
                <thead><tr><th>Anggota</th><th>Buku</th><th>Diajukan</th><th></th></tr></thead>
                <tbody>@foreach ($queue as $loan)
                    <tr><td>{{ $loan->member->name }} <span class="muted small">&#64;{{ $loan->member->username }}</span></td>
                        <td>{{ $loan->details->map(fn ($d) => $d->book->title)->implode(', ') }}</td>
                        <td>{{ $loan->requested_at->format('d/m/Y H:i') }}</td>
                        <td><a href="{{ route('petugas.pengajuan.show', $loan) }}" class="btn btn-outline btn-sm">Periksa</a></td></tr>
                @endforeach</tbody>
            </table></div>
        @endif
    </section>
</div>
@endsection
