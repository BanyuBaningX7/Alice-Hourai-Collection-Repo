@extends('layouts.admin')
@section('title', 'Akun & Role')
@section('heading', 'Akun & Role')

@section('content')
<div class="page-head">
    <form method="GET" class="row">
        <div class="search"><i class="fa-solid fa-magnifying-glass"></i><input name="q" value="{{ request('q') }}" placeholder="Cari username, nama, email"></div>
        <select name="role" class="select" style="width:auto" onchange="this.form.submit()">
            <option value="">Semua role</option>
            @foreach (['admin', 'petugas', 'anggota'] as $r) <option value="{{ $r }}" @selected(request('role') === $r)>{{ ucfirst($r) }}</option> @endforeach
        </select>
        <select name="status" class="select" style="width:auto" onchange="this.form.submit()">
            <option value="">Semua status</option>
            <option value="aktif" @selected(request('status') === 'aktif')>Aktif</option>
            <option value="nonaktif" @selected(request('status') === 'nonaktif')>Nonaktif</option>
        </select>
    </form>
    <a href="{{ route('admin.pengguna.create') }}" class="btn btn-primary btn-sm">+ Tambah Akun</a>
</div>

<div class="tbl-wrap"><table class="tbl">
    <thead><tr><th>Username</th><th>Nama</th><th>Email</th><th>Role</th><th>Status</th><th>Aksi</th></tr></thead>
    <tbody>@forelse ($users as $u)
        <tr>
            <td>&#64;{{ $u->username }}</td><td>{{ $u->name }}</td><td>{{ $u->email }}</td>
            <td><x-badge :status="$u->role" /></td>
            <td><x-badge :status="$u->status" /> @if ($u->isSuspended()) <x-badge status="ditangguhkan" /> @endif</td>
            <td><div class="actions">
                <a href="{{ route('admin.pengguna.edit', $u) }}" class="link-btn" title="Ubah"><i class="fa-solid fa-pen"></i></a>
                <form method="POST" action="{{ route('admin.pengguna.status', $u) }}" data-confirm="{{ $u->status === 'aktif' ? 'Nonaktifkan' : 'Aktifkan' }} akun {{ $u->username }}?">@csrf @method('PATCH')
                    <button class="link-btn" title="{{ $u->status === 'aktif' ? 'Nonaktifkan' : 'Aktifkan' }}"><i class="fa-solid fa-{{ $u->status === 'aktif' ? 'user-slash' : 'user-check' }}"></i></button></form>
                <form method="POST" action="{{ route('admin.pengguna.destroy', $u) }}" data-confirm="Hapus akun {{ $u->username }}? Hanya akun tanpa riwayat transaksi yang dapat dihapus." data-yes="Hapus">@csrf @method('DELETE')
                    <button class="link-btn danger" title="Hapus"><i class="fa-solid fa-trash"></i></button></form>
            </div></td>
        </tr>
    @empty <tr><td colspan="6" class="empty">Akun tidak ditemukan.</td></tr> @endforelse</tbody>
</table></div>
@include('partials.pagination', ['p' => $users])

<div class="card mt2">
    <h3>Matriks hak akses (ringkas)</h3>
    <div class="tbl-wrap" style="border:0"><table class="tbl">
        <thead><tr><th>Fitur</th><th>Anggota</th><th>Petugas</th><th>Admin</th></tr></thead>
        <tbody>
        @foreach ([['Ajukan & batalkan peminjaman', 1, 0, 0], ['Setujui / tolak peminjaman', 0, 1, 0], ['Catat pengembalian & denda', 0, 1, 0],
                   ['Lihat seluruh anggota & transaksi', 0, 1, 1], ['Kelola buku & kategori', 0, 0, 1], ['Admin Panel (akun & role)', 0, 0, 1], ['Log aktivitas', 0, 0, 1]] as [$f, $a, $p, $d])
            <tr><td>{{ $f }}</td><td>{!! $a ? '<i class="fa-solid fa-check ok"></i>' : '-' !!}</td><td>{!! $p ? '<i class="fa-solid fa-check ok"></i>' : '-' !!}</td><td>{!! $d ? '<i class="fa-solid fa-check ok"></i>' : '-' !!}</td></tr>
        @endforeach
        </tbody>
    </table></div>
</div>
@endsection
