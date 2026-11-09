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

    public function update(Request $request)
    {
        $user = auth()->user();
        $idType = (string) $request->input('identity_type', 'KTP');

        $data = $request->validate([
            'name'            => 'required|string|max:100',
            'email'           => ['required', 'email', 'max:150', Rule::unique('users')->ignore($user->id)],
            'phone'           => 'nullable|string|max:20',
            'identity_type'   => ['required', Rule::in(User::identityTypeKeys())],
            'identity_number' => User::identityNumberRules($idType, $user->id, true),
            'identity_card'   => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ], [
            'identity_number.unique' => 'Nomor identitas ini sudah digunakan oleh akun lain.',
        ]);

        if ($request->hasFile('identity_card')) {
            $data['identity_card_path'] = $request->file('identity_card')->store('identities', 'public');
        }
        unset($data['identity_card']);

        $user->update($data);
        ActivityLog::record('user.profile_updated', 'user', $user->id, [
            'identity_type'   => $user->identity_type,
            'identity_number' => $user->identity_number,
        ]);

        return redirect()->route('profil')->with('ok', 'Profil dan data jaminan identitas diperbarui.');
    }

    public function passwordForm()
    {
        return view('profil.password', ['user' => auth()->user()]);
    }

    public function passwordUpdate(Request $request)
    {
        $user = auth()->user();
        $request->validate([
            'current_password' => 'required|current_password',
            'password'         => array_merge(['required', 'confirmed'], User::passwordRules($user->role)),
        ]);
        $user->update(['password' => $request->password]);
        ActivityLog::record('user.password_changed', 'user', $user->id);

        return redirect()->route('profil')->with('ok', 'Kata sandi diperbarui.');
    }
}
