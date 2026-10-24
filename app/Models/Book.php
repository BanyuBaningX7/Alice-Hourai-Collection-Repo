<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Book extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'category_id', 'title', 'isbn', 'author', 'publisher', 'year', 'price',
        'stock_total', 'stock_available', 'location', 'description', 'cover_path', 'status',
    ];

    protected $casts = ['price' => 'decimal:2'];

    public function category()    { return $this->belongsTo(Category::class); }
    public function loanDetails() { return $this->hasMany(LoanDetail::class); }

    // Hanya buku aktif yang tampil di katalog
    public function scopeAktif($q) { return $q->where('status', 'aktif'); }

    // Pencarian judul, penulis, atau ISBN
    public function scopeSearch($q, ?string $term)
    {
        if (! $term) return $q;
        return $q->where(fn ($w) => $w->where('title', 'like', "%{$term}%")
                                      ->orWhere('author', 'like', "%{$term}%")
                                      ->orWhere('isbn', 'like', "%{$term}%"));
    }
}
