<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoanDetail extends Model
{
    protected $fillable = ['loan_id', 'book_id', 'received_by', 'item_status', 'kondisi',
                           'denda', 'denda_lunas_at', 'returned_at'];

    protected $casts = [
        'denda'          => 'decimal:2',
        'denda_lunas_at' => 'datetime',
        'returned_at'    => 'datetime',
    ];

    public function loan()     { return $this->belongsTo(Loan::class); }
    public function book()     { return $this->belongsTo(Book::class)->withTrashed(); }
    public function receiver() { return $this->belongsTo(User::class, 'received_by'); }

    public function hasUnpaidFine(): bool
    {
        return $this->denda > 0 && is_null($this->denda_lunas_at);
    }
}
