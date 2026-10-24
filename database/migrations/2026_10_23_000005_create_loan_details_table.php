<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loan_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loan_id')->constrained()->restrictOnDelete();
            $table->foreignId('book_id')->constrained()->restrictOnDelete();
            $table->foreignId('received_by')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('item_status', ['dipinjam', 'dikembalikan', 'hilang'])->default('dipinjam');
            $table->enum('kondisi', ['utuh', 'rusak', 'hilang'])->nullable();
            $table->decimal('denda', 12, 2)->nullable();     // = harga buku bila rusak/hilang
            $table->dateTime('denda_lunas_at')->nullable();
            $table->dateTime('returned_at')->nullable();
            $table->timestamps();
            $table->unique(['loan_id', 'book_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loan_details');
    }
};
