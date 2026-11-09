<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    protected $fillable = [
        'member_id', 'processed_by', 'requested_at', 'approved_at',
        'loan_date', 'due_date', 'status', 'identity_verified',
        'notes', 'due_notified_at', 'due_notification_note',
    ];

    protected $casts = [
        'requested_at'      => 'datetime',
        'approved_at'       => 'datetime',
        'loan_date'         => 'date',
        'due_date'          => 'date',
        'identity_verified' => 'boolean',
        'due_notified_at'   => 'datetime',
    ];

    public function member()    { return $this->belongsTo(User::class, 'member_id'); }
    public function processor() { return $this->belongsTo(User::class, 'processed_by'); }
    public function details()   { return $this->hasMany(LoanDetail::class); }

    // Selisih hari terhadap jatuh tempo (negatif = terlambat, 0 = hari ini, positif = sisa hari)
    public function daysRemaining(): ?int
    {
        if (! $this->due_date) return null;
        return (int) today()->diffInDays($this->due_date, false);
    }

    // Terlambat dihitung dari data, tidak disimpan
    public function isOverdue(): bool
    {
        return $this->status === 'dipinjam' && $this->due_date && $this->due_date->lt(today());
    }

    // Mendekati jatuh tempo (H-2, H-1, atau Hari-H)
    public function isDueSoon(): bool
    {
        if ($this->status !== 'dipinjam' || ! $this->due_date) return false;
        $days = $this->daysRemaining();
        return $days !== null && $days >= 0 && $days <= (int) config('library.due_warning_days', 2);
    }

    // Label keterangan sisa waktu jatuh tempo untuk UI Petugas & Anggota
    public function dueStatusLabel(): string
    {
        if ($this->status !== 'dipinjam' || ! $this->due_date) return '-';
        $days = $this->daysRemaining();
        if ($days < 0)  return 'Terlambat ' . abs($days) . ' hari';
        if ($days === 0) return 'Jatuh tempo hari ini!';
        if ($days <= (int) config('library.due_warning_days', 2)) return "Segera jatuh tempo (H-{$days})";
        return "Sisa {$days} hari";
    }

    // Status yang ditampilkan (terlambat menggantikan dipinjam)
    public function displayStatus(): string
    {
        return $this->isOverdue() ? 'terlambat' : $this->status;
    }
}
