@extends('layouts.plain')
@section('title', 'Masuk - Alice Hourai Collection')
@section('auth', '1')

@section('content')
<div class="auth-wrap">
    <a href="{{ route('beranda') }}" class="auth-back"><i class="fa-solid fa-chevron-left"></i> Kembali ke Beranda</a>
    <div class="card auth-card">
        <div class="logo">ALICE HOURAI</div>
        <h1 class="center">Masuk</h1>

        @error('username')
            <div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</div>
        @enderror

        <form method="POST" action="{{ route('login.post') }}">@csrf
            <div class="field">
                <label for="username">Username</label>
                <input class="input {{ $errors->has('username') ? 'is-error' : '' }}" id="username" name="username" value="{{ old('username') }}" required autofocus autocomplete="username">
            </div>
            <div class="field">
                <label for="login-pw">Kata sandi</label>
                <div class="pw-wrap">
                    <input class="input" type="password" id="login-pw" name="password" required autocomplete="current-password">
                    <button type="button" data-toggle-pw="login-pw" aria-label="Tampilkan kata sandi"><i class="fa-regular fa-eye"></i></button>
                </div>
            </div>
            <label class="check"><input type="checkbox" name="remember" value="1"> Ingat saya</label>
            <button class="btn btn-primary btn-block">Masuk</button>
        </form>
        <p class="center muted mt">Belum punya akun? <a class="accent" href="{{ route('register') }}">Daftar</a></p>
    </div>
</div>
@endsection
