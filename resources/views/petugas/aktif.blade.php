@extends('layouts.app')
@section('title', 'Peminjaman Aktif & Notifikasi Jatuh Tempo')

@section('content')
<div class="container">
    <div class="page-head"><h1>Peminjaman Aktif & Deteksi Jatuh Tempo</h1>
        <form method="GET" class="search" style="min-width:280px"><i class="fa-solid fa-magnifying-glass"></i>
            <input name="q" value="{{ request('q') }}" placeholder="Cari nama, username, atau NIK KTP..."><button class="btn btn-primary btn-sm">Cari</button></form></div>

    @if ($loans->isEmpty())
        <div class="empty card"><i class="fa-solid fa-book-open"></i><p>Tidak ada peminjaman aktif.</p></div>
    @else
        <div class="tbl-wrap"><table class="tbl">
            <thead><tr><th>ID</th><th>Anggota & Jaminan ID</th><th>Buku</th><th>Jatuh Tempo & Deteksi</th><th>Status</th><th>Notifikasi & Aksi</th></tr></thead>
            <tbody>@foreach ($loans as $loan)
                <tr>
                    <td>#{{ $loan->id }}</td>
                    <td>
                        <b>{{ $loan->member->name }}</b> <span class="muted small">(&#64;{{ $loan->member->username }})</span><br>
                        <span class="muted small"><i class="fa-solid fa-id-card"></i> {{ $loan->member->identity_type ?? 'KTP' }}: {{ $loan->member->identity_number ?? '-' }}</span>
                        @if ($loan->identity_verified) <span class="badge b-aktif" style="font-size:10px;padding:2px 6px">ID Terverifikasi</span> @endif
                    </td>
                    <td>{{ $loan->details->map(fn ($d) => $d->book->title)->implode(', ') }}</td>
                    <td>
                        {{ $loan->due_date->format('d/m/Y') }}<br>
                        <span class="small {{ $loan->isOverdue() ? 'danger' : ($loan->isDueSoon() ? 'warn' : 'muted') }}">
                            <b>{{ $loan->dueStatusLabel() }}</b>
                        </span>
                    </td>
                    <td><x-badge :status="$loan->displayStatus()" /></td>
                    <td>
                        <div class="row" style="gap:6px;flex-wrap:wrap">
                            <form method="POST" action="{{ route('petugas.remind', $loan) }}">@csrf
                                <button class="btn btn-outline btn-sm" title="Kirim notifikasi peringatan jatuh tempo ke akun anggota">
                                    <i class="fa-solid fa-bell"></i> {{ $loan->due_notified_at ? 'Kirim Ulang' : 'Ingatkan' }}
                                </button>
                            </form>
                            <a href="{{ route('petugas.pengembalian.show', $loan) }}" class="btn btn-primary btn-sm">Proses Kembali</a>
                        </div>
                        @if ($loan->due_notified_at)
                            <div class="muted small" style="margin-top:4px"><i class="fa-solid fa-check ok"></i> Diingatkan {{ $loan->due_notified_at->format('d/m H:i') }}</div>
                        @endif
                    </td>
                </tr>
            @endforeach</tbody>
        </table></div>
        @include('partials.pagination', ['p' => $loans])
    @endif
</div>
@endsection
