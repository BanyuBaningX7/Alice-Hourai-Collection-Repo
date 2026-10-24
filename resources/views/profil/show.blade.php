@extends('layouts.app')
@section('title', 'Profil - Alice Hourai Collection')
@section('norail', '1')

@section('content')
<div class="container"><div class="profile-col">
    <div class="center" style="margin-bottom:24px">
        <div class="avatar-lg"><i class="fa-regular fa-user"></i></div>
        <h1 style="margin-bottom:2px">{{ $user->name }}</h1>
        <div class="muted">&#64;{{ $user->username }} &middot; {{ $user->email }}</div>
        <div class="row" style="justify-content:center;margin-top:8px">
            <x-badge :status="$user->role" />
            <x-badge :status="$user->status" />
            @if ($user->isSuspended()) <x-badge status="ditangguhkan" /> @endif
        </div>
    </div>

    @if ($user->isAdmin())
        <a href="{{ route('admin.dashboard') }}" class="card menu-item"><i class="fa-solid fa-gauge accent"></i> Admin Panel <i class="fa-solid fa-chevron-right chev"></i></a>
    @endif
    <a href="{{ route('profil.edit') }}" class="card menu-item"><i class="fa-solid fa-pen accent"></i> Edit Profil <i class="fa-solid fa-chevron-right chev"></i></a>
    <a href="{{ route('profil.password') }}" class="card menu-item"><i class="fa-solid fa-lock accent"></i> Ubah Kata Sandi <i class="fa-solid fa-chevron-right chev"></i></a>

    <div class="card mt">
        <h3>Pengaturan Akun</h3>
        <dl class="info-grid" style="margin:0">
            <div><dt>Role</dt><dd>{{ ucfirst($user->role) }}</dd></div>
            <div><dt>Status akun</dt><dd>{{ ucfirst($user->status) }}</dd></div>
            <div><dt>Telepon</dt><dd>{{ $user->phone ?? '-' }}</dd></div>
            <div><dt>Aturan kata sandi</dt><dd>{{ $user->isAdmin() ? 'Minimal 30 karakter' : 'Tepat 10 karakter' }}</dd></div>
        </dl>
        @if ($user->isAnggota() && $user->isSuspended())
            <div class="alert alert-warn mt"><i class="fa-solid fa-triangle-exclamation"></i> Peminjaman ditangguhkan. Lihat <a class="accent" href="{{ route('member.denda') }}">Tagihan Denda</a>.</div>
        @endif
    </div>

    <form method="POST" action="{{ route('logout') }}" data-confirm="Keluar dari akun?" data-yes="Keluar" class="mt">@csrf
        <button class="btn btn-danger btn-block">Logout</button>
    </form>
</div></div>
@endsection
