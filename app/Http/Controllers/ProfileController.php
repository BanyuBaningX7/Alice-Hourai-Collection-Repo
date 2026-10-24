<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function show()
    {
        return view('profil.show', ['user' => auth()->user()]);
    }

    public function edit()
    {
        return view('profil.edit', ['user' => auth()->user()]);
    }

    // Username tidak dapat diubah sendiri (hanya lewat Admin Panel)
    public function update(Request $request)
    {
        $user = auth()->user();
        $data = $request->validate([
            'name'  => 'required|string|max:100',
            'email' => ['required', 'email', 'max:150', Rule::unique('users')->ignore($user->id)],
            'phone' => 'nullable|string|max:20',
        ]);
        $user->update($data);

        return redirect()->route('profil')->with('ok', 'Profil diperbarui.');
    }

    public function passwordForm()
    {
        return view('profil.password', ['user' => auth()->user()]);
    }

    // Admin: minimal 30 karakter. Anggota & petugas: tepat 10 karakter.
    public function passwordUpdate(Request $request)
    {
        $user = auth()->user();
        $request->validate([
            'current_password' => 'required|current_password',
            'password'         => array_merge(['required', 'confirmed'], User::passwordRules($user->role)),
        ]);
        $user->update(['password' => $request->password]);

        return redirect()->route('profil')->with('ok', 'Kata sandi diperbarui.');
    }
}
