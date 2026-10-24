<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('processed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('requested_at');
            $table->dateTime('approved_at')->nullable();
            $table->date('loan_date')->nullable();
            $table->date('due_date')->nullable()->index();
            $table->enum('status', ['menunggu', 'ditolak', 'dibatalkan', 'dipinjam', 'selesai'])
                  ->default('menunggu')->index();
            $table->text('notes')->nullable();               // alasan penolakan
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loans');
    }
};
