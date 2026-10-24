<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Loan;
use App\Models\LoanDetail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

// Admin Panel: kelola akun dan role (hanya admin)
class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::query()
            ->when($request->q, fn ($q, $s) => $q->where(fn ($w) => $w
                ->where('username', 'like', "%{$s}%")->orWhere('name', 'like', "%{$s}%")->orWhere('email', 'like', "%{$s}%")))
            ->when($request->role, fn ($q, $r) => $q->where('role', $r))
            ->when($request->status, fn ($q, $s) => $q->where('status', $s))
            ->orderBy('name')->paginate(10)->withQueryString();

        return view('admin.pengguna.index', compact('users'));
    }

    public function create()
    {
        return view('admin.pengguna.form', ['user' => new User(['role' => 'anggota', 'status' => 'aktif'])]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'     => 'required|string|max:100',
            'username' => 'required|alpha_dash|max:50|unique:users,username',
            'email'    => 'required|email|max:150|unique:users,email',
            'phone'    => 'nullable|string|max:20',
            'role'     => 'required|in:admin,petugas,anggota',
            'status'   => 'required|in:aktif,nonaktif',
            'password' => array_merge(['required', 'confirmed'], User::passwordRules((string) $request->role)),
        ]);

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
        $data = $request->validate([
            'name'     => 'required|string|max:100',
            'username' => ['required', 'alpha_dash', 'max:50', Rule::unique('users')->ignore($pengguna->id)],
            'email'    => ['required', 'email', 'max:150', Rule::unique('users')->ignore($pengguna->id)],
            'phone'    => 'nullable|string|max:20',
            'role'     => 'required|in:admin,petugas,anggota',
            'status'   => 'required|in:aktif,nonaktif',
            'password' => array_merge(['nullable', 'confirmed'], User::passwordRules((string) $request->role)),
        ]);

        // Admin tidak boleh menurunkan atau menonaktifkan dirinya sendiri
        if ($pengguna->id === auth()->id() && ($data['role'] !== 'admin' || $data['status'] !== 'aktif')) {
            return back()->withInput()->with('err', 'Anda tidak dapat menurunkan role atau menonaktifkan akun sendiri.');
        }

        if (empty($data['password'])) unset($data['password']);
        $pengguna->update($data);
        ActivityLog::record('user.updated', 'user', $pengguna->id, ['role' => $pengguna->role]);

        return redirect()->route('admin.pengguna.index')->with('ok', 'Akun diperbarui.');
    }

    // Aktifkan / nonaktifkan akun
    public function status(User $pengguna)
    {
        if ($pengguna->id === auth()->id()) {
            return back()->with('err', 'Anda tidak dapat menonaktifkan akun sendiri.');
        }
        $pengguna->update(['status' => $pengguna->status === 'aktif' ? 'nonaktif' : 'aktif']);
        ActivityLog::record('user.status', 'user', $pengguna->id, ['status' => $pengguna->status]);

        return back()->with('ok', 'Status akun diubah menjadi ' . $pengguna->status . '.');
    }

    // Hapus hanya untuk akun tanpa riwayat transaksi
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
