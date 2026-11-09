@extends('layouts.app')
@section('title', 'Edit Profil - Alice Hourai Collection')
@section('norail', '1')

@section('content')
<div class="container"><div class="profile-col">
    <h1>Edit Profil &amp; Jaminan Identitas</h1>

    @if ($errors->any())
        <div class="alert alert-danger" style="margin-bottom:16px"><i class="fa-solid fa-circle-exclamation"></i>
            <ul style="margin:0;padding-left:18px">@foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach</ul></div>
    @endif

    <form method="POST" action="{{ route('profil.update') }}" enctype="multipart/form-data" class="card">@csrf @method('PUT')
        <div class="field"><label for="name">Nama</label>
            <input class="input" id="name" name="name" value="{{ old('name', $user->name) }}" required>
            @error('name') <div class="err-text">{{ $message }}</div> @enderror</div>
        <div class="field"><label for="email">Email</label>
            <input class="input" type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required>
            @error('email') <div class="err-text">{{ $message }}</div> @enderror</div>
        <div class="field"><label for="phone">Telepon / WhatsApp</label>
            <input class="input" id="phone" name="phone" value="{{ old('phone', $user->phone) }}"></div>

        <div class="grid g2">
            <div class="field"><label for="identity_type">Kategori Kartu Identitas Jaminan</label>
                <select class="select" id="identity_type" name="identity_type" required>
                    @foreach (\App\Models\User::IDENTITY_TYPES as $key => $label)
                        <option value="{{ $key }}" @selected(old('identity_type', $user->identity_type ?? 'KTP') === $key)>{{ $label }}</option>
                    @endforeach
                </select></div>
            <div class="field"><label for="identity_number">Nomor Identitas</label>
                <input class="input {{ $errors->has('identity_number') ? 'is-error' : '' }}" id="identity_number" name="identity_number" value="{{ old('identity_number', $user->identity_number) }}" required>
                <div class="hint" id="id-hint"></div>
                @error('identity_number') <div class="err-text">{{ $message }}</div> @enderror</div>
        </div>

        <div class="field">
            <label for="identity_card">Foto Kartu Identitas {{ $user->identity_card_path ? '(kosongkan bila tidak diganti)' : '' }}</label>
            <input class="input" type="file" id="identity_card" name="identity_card" accept=".jpg,.jpeg,.png">
            @if ($user->identity_card_path)
                <div class="hint"><a class="accent" href="{{ asset('storage/' . $user->identity_card_path) }}" target="_blank">Lihat foto kartu identitas saat ini</a></div>
            @endif
        </div>

        <div class="field"><label>Username</label>
            <input class="input" value="{{ $user->username }}" disabled>
            <div class="hint">Username hanya dapat diubah oleh admin.</div></div>
        <div class="form-actions"><button class="btn btn-primary">Simpan</button><a href="{{ route('profil') }}" class="btn btn-outline">Batal</a></div>
    </form>
</div></div>
@endsection
