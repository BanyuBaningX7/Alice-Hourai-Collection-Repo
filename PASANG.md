# Alice Hourai Collection - paket kode Laravel

Folder ini adalah "tempelan" (overlay) untuk proyek Laravel 11/12 yang baru.
Panduan lengkap langkah demi langkah ada di file Word: Panduan_Implementasi_Alice_Hourai_Collection.docx

## Ringkasan 10 langkah
1. Pasang Laragon (PHP 8.2+, MySQL, Composer, Node.js) lalu jalankan Start All.
2. Buat proyek: `composer create-project laravel/laravel alice-hourai` di folder `C:\laragon\www`.
3. Buat database `alice_hourai` (HeidiSQL / phpMyAdmin).
4. Salin isi paket ini ke dalam folder proyek (gabungkan app, config, database, lang, public, resources, routes).
5. Atur `.env` mengikuti `snippets/env.txt`.
6. Daftarkan alias middleware `role` mengikuti `snippets/bootstrap-app.php.txt`.
7. Hapus migration bawaan Laravel yang tidak dipakai bila perlu (cache/jobs boleh dibiarkan).
8. `php artisan migrate --seed`
9. `php artisan storage:link`
10. Buka `http://alice-hourai.test` (Laragon) atau `php artisan serve`.

## Akun contoh (ganti setelah dipakai)
| Role | Username | Kata sandi |
|---|---|---|
| Admin | admin | AdminAlice@2026!PerpustakaanDigitalKu# |
| Petugas | petugas | Petugas#26 |
| Anggota | anggota | Anggota#26 |
