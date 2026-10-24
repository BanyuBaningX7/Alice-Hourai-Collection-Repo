@extends('layouts.app')
@section('title', 'Edit Profil - Alice Hourai Collection')
@section('norail', '1')

@section('content')
<div class="container"><div class="profile-col">
    <h1>Edit Profil</h1>
    <form method="POST" action="{{ route('profil.update') }}" class="card">@csrf @method('PUT')
        <div class="field"><label for="name">Nama</label>
            <input class="input" id="name" name="name" value="{{ old('name', $user->name) }}" required>
            @error('name') <div class="err-text">{{ $message }}</div> @enderror</div>
        <div class="field"><label for="email">Email</label>
            <input class="input" type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required>
            @error('email') <div class="err-text">{{ $message }}</div> @enderror</div>
        <div class="field"><label for="phone">Telepon</label>
            <input class="input" id="phone" name="phone" value="{{ old('phone', $user->phone) }}"></div>
        <div class="field"><label>Username</label>
            <input class="input" value="{{ $user->username }}" disabled>
            <div class="hint">Username hanya dapat diubah oleh admin.</div></div>
        <div class="form-actions"><button class="btn btn-primary">Simpan</button><a href="{{ route('profil') }}" class="btn btn-outline">Batal</a></div>
    </form>
</div></div>
@endsection
