<?php

namespace App\Models;

use Closure;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Validation\Rule;

class User extends Authenticatable
{
    use Notifiable;

    // Daftar kategori kartu identitas resmi beserta label penjelasannya
    public const IDENTITY_TYPES = [
        'KTP'           => 'KTP — Kartu Tanda Penduduk (NIK 16 Digit Angka)',
        'Kartu Pelajar' => 'Kartu Pelajar — Siswa Sekolah (NISN 10 Digit Angka)',
        'KTM'           => 'KTM — Kartu Tanda Mahasiswa (NIM 8–15 Karakter)',
        'SIM'           => 'SIM — Surat Izin Mengemudi (12–16 Digit Angka)',
        'Paspor'        => 'Paspor — Dokumen Perjalanan Resmi (8–9 Karakter)',
    ];

    protected $fillable = [
        'name', 'username', 'email', 'phone',
        'identity_type', 'identity_number', 'identity_card_path',
        'password', 'role', 'status',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return ['password' => 'hashed'];
    }

    // ---------- Role ----------
    public function isAdmin(): bool   { return $this->role === 'admin'; }
    public function isPetugas(): bool { return $this->role === 'petugas'; }
    public function isAnggota(): bool { return $this->role === 'anggota'; }

    public function homeRoute(): string
    {
        return match ($this->role) {
            'admin'   => 'admin.dashboard',
            'petugas' => 'petugas.dashboard',
            default   => 'member.dashboard',
        };
    }

    public static function passwordRules(string $role): array
    {
        return $role === 'admin'
            ? ['string', 'min:30']
            : ['string', 'size:10'];
    }

    // ---------- Validasi Format & Batas Nomor Identitas Sesuai Kategori ----------
    public static function identityTypeKeys(): array
    {
        return array_keys(self::IDENTITY_TYPES);
    }

    public static function identityNumberRules(string $type, ?int $ignoreUserId = null, bool $required = true): array
    {
        $uniqueRule = Rule::unique('users', 'identity_number');
        if ($ignoreUserId) {
            $uniqueRule->ignore($ignoreUserId);
        }

        return [
            $required ? 'required' : 'nullable',
            'string',
            $uniqueRule,
            function (string $attribute, mixed $value, Closure $fail) use ($type) {
                $v = trim((string) $value);
                if ($v === '') return;

                switch ($type) {
                    case 'KTP':
                        if (! preg_match('/^\d{16}$/', $v)) {
                            $fail('Nomor NIK KTP wajib berisi tepat 16 digit angka.');
                            return;
                        }
                        if (preg_match('/^(\d)\1{15}$/', $v) || $v === '1234567890123456' || str_starts_with($v, '00')) {
                            $fail('Nomor NIK KTP tidak valid (tidak boleh berisi angka urut/kembar asal seperti 1234567890123456).');
                        }
                        break;

                    case 'Kartu Pelajar':
                        if (! preg_match('/^\d{10}$/', $v)) {
                            $fail('Nomor NISN Kartu Pelajar wajib berisi tepat 10 digit angka.');
                            return;
                        }
                        if (preg_match('/^(\d)\1{9}$/', $v) || $v === '1234567890') {
                            $fail('Nomor NISN Kartu Pelajar tidak valid (tidak boleh berisi angka urut/kembar asal).');
                        }
                        break;

                    case 'KTM':
                        if (! preg_match('/^[A-Za-z0-9]{8,15}$/', $v)) {
                            $fail('Nomor NIM Kartu Mahasiswa (KTM) wajib berisi 8 s/d 15 karakter huruf/angka tanpa spasi.');
                            return;
                        }
                        if (preg_match('/^(.)\1+$/', $v) || str_starts_with($v, '12345678')) {
                            $fail('Nomor NIM Kartu Mahasiswa tidak valid (tidak boleh karakter urut/kembar asal).');
                        }
                        break;

                    case 'SIM':
                        if (! preg_match('/^\d{12,16}$/', $v)) {
                            $fail('Nomor SIM wajib berisi 12 s/d 16 digit angka.');
                            return;
                        }
                        if (preg_match('/^(\d)\1+$/', $v) || str_starts_with($v, '123456789')) {
                            $fail('Nomor SIM tidak valid (tidak boleh berisi angka urut/kembar asal).');
                        }
                        break;

                    case 'Paspor':
                        if (! preg_match('/^[A-Za-z]{1,2}\d{7}$/', $v)) {
                            $fail('Nomor Paspor wajib diawali 1–2 huruf diikuti tepat 7 digit angka (contoh: C1234567).');
                        }
                        break;

                    default:
                        if (strlen($v) < 8 || strlen($v) > 20) {
                            $fail('Nomor identitas wajib antara 8 s/d 20 karakter.');
                        }
                }
            },
        ];
    }

    public function hasIdentityGuarantee(): bool
    {
        return ! empty($this->identity_number);
    }

    public function identityCategoryLabel(): string
    {
        return self::IDENTITY_TYPES[$this->identity_type ?? 'KTP'] ?? ($this->identity_type ?? 'KTP');
    }

    // ---------- Relasi ----------
    public function loans()          { return $this->hasMany(Loan::class, 'member_id'); }
    public function processedLoans() { return $this->hasMany(Loan::class, 'processed_by'); }

    // ---------- Aturan peminjaman ----------
    public function activeBookCount(): int
    {
        return LoanDetail::where('item_status', 'dipinjam')
            ->whereHas('loan', fn ($q) => $q->where('member_id', $this->id)
                                           ->whereIn('status', ['menunggu', 'dipinjam']))
            ->count();
    }

    public function hasOverdueBook(): bool
    {
        return LoanDetail::where('item_status', 'dipinjam')
            ->whereHas('loan', fn ($q) => $q->where('member_id', $this->id)
                                           ->where('status', 'dipinjam')
                                           ->whereDate('due_date', '<', today()))
            ->exists();
    }

    public function hasUnpaidFine(): bool
    {
        return LoanDetail::where('denda', '>', 0)->whereNull('denda_lunas_at')
            ->whereHas('loan', fn ($q) => $q->where('member_id', $this->id))
            ->exists();
    }

    public function isSuspended(): bool
    {
        return $this->isAnggota() && ($this->hasOverdueBook() || $this->hasUnpaidFine());
    }

    public function dueNotifications(): Collection
    {
        $warnDays = (int) config('library.due_warning_days', 2);
        $limitDate = today()->addDays($warnDays);

        if ($this->isAnggota()) {
            return Loan::with('details.book')
                ->where('member_id', $this->id)
                ->where('status', 'dipinjam')
                ->where(function ($q) use ($limitDate) {
                    $q->whereDate('due_date', '<=', $limitDate)
                      ->orWhereNotNull('due_notified_at');
                })
                ->orderBy('due_date')
                ->get();
        }

        return Loan::with('member', 'details.book')
            ->where('status', 'dipinjam')
            ->whereDate('due_date', '<=', $limitDate)
            ->orderBy('due_date')
            ->take(10)
            ->get();
    }
}
