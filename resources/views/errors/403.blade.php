@extends('layouts.plain')
@section('title', 'Akses ditolak')
@section('content')
<div class="auth-wrap"><div class="card auth-card center">
    <i class="fa-solid fa-shield-halved accent" style="font-size:44px;margin-bottom:12px"></i>
    <h1>Akses ditolak</h1>
    <p class="muted">Anda tidak memiliki izin untuk membuka halaman ini.</p>
    <a href="{{ route('beranda') }}" class="btn btn-primary">Kembali ke Beranda</a>
</div></div>
@endsection
