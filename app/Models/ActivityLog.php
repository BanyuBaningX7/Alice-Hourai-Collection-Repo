<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    public $timestamps = false;

    protected $fillable = ['user_id', 'action', 'entity_type', 'entity_id', 'metadata', 'created_at'];

    protected $casts = ['metadata' => 'array', 'created_at' => 'datetime'];

    public function user() { return $this->belongsTo(User::class); }

    // Catat aksi penting: ActivityLog::record('loan.approved', 'loan', $loan->id)
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
}
