<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\KatalogController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\PetugasController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Alice Hourai Collection - daftar route
| Urutan: publik -> akun -> anggota -> petugas -> admin
|--------------------------------------------------------------------------
*/

// ---------- Publik ----------
Route::get('/', [HomeController::class, 'index'])->name('beranda');
Route::get('/katalog', [KatalogController::class, 'index'])->name('katalog.index');
Route::get('/katalog/{book}', [KatalogController::class, 'show'])->name('katalog.show');

// ---------- Login, registrasi, logout ----------
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:10,1')->name('login.post');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');
});
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');
Route::view('/logout/selesai', 'auth.logout-selesai')->name('logout.selesai');

// ---------- Profil (semua role yang sudah login) ----------
Route::middleware('auth')->group(function () {
    Route::get('/profil', [ProfileController::class, 'show'])->name('profil');
    Route::get('/profil/edit', [ProfileController::class, 'edit'])->name('profil.edit');
    Route::put('/profil', [ProfileController::class, 'update'])->name('profil.update');
    Route::get('/profil/password', [ProfileController::class, 'passwordForm'])->name('profil.password');
    Route::put('/profil/password', [ProfileController::class, 'passwordUpdate'])->name('profil.password.update');
});

// ---------- Anggota ----------
Route::middleware(['auth', 'role:anggota'])->group(function () {
    Route::get('/dashboard', [MemberController::class, 'dashboard'])->name('member.dashboard');
    Route::get('/ajukan', [CartController::class, 'show'])->name('ajukan');
    Route::post('/ajukan', [CartController::class, 'store'])->name('ajukan.store');
    Route::post('/keranjang/{book}', [CartController::class, 'add'])->name('cart.add');
    Route::delete('/keranjang/{book}', [CartController::class, 'remove'])->name('cart.remove');
    Route::get('/riwayat', [MemberController::class, 'riwayat'])->name('member.riwayat');
    Route::post('/riwayat/{loan}/batal', [MemberController::class, 'cancel'])->name('member.batal');
    Route::get('/denda', [MemberController::class, 'denda'])->name('member.denda');
});

// ---------- Petugas (HANYA petugas yang dapat menyetujui peminjaman) ----------
Route::middleware(['auth', 'role:petugas'])->prefix('petugas')->name('petugas.')->group(function () {
    Route::get('/', [PetugasController::class, 'dashboard'])->name('dashboard');
    Route::get('/pengajuan', [PetugasController::class, 'pengajuan'])->name('pengajuan');
    Route::get('/pengajuan/{loan}', [PetugasController::class, 'pengajuanShow'])->name('pengajuan.show');
    Route::post('/pengajuan/{loan}/setujui', [PetugasController::class, 'approve'])->name('approve');
    Route::post('/pengajuan/{loan}/tolak', [PetugasController::class, 'reject'])->name('reject');
    Route::get('/pinjaman', [PetugasController::class, 'aktif'])->name('aktif');
    Route::get('/pengembalian', [PetugasController::class, 'pengembalian'])->name('pengembalian');
    Route::get('/pengembalian/{loan}', [PetugasController::class, 'pengembalianShow'])->name('pengembalian.show');
    Route::post('/pengembalian/item/{detail}', [PetugasController::class, 'kembali'])->name('kembali');
    Route::get('/denda', [PetugasController::class, 'denda'])->name('denda');
    Route::post('/denda/{detail}/lunas', [PetugasController::class, 'lunas'])->name('lunas');
    Route::get('/anggota', [PetugasController::class, 'anggota'])->name('anggota');
    Route::get('/transaksi', [ReportController::class, 'transaksi'])->name('transaksi');
    Route::get('/laporan', [ReportController::class, 'laporan'])->name('laporan');
});

// ---------- Admin Panel (hanya admin) ----------
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [Admin\DashboardController::class, 'index'])->name('dashboard');

    Route::resource('pengguna', Admin\UserController::class)->except('show');
    Route::patch('pengguna/{pengguna}/status', [Admin\UserController::class, 'status'])->name('pengguna.status');

    Route::resource('buku', Admin\BookController::class)->parameters(['buku' => 'book'])->except('show');
    Route::resource('kategori', Admin\CategoryController::class)->parameters(['kategori' => 'category'])->except(['show', 'create']);

    Route::get('/transaksi', [ReportController::class, 'transaksi'])->name('transaksi');
    Route::get('/laporan', [ReportController::class, 'laporan'])->name('laporan');
    Route::get('/log', [Admin\DashboardController::class, 'log'])->name('log');
});
