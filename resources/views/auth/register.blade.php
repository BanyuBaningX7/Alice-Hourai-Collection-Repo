@extends('layouts.plain')
@section('title', 'Daftar - Alice Hourai Collection')
@section('auth', '1')

@section('content')
<div class="auth-wrap">
    <a href="{{ route('beranda') }}" class="auth-back"><i class="fa-solid fa-chevron-left"></i> Kembali ke Beranda</a>
    <div class="card auth-card wide">
        <div class="logo">ALICE HOURAI</div>
        <h1 class="center">Daftar Anggota</h1>

        @if ($errors->any())
            <div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation"></i>
                <ul style="margin:0;padding-left:18px">@foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach</ul></div>
        @endif

        <form method="POST" action="{{ route('register.post') }}" enctype="multipart/form-data">@csrf
            <div class="grid g2">
                <div class="field"><label for="name">Nama lengkap</label>
                    <input class="input {{ $errors->has('name') ? 'is-error' : '' }}" id="name" name="name" value="{{ old('name') }}" required></div>
                <div class="field"><label for="username">Username</label>
                    <input class="input {{ $errors->has('username') ? 'is-error' : '' }}" id="username" name="username" value="{{ old('username') }}" required></div>
                <div class="field"><label for="email">Email</label>
                    <input class="input {{ $errors->has('email') ? 'is-error' : '' }}" type="email" id="email" name="email" value="{{ old('email') }}" required></div>
                <div class="field"><label for="phone">Telepon / WhatsApp</label>
                    <input class="input" id="phone" name="phone" value="{{ old('phone') }}" placeholder="0812xxxxxxxx"></div>
                <div class="field"><label for="identity_type">Kategori Kartu Identitas Jaminan</label>
                    <select class="select" id="identity_type" name="identity_type" required>
                        @foreach (\App\Models\User::IDENTITY_TYPES as $key => $label)
                            <option value="{{ $key }}" @selected(old('identity_type', 'KTP') === $key)>{{ $label }}</option>
                        @endforeach
                    </select></div>
                <div class="field"><label for="identity_number">Nomor Induk Kependudukan / NIK KTP (Wajib 16 Digit)</label>
                    <input class="input {{ $errors->has('identity_number') ? 'is-error' : '' }}" id="identity_number" name="identity_number" value="{{ old('identity_number') }}" placeholder="Contoh: 3275012005080003" required>
                    <div class="hint" id="id-hint"></div></div>
            </div>
            <div class="field">
                <label for="identity_card">Unggah Foto Kartu Identitas (Jaminan Keamanan Peminjaman)</label>
                <input class="input" type="file" id="identity_card" name="identity_card" accept=".jpg,.jpeg,.png">
                <div class="hint">Wajib menunjukkan kartu identitas asli kepada Petugas saat pengambilan buku. (JPG/PNG maks 2MB).</div>
            </div>
            <div class="field"><label for="password">Kata sandi</label>
                <input class="input {{ $errors->has('password') ? 'is-error' : '' }}" type="password" id="password" name="password" required autocomplete="new-password">
                <div class="hint" id="pw-hint" data-role="anggota"></div></div>
            <div class="field"><label for="password_confirmation">Konfirmasi kata sandi</label>
                <input class="input" type="password" id="password_confirmation" name="password_confirmation" required autocomplete="new-password"></div>
            <button class="btn btn-primary btn-block">Daftar sebagai Anggota</button>
        </form>
        <p class="center muted mt">Sudah punya akun? <a class="accent" href="{{ route('login') }}">Masuk</a></p>
    </div>
</div>
@endsection
