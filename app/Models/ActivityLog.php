<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    public $timestamps = false;

    protected $fillable = ['user_id', 'action', 'entity_type', 'entity_id', 'metadata', 'created_at'];

    protected $casts = ['metadata' => 'array', 'created_at' => 'datetime'];

    // Kamus terjemahan kode aksi ke Bahasa Indonesia (Plain Text)
    public const ACTION_LABELS = [
        'loan.requested'        => 'Mengajukan peminjaman buku',
        'loan.approved'         => 'Menyetujui peminjaman buku',
        'loan.rejected'         => 'Menolak pengajuan peminjaman',
        'loan.cancelled'        => 'Membatalkan pengajuan peminjaman',
        'loan.returned'         => 'Mencatat pengembalian buku',
        'loan.due_reminded'     => 'Mengirim peringatan jatuh tempo',
        'fine.paid'             => 'Memverifikasi pelunasan denda',
        'book.created'          => 'Menambahkan koleksi buku baru',
        'book.updated'          => 'Memperbarui data buku',
        'book.archived'         => 'Mengarsipkan koleksi buku',
        'category.created'      => 'Menambahkan kategori buku',
        'category.updated'      => 'Memperbarui kategori buku',
        'category.deleted'      => 'Menghapus kategori buku',
        'user.created'          => 'Membuat akun pengguna baru',
        'user.registered'       => 'Mendaftar sebagai anggota baru',
        'user.updated'          => 'Memperbarui data akun pengguna',
        'user.profile_updated'  => 'Memperbarui profil & identitas jaminan',
        'user.password_changed' => 'Mengubah kata sandi akun',
        'user.status'           => 'Mengubah status aktif akun',
        'user.deleted'          => 'Menghapus akun pengguna',
    ];

    public const ENTITY_LABELS = [
        'loan'        => 'Transaksi Peminjaman',
        'loan_detail' => 'Item Buku Pinjaman',
        'book'        => 'Koleksi Buku',
        'category'    => 'Kategori Buku',
        'user'        => 'Akun Pengguna',
    ];

    public function user() { return $this->belongsTo(User::class); }

    public static function record(string $action, string $type, ?int $id = null, array $meta = []): void
    {
        static::create([
            'user_id'     => auth()->id(),
            'action'      => $action,
            'entity_type' => $type,
            'entity_id'   => $id,
            'metadata'    => $meta ?: null,
            'created_at'  => now(),
        ]);
    }

    // Mengubah kode aksi (mis. loan.approved) menjadi kalimat Bahasa Indonesia
    public function actionLabel(): string
    {
        if (isset(self::ACTION_LABELS[$this->action])) {
            return self::ACTION_LABELS[$this->action];
        }
        return ucfirst(str_replace(['.', '_'], ' ', (string) $this->action));
    }

    // Mengubah objek (mis. loan_detail #1) menjadi teks Bahasa Indonesia
    public function targetLabel(): string
    {
        $name = self::ENTITY_LABELS[$this->entity_type] ?? ucfirst(str_replace('_', ' ', (string) $this->entity_type));
        return $this->entity_id ? "{$name} #{$this->entity_id}" : $name;
    }

    // Mengubah metadata array/JSON menjadi kalimat biasa (bukan sintaks JSON/PHP)
    public function metaLabel(): string
    {
        if (empty($this->metadata) || ! is_array($this->metadata)) {
            return '-';
        }

        $parts = [];
        $meta = $this->metadata;

        if (! empty($meta['identity_type']) && ! empty($meta['identity_number'])) {
            $parts[] = "Jaminan {$meta['identity_type']}: {$meta['identity_number']}";
            unset($meta['identity_type'], $meta['identity_number']);
        } elseif (! empty($meta['identity_type'])) {
            $parts[] = "Jenis identitas: {$meta['identity_type']}";
            unset($meta['identity_type']);
        }

        foreach ($meta as $key => $val) {
            if ($val === null || $val === '') continue;
            $parts[] = match ($key) {
                'kondisi'           => 'Kondisi fisik buku: ' . ucfirst((string) $val),
                'alasan'            => 'Alasan penolakan: ' . $val,
                'role'              => 'Peran akun: ' . ucfirst((string) $val),
                'status'            => 'Status akun: ' . ucfirst((string) $val),
                'identity_verified' => $val ? 'Kartu identitas jaminan telah diverifikasi' : 'Identitas belum diverifikasi',
                'petugas'           => 'Diproses oleh petugas: ' . $val,
                'member_id'         => 'ID Anggota #' . $val,
                default             => ucfirst(str_replace('_', ' ', (string) $key)) . ': ' . (is_bool($val) ? ($val ? 'Ya' : 'Tidak') : $val),
            };
        }

        return $parts ? implode(' · ', $parts) : '-';
    }
}
