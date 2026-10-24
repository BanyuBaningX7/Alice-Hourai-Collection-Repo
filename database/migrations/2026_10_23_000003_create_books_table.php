<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->restrictOnDelete();
            $table->string('title', 200)->index();
            $table->string('isbn', 20)->nullable()->unique();
            $table->string('author', 150)->index();
            $table->string('publisher', 150)->nullable();
            $table->unsignedSmallInteger('year')->nullable();
            $table->decimal('price', 12, 2);                 // harga buku = dasar denda
            $table->unsignedInteger('stock_total');
            $table->unsignedInteger('stock_available');
            $table->string('location', 50)->nullable();
            $table->text('description')->nullable();
            $table->string('cover_path')->nullable();
            $table->enum('status', ['aktif', 'nonaktif'])->default('aktif');
            $table->softDeletes();                           // "hapus" = arsipkan
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
