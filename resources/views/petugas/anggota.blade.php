@extends('layouts.app')
@section('title', 'Seluruh Anggota')

@section('content')
<div class="container">
    <div class="page-head"><div><h1>Seluruh Anggota</h1><span class="badge b-nonaktif">Hanya lihat</span></div>
        <form method="GET" class="row">
            <div class="search"><i class="fa-solid fa-magnifying-glass"></i><input name="q" value="{{ request('q') }}" placeholder="Cari anggota..."></div>
            <select name="status" class="select" style="width:auto" onchange="this.form.submit()">
                <option value="">Semua status</option>
                <option value="aktif" @selected(request('status') === 'aktif')>Aktif</option>
                <option value="nonaktif" @selected(request('status') === 'nonaktif')>Nonaktif</option>
            </select>
        </form></div>

    <div class="tbl-wrap"><table class="tbl">
        <thead><tr><th>Nama</th><th>Username</th><th>Email</th><th>Telepon</th><th>Transaksi</th><th>Status</th></tr></thead>
        <tbody>@forelse ($members as $m)
            <tr><td>{{ $m->name }}</td><td>&#64;{{ $m->username }}</td><td>{{ $m->email }}</td><td>{{ $m->phone ?? '-' }}</td><td>{{ $m->loans_count }}</td>
                <td><x-badge :status="$m->status" /> @if ($m->isSuspended()) <x-badge status="ditangguhkan" /> @endif</td></tr>
        @empty <tr><td colspan="6" class="empty">Anggota tidak ditemukan.</td></tr> @endforelse</tbody>
    </table></div>
    @include('partials.pagination', ['p' => $members])
</div>
@endsection
