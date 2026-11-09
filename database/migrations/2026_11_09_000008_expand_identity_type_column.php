<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('users', 'identity_type')) {
            try {
                DB::statement("ALTER TABLE users MODIFY COLUMN identity_type VARCHAR(30) NOT NULL DEFAULT 'KTP'");
            } catch (\Throwable $e) {
                // Ignore if SQLite or already varchar
            }
        }
    }

    public function down(): void
    {
        //
    }
};
