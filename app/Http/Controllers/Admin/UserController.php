<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Loan;
use App\Models\LoanDetail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::query()
            ->when($request->q, fn ($q, $s) => $q->where(fn ($w) => $w
                ->where('username', 'like', "%{$s}%")->orWhere('name', 'like', "%{$s}%")->orWhere('email', 'like', "%{$s}%")
                ->orWhere('identity_number', 'like', "%{$s}%")))
            ->when($request->role, fn ($q, $r) => $q->where('role', $r))
            ->when($request->status, fn ($q, $s) => $q->where('status', $s))
            ->orderBy('name')->paginate(10)->withQueryString();

        return view('admin.pengguna.index', compact('users'));
    }

    public function create()
    {
        return view('admin.pengguna.form', ['user' => new User(['role' => 'anggota', 'status' => 'aktif', 'identity_type' => 'KTP'])]);
    }

    public function store(Request $request)
    {
        $idType = (string) $request->input('identity_type', 'KTP');
        $isMember = $request->input('role') === 'anggota';

        $data = $request->validate([
            'name'            => 'required|string|max:100',
            'username'        => 'required|alpha_dash|max:50|unique:users,username',
            'email'           => 'required|email|max:150|unique:users,email',
            'phone'           => 'nullable|string|max:20',
            'identity_type'   => ['required', Rule::in(User::identityTypeKeys())],
            'identity_number' => User::identityNumberRules($idType, null, $isMember),
            'identity_card'   => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'role'            => 'required|in:admin,petugas,anggota',
            'status'          => 'required|in:aktif,nonaktif',
            'password'        => array_merge(['required', 'confirmed'], User::passwordRules((string) $request->role)),
        ], [
            'identity_number.unique' => 'Nomor identitas ini sudah digunakan oleh akun lain.',
        ]);

        if ($request->hasFile('identity_card')) {
            $data['identity_card_path'] = $request->file('identity_card')->store('identities', 'public');
        }
        unset($data['identity_card']);

        $user = User::create($data);
        ActivityLog::record('user.created', 'user', $user->id, ['role' => $user->role]);

        return redirect()->route('admin.pengguna.index')->with('ok', 'Akun berhasil dibuat.');
    }

    public function edit(User $pengguna)
    {
        return view('admin.pengguna.form', ['user' => $pengguna]);
    }

    public function update(Request $request, User $pengguna)
    {
        $idType = (string) $request->input('identity_type', 'KTP');
        $isMember = $request->input('role') === 'anggota';

        $data = $request->validate([
            'name'            => 'required|string|max:100',
            'username'        => ['required', 'alpha_dash', 'max:50', Rule::unique('users')->ignore($pengguna->id)],
            'email'           => ['required', 'email', 'max:150', Rule::unique('users')->ignore($pengguna->id)],
            'phone'           => 'nullable|string|max:20',
            'identity_type'   => ['required', Rule::in(User::identityTypeKeys())],
            'identity_number' => User::identityNumberRules($idType, $pengguna->id, $isMember),
            'identity_card'   => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'role'            => 'required|in:admin,petugas,anggota',
            'status'          => 'required|in:aktif,nonaktif',
            'password'        => array_merge(['nullable', 'confirmed'], User::passwordRules((string) $request->role)),
        ], [
            'identity_number.unique' => 'Nomor identitas ini sudah digunakan oleh akun lain.',
        ]);

        if ($pengguna->id === auth()->id() && ($data['role'] !== 'admin' || $data['status'] !== 'aktif')) {
            return back()->withInput()->with('err', 'Anda tidak dapat menurunkan role atau menonaktifkan akun sendiri.');
        }

        if ($request->hasFile('identity_card')) {
            $data['identity_card_path'] = $request->file('identity_card')->store('identities', 'public');
        }
        unset($data['identity_card']);

        if (empty($data['password'])) unset($data['password']);
        $pengguna->update($data);
        ActivityLog::record('user.updated', 'user', $pengguna->id, ['role' => $pengguna->role]);

        return redirect()->route('admin.pengguna.index')->with('ok', 'Akun diperbarui.');
    }

    public function status(User $pengguna)
    {
        if ($pengguna->id === auth()->id()) {
            return back()->with('err', 'Anda tidak dapat menonaktifkan akun sendiri.');
        }
        $pengguna->update(['status' => $pengguna->status === 'aktif' ? 'nonaktif' : 'aktif']);
        ActivityLog::record('user.status', 'user', $pengguna->id, ['status' => $pengguna->status]);

        return back()->with('ok', 'Status akun diubah menjadi ' . $pengguna->status . '.');
    }

    public function destroy(User $pengguna)
    {
        if ($pengguna->id === auth()->id()) {
            return back()->with('err', 'Anda tidak dapat menghapus akun sendiri.');
        }
        $hasHistory = Loan::where('member_id', $pengguna->id)->orWhere('processed_by', $pengguna->id)->exists()
                    || LoanDetail::where('received_by', $pengguna->id)->exists();
        if ($hasHistory) {
            return back()->with('err', 'Akun punya riwayat transaksi. Gunakan Nonaktifkan.');
        }

        ActivityLog::record('user.deleted', 'user', $pengguna->id);
        $pengguna->delete();

        return back()->with('ok', 'Akun dihapus.');
    }
}
