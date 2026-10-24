@extends('layouts.app')
@section('title', 'Ubah Kata Sandi - Alice Hourai Collection')
@section('norail', '1')

@section('content')
<div class="container"><div class="profile-col">
    <h1>Ubah Kata Sandi</h1>
    <form method="POST" action="{{ route('profil.password.update') }}" class="card">@csrf @method('PUT')
        <div class="field"><label for="current_password">Kata sandi lama</label>
            <input class="input {{ $errors->has('current_password') ? 'is-error' : '' }}" type="password" id="current_password" name="current_password" required>
            @error('current_password') <div class="err-text">{{ $message }}</div> @enderror</div>
        <div class="field"><label for="password">Kata sandi baru</label>
            <input class="input {{ $errors->has('password') ? 'is-error' : '' }}" type="password" id="password" name="password" required>
            <div class="hint" id="pw-hint" data-role="{{ $user->role }}"></div>
            @error('password') <div class="err-text">{{ $message }}</div> @enderror</div>
        <div class="field"><label for="password_confirmation">Konfirmasi kata sandi baru</label>
            <input class="input" type="password" id="password_confirmation" name="password_confirmation" required></div>
        <div class="form-actions"><button class="btn btn-primary">Simpan</button><a href="{{ route('profil') }}" class="btn btn-outline">Batal</a></div>
    </form>
</div></div>
@endsection
