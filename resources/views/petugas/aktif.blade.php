@extends('layouts.app')
@section('title', 'Peminjaman Aktif')

@section('content')
<div class="container">
    <div class="page-head"><h1>Peminjaman Aktif</h1>
        <form method="GET" class="search" style="min-width:280px"><i class="fa-solid fa-magnifying-glass"></i>
            <input name="q" value="{{ request('q') }}" placeholder="Cari anggota..."><button class="btn btn-primary btn-sm">Cari</button></form></div>

    @if ($loans->isEmpty())
        <div class="empty card"><i class="fa-solid fa-book-open"></i><p>Tidak ada peminjaman aktif.</p></div>
    @else
        <div class="tbl-wrap"><table class="tbl">
            <thead><tr><th>ID</th><th>Anggota</th><th>Buku</th><th>Dipinjam</th><th>Jatuh tempo</th><th>Status</th><th></th></tr></thead>
            <tbody>@foreach ($loans as $loan)
                <tr><td>#{{ $loan->id }}</td><td>{{ $loan->member->name }}</td>
                    <td>{{ $loan->details->map(fn ($d) => $d->book->title)->implode(', ') }}</td>
                    <td>{{ $loan->loan_date->format('d/m/Y') }}</td><td>{{ $loan->due_date->format('d/m/Y') }}</td>
                    <td><x-badge :status="$loan->displayStatus()" /></td>
                    <td><a href="{{ route('petugas.pengembalian.show', $loan) }}" class="btn btn-outline btn-sm">Proses</a></td></tr>
            @endforeach</tbody>
        </table></div>
        @include('partials.pagination', ['p' => $loans])
    @endif
</div>
@endsection
