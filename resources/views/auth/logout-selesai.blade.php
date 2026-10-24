@extends('layouts.plain')
@section('title', 'Anda telah keluar')

@section('content')
<div class="auth-wrap">
    <div class="card auth-card center">
        <i class="fa-solid fa-circle-check ok" style="font-size:48px;margin-bottom:12px"></i>
        <h1>Anda telah keluar</h1>
        <p class="muted">Sesi Anda sudah berakhir dengan aman.</p>
        <div class="row" style="justify-content:center">
            <a href="{{ route('login') }}" class="btn btn-primary">Masuk Lagi</a>
            <a href="{{ route('beranda') }}" class="btn btn-outline">Ke Beranda</a>
        </div>
    </div>
</div>
@endsection
