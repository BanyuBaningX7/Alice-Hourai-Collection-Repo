@extends('layouts.app')
@section('title', 'Antrean Pengajuan')

@section('content')
<div class="container">
    <div class="page-head"><h1>Antrean Pengajuan</h1>
        <form method="GET" class="search" style="min-width:280px"><i class="fa-solid fa-magnifying-glass"></i>
            <input name="q" value="{{ request('q') }}" placeholder="Cari anggota..."><button class="btn btn-primary btn-sm">Cari</button></form></div>

    @if ($loans->isEmpty())
        <div class="empty card"><i class="fa-solid fa-inbox"></i><p>Tidak ada pengajuan menunggu.</p></div>
    @else
        <div class="tbl-wrap"><table class="tbl">
            <thead><tr><th>Anggota</th><th>Buku</th><th>Diajukan</th><th>Status</th><th>Aksi</th></tr></thead>
            <tbody>@foreach ($loans as $loan)
                <tr><td>{{ $loan->member->name }} <span class="muted small">&#64;{{ $loan->member->username }}</span></td>
                    <td>{{ $loan->details->map(fn ($d) => $d->book->title)->implode(', ') }}</td>
                    <td>{{ $loan->requested_at->format('d/m/Y H:i') }}</td>
                    <td><x-badge status="menunggu" /></td>
                    <td><a href="{{ route('petugas.pengajuan.show', $loan) }}" class="btn btn-outline btn-sm">Periksa</a></td></tr>
            @endforeach</tbody>
        </table></div>
        @include('partials.pagination', ['p' => $loans])
    @endif
</div>
@endsection
