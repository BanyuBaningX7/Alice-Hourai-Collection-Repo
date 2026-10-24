<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    protected $fillable = ['member_id', 'processed_by', 'requested_at', 'approved_at',
                           'loan_date', 'due_date', 'status', 'notes'];

    protected $casts = [
        'requested_at' => 'datetime',
        'approved_at'  => 'datetime',
        'loan_date'    => 'date',
        'due_date'     => 'date',
    ];

    public function member()    { return $this->belongsTo(User::class, 'member_id'); }
    public function processor() { return $this->belongsTo(User::class, 'processed_by'); }
    public function details()   { return $this->hasMany(LoanDetail::class); }

    // Terlambat dihitung dari data, tidak disimpan
    public function isOverdue(): bool
    {
        return $this->status === 'dipinjam' && $this->due_date && $this->due_date->lt(today());
    }

    // Status yang ditampilkan (terlambat menggantikan dipinjam)
    public function displayStatus(): string
    {
        return $this->isOverdue() ? 'terlambat' : $this->status;
    }
}
