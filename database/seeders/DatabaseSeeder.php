<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ---------- Akun contoh (GANTI kata sandi setelah dipakai) ----------
        // Admin: minimal 30 karakter. Petugas & anggota: tepat 10 karakter.
        User::create(['name' => 'Administrator', 'username' => 'admin', 'email' => 'admin@alice.test',
            'password' => 'AdminAlice@2026!PerpustakaanDigitalKu#', 'role' => 'admin']);
        User::create(['name' => 'Petugas Satu', 'username' => 'petugas', 'email' => 'petugas@alice.test',
            'password' => 'Petugas#26', 'role' => 'petugas']);
        User::create(['name' => 'Anggota Satu', 'username' => 'anggota', 'email' => 'anggota@alice.test',
            'phone' => '081234567890', 'password' => 'Anggota#26', 'role' => 'anggota']);

        // ---------- Kategori ----------
        $names = ['Fiksi', 'Petualangan', 'Fiksi Ilmiah', 'Inspirasi', 'Sejarah', 'Bisnis', 'Biografi', 'Klasik'];
        $cat = [];
        foreach ($names as $n) {
            $cat[$n] = Category::create(['name' => $n, 'description' => "Koleksi buku kategori {$n}."]);
        }

        // ---------- Buku contoh ----------
        $books = [
            ['Jejak Awan',      'Dewi Kusumawati', 'Fiksi',        '9786020000011', 65000, 3],
            ['Hutan Biru',      'Bima Saputra',    'Petualangan',  '9786020000028', 72000, 3],
            ['Ujung Galaksi',   'Siti Rahayu',     'Fiksi Ilmiah', '9786020000035', 85000, 3],
            ['Cahaya Senja',    'Ahmad Fauzi',     'Inspirasi',    '9786020000042', 58000, 2],
            ['Nusantara Kuno',  'Rini Handayani',  'Sejarah',      '9786020000059', 90000, 2],
            ['Strategi Usaha',  'Budi Santoso',    'Bisnis',       '9786020000066', 78000, 4],
            ['Langkah Seorang Guru', 'Lina Marlina', 'Biografi',   '9786020000073', 69000, 2],
            ['Hikayat Lama',    'Pustaka Klasik',  'Klasik',       '9786020000080', 55000, 3],
        ];
        foreach ($books as [$title, $author, $category, $isbn, $price, $stock]) {
            Book::create([
                'category_id' => $cat[$category]->id, 'title' => $title, 'author' => $author,
                'publisher' => 'Penerbit Alice', 'year' => 2024, 'isbn' => $isbn, 'price' => $price,
                'stock_total' => $stock, 'stock_available' => $stock, 'location' => 'Rak A-' . rand(1, 9),
                'description' => "{$title} adalah buku contoh karya {$author} untuk keperluan pengujian aplikasi.",
            ]);
        }
    }
}
