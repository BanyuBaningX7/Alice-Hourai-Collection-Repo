<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()    { return view('auth.login'); }
    public function showRegister() { return view('auth.register'); }

    // Login memakai USERNAME + kata sandi
    public function login(Request $request)
    {
        $data = $request->validate(['username' => 'required|string', 'password' => 'required|string']);

        $user = User::where('username', $data['username'])->first();

        if ($user && Hash::check($data['password'], $user->password)) {
            if ($user->status === 'nonaktif') {
                return back()->withErrors(['username' => 'Akun Anda nonaktif. Hubungi admin.'])->onlyInput('username');
            }
            Auth::login($user, $request->boolean('remember'));
            $request->session()->regenerate();

            return redirect()->intended(route($user->homeRoute()));
        }

        return back()->withErrors(['username' => 'Username atau kata sandi salah.'])->onlyInput('username');
    }

    // Registrasi publik: role selalu anggota. Kata sandi tepat 10 karakter.
    public function register(Request $request)
    {
        $data = $request->validate([
            'name'     => 'required|string|max:100',
            'username' => 'required|alpha_dash|max:50|unique:users,username',
            'email'    => 'required|email|max:150|unique:users,email',
            'phone'    => 'nullable|string|max:20',
            'password' => array_merge(['required', 'confirmed'], User::passwordRules('anggota')),
        ]);

        $user = User::create($data + ['role' => 'anggota', 'status' => 'aktif']);
        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('member.dashboard')->with('ok', 'Akun berhasil dibuat. Selamat datang!');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('logout.selesai');
    }
}
