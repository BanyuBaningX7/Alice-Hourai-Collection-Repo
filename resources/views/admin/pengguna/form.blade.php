@extends('layouts.admin')
@php $edit = $user->exists; @endphp
@section('title', $edit ? 'Ubah Akun' : 'Tambah Akun')
@section('heading', $edit ? 'Ubah Akun' : 'Tambah Akun')

@section('content')
<form method="POST" action="{{ $edit ? route('admin.pengguna.update', $user) : route('admin.pengguna.store') }}" enctype="multipart/form-data" class="card form-card">
    @csrf @if ($edit) @method('PUT') @endif

    @if ($errors->any())
        <div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation"></i>
            <ul style="margin:0;padding-left:18px">@foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach</ul></div>
    @endif

    <div class="grid g2">
        <div class="field"><label for="name">Nama</label>
            <input class="input" id="name" name="name" value="{{ old('name', $user->name) }}" required></div>
        <div class="field"><label for="username">Username</label>
            <input class="input" id="username" name="username" value="{{ old('username', $user->username) }}" required></div>
        <div class="field"><label for="email">Email</label>
            <input class="input" type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required></div>
        <div class="field"><label for="phone">Telepon / WhatsApp</label>
            <input class="input" id="phone" name="phone" value="{{ old('phone', $user->phone) }}"></div>
        <div class="field"><label for="identity_type">Kategori Kartu Identitas Jaminan</label>
            <select class="select" id="identity_type" name="identity_type">
                @foreach (\App\Models\User::IDENTITY_TYPES as $key => $label)
                    <option value="{{ $key }}" @selected(old('identity_type', $user->identity_type ?? 'KTP') === $key)>{{ $label }}</option>
                @endforeach
            </select></div>
        <div class="field"><label for="identity_number">Nomor Identitas</label>
            <input class="input {{ $errors->has('identity_number') ? 'is-error' : '' }}" id="identity_number" name="identity_number" value="{{ old('identity_number', $user->identity_number) }}">
            <div class="hint" id="id-hint"></div></div>
        <div class="field"><label for="role">Role</label>
            <select class="select" id="role" name="role">
                @foreach (['anggota', 'petugas', 'admin'] as $r) <option value="{{ $r }}" @selected(old('role', $user->role) === $r)>{{ ucfirst($r) }}</option> @endforeach
            </select></div>
        <div class="field"><label for="status">Status</label>
            <select class="select" id="status" name="status">
                @foreach (['aktif', 'nonaktif'] as $s) <option value="{{ $s }}" @selected(old('status', $user->status) === $s)>{{ ucfirst($s) }}</option> @endforeach
            </select></div>
    </div>

    <div class="field">
        <label for="identity_card">Foto Kartu Identitas {{ $user->identity_card_path ? '(kosongkan bila tidak diubah)' : '(opsional)' }}</label>
        <input class="input" type="file" id="identity_card" name="identity_card" accept=".jpg,.jpeg,.png">
    </div>

    <div class="field"><label for="password">Kata sandi {{ $edit ? '(kosongkan bila tidak diubah)' : '' }}</label>
        <input class="input" type="password" id="password" name="password" autocomplete="new-password" {{ $edit ? '' : 'required' }}>
        <div class="hint" id="pw-hint" data-role="{{ $user->role ?? 'anggota' }}"></div></div>
    <div class="field"><label for="password_confirmation">Konfirmasi kata sandi</label>
        <input class="input" type="password" id="password_confirmation" name="password_confirmation" autocomplete="new-password"></div>

    <div class="form-actions"><button class="btn btn-primary">Simpan</button><a href="{{ route('admin.pengguna.index') }}" class="btn btn-outline">Batal</a></div>
</form>
@endsection
