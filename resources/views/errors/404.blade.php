@extends('layouts.plain')
@section('title', 'Halaman tidak ditemukan')
@section('content')
<div class="auth-wrap"><div class="card auth-card center">
    <i class="fa-solid fa-compass accent" style="font-size:44px;margin-bottom:12px"></i>
    <h1>Halaman tidak ditemukan</h1>
    <p class="muted">Alamat yang Anda buka tidak tersedia.</p>
    <a href="{{ route('beranda') }}" class="btn btn-primary">Kembali ke Beranda</a>
</div></div>
@endsection
