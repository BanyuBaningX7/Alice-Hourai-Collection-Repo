<?php

namespace App\Support;

use App\Models\User;

// Daftar menu navigasi per role. Ubah menu cukup di file ini.
class Menu
{
    // Setiap item: [label, nama route, pola route aktif, ikon Font Awesome]
    public static function items(?User $u): array
    {
        if (! $u) {
            return [['Beranda', 'beranda', 'beranda', 'fa-house'], ['Katalog', 'katalog.index', 'katalog.*', 'fa-book']];
        }
        if ($u->isAnggota()) {
            return [['Beranda', 'beranda', 'beranda', 'fa-house'], ['Katalog', 'katalog.index', 'katalog.*', 'fa-book'],
                    ['Riwayat Pinjam', 'member.riwayat', 'member.riwayat', 'fa-clock-rotate-left'],
                    ['Tagihan Denda', 'member.denda', 'member.denda', 'fa-receipt'],
                    ['Profil', 'profil', 'profil*', 'fa-user']];
        }
        if ($u->isPetugas()) {
            return [['Dashboard', 'petugas.dashboard', 'petugas.dashboard', 'fa-house'],
                    ['Pengajuan', 'petugas.pengajuan', 'petugas.pengajuan*', 'fa-clipboard-list'],
                    ['Peminjaman', 'petugas.aktif', 'petugas.aktif', 'fa-book-open'],
                    ['Pengembalian', 'petugas.pengembalian', 'petugas.pengembalian*', 'fa-rotate-left'],
                    ['Denda', 'petugas.denda', 'petugas.denda', 'fa-receipt'],
                    ['Anggota', 'petugas.anggota', 'petugas.anggota', 'fa-users'],
                    ['Laporan', 'petugas.laporan', 'petugas.laporan', 'fa-chart-column']];
        }
        // admin yang membuka halaman publik
        return [['Beranda', 'beranda', 'beranda', 'fa-house'], ['Katalog', 'katalog.index', 'katalog.*', 'fa-book'],
                ['Admin Panel', 'admin.dashboard', 'admin.*', 'fa-gauge']];
    }

    // Ikon di rail kanan: [route, pola aktif, ikon, judul]
    public static function rail(?User $u): array
    {
        if ($u && $u->isPetugas()) {
            return [['petugas.dashboard', 'petugas.dashboard', 'fa-house', 'Dashboard'],
                    ['petugas.pengajuan', 'petugas.pengajuan*', 'fa-clipboard-list', 'Pengajuan'],
                    ['petugas.pengembalian', 'petugas.pengembalian*', 'fa-rotate-left', 'Pengembalian'],
                    ['petugas.laporan', 'petugas.laporan', 'fa-chart-column', 'Laporan']];
        }
        $rail = [['beranda', 'beranda', 'fa-house', 'Beranda'], ['katalog.index', 'katalog.*', 'fa-book', 'Katalog']];
        if ($u && $u->isAnggota()) {
            $rail[] = ['member.riwayat', 'member.riwayat', 'fa-clock-rotate-left', 'Riwayat'];
        }
        $rail[] = $u
            ? [$u->isAdmin() ? 'admin.dashboard' : 'profil', 'profil*', 'fa-user', $u->isAdmin() ? 'Admin Panel' : 'Profil']
            : ['login', 'login', 'fa-user', 'Masuk'];

        return $rail;
    }
}
