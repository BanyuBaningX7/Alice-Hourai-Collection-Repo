<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = ['name', 'username', 'email', 'phone', 'password', 'role', 'status'];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return ['password' => 'hashed'];   // kata sandi otomatis di-hash saat disimpan
    }

    // ---------- Role ----------
    public function isAdmin(): bool   { return $this->role === 'admin'; }
    public function isPetugas(): bool { return $this->role === 'petugas'; }
    public function isAnggota(): bool { return $this->role === 'anggota'; }

    // Halaman awal setelah login, sesuai role
    public function homeRoute(): string
    {
        return match ($this->role) {
            'admin'   => 'admin.dashboard',
            'petugas' => 'petugas.dashboard',
            default   => 'member.dashboard',
        };
    }

    // Aturan panjang kata sandi per role: admin minimal 30, lainnya tepat 10 karakter
    public static function passwordRules(string $role): array
    {
        return $role === 'admin'
            ? ['string', 'min:30']
            : ['string', 'size:10'];
    }

    // ---------- Relasi ----------
    public function loans()          { return $this->hasMany(Loan::class, 'member_id'); }
    public function processedLoans() { return $this->hasMany(Loan::class, 'processed_by'); }

    // ---------- Aturan peminjaman ----------
    // Jumlah buku aktif = buku pada pengajuan menunggu + yang sedang dipinjam
    public function activeBookCount(): int
    {
        return LoanDetail::where('item_status', 'dipinjam')
            ->whereHas('loan', fn ($q) => $q->where('member_id', $this->id)
                                           ->whereIn('status', ['menunggu', 'dipinjam']))
            ->count();
    }

    // Ada buku terlambat yang belum kembali
    public function hasOverdueBook(): bool
    {
        return LoanDetail::where('item_status', 'dipinjam')
            ->whereHas('loan', fn ($q) => $q->where('member_id', $this->id)
                                           ->where('status', 'dipinjam')
                                           ->whereDate('due_date', '<', today()))
            ->exists();
    }

    // Ada denda yang belum dibayar
    public function hasUnpaidFine(): bool
    {
        return LoanDetail::where('denda', '>', 0)->whereNull('denda_lunas_at')
            ->whereHas('loan', fn ($q) => $q->where('member_id', $this->id))
            ->exists();
    }

    // Penangguhan dihitung dari data (tidak disimpan di database)
    public function isSuspended(): bool
    {
        return $this->isAnggota() && ($this->hasOverdueBook() || $this->hasUnpaidFine());
    }
}
