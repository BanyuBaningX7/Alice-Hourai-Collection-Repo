<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AuthController extends Controller
{
    public function showLogin()    { return view('auth.login'); }
    public function showRegister() { return view('auth.register'); }

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

    public function register(Request $request)
    {
        $idType = (string) $request->input('identity_type', 'KTP');
        $data = $request->validate([
            'name'            => 'required|string|max:100',
            'username'        => 'required|alpha_dash|max:50|unique:users,username',
            'email'           => 'required|email|max:150|unique:users,email',
            'phone'           => 'nullable|string|max:20',
            'identity_type'   => ['required', Rule::in(User::identityTypeKeys())],
            'identity_number' => User::identityNumberRules($idType, null, true),
            'identity_card'   => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'password'        => array_merge(['required', 'confirmed'], User::passwordRules('anggota')),
        ], [
            'identity_number.unique' => 'Nomor identitas ini sudah terdaftar pada akun lain.',
        ]);

        if ($request->hasFile('identity_card')) {
            $data['identity_card_path'] = $request->file('identity_card')->store('identities', 'public');
        }
        unset($data['identity_card']);

        $user = User::create($data + ['role' => 'anggota', 'status' => 'aktif']);
        ActivityLog::record('user.registered', 'user', $user->id, [
            'identity_type'   => $user->identity_type,
            'identity_number' => $user->identity_number,
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('member.dashboard')->with('ok', 'Akun & data jaminan identitas berhasil didaftarkan!');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('logout.selesai');
    }
}
