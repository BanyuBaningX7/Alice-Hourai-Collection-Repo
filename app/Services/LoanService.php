<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\Book;
use App\Models\Loan;
use App\Models\LoanDetail;
use App\Models\User;
use DomainException;
use Illuminate\Support\Facades\DB;

/**
 * Semua aturan peminjaman ada di sini supaya controller tetap pendek.
 * Setiap aturan yang dilanggar melempar DomainException (pesannya ditampilkan ke pengguna).
 */
class LoanService
{
    // Anggota mengajukan peminjaman (status: menunggu)
    public function request(User $member, array $bookIds): Loan
    {
        $bookIds = array_values(array_unique($bookIds));
        $max = config('library.max_books');

        if (empty($bookIds))                      throw new DomainException('Pilih minimal satu buku.');
        if ($member->status !== 'aktif')          throw new DomainException('Akun Anda tidak aktif.');
        if ($member->isSuspended())               throw new DomainException('Akses peminjaman ditangguhkan. Kembalikan buku yang terlambat atau lunasi denda.');
        if ($member->activeBookCount() + count($bookIds) > $max) {
            throw new DomainException("Maksimal {$max} buku aktif (diajukan atau dipinjam).");
        }

        $books = Book::aktif()->whereIn('id', $bookIds)->get();
        if ($books->count() !== count($bookIds)) throw new DomainException('Ada buku yang tidak tersedia.');
        foreach ($books as $book) {
            if ($book->stock_available < 1) throw new DomainException("Stok \"{$book->title}\" habis.");
        }

        return DB::transaction(function () use ($member, $books) {
            $loan = Loan::create(['member_id' => $member->id, 'requested_at' => now(), 'status' => 'menunggu']);
            foreach ($books as $book) {
                $loan->details()->create(['book_id' => $book->id]);
            }
            ActivityLog::record('loan.requested', 'loan', $loan->id);
            return $loan;
        });
    }

    // Anggota membatalkan pengajuan (hanya saat masih menunggu)
    public function cancel(Loan $loan, User $member): void
    {
        if ($loan->member_id !== $member->id) throw new DomainException('Pengajuan bukan milik Anda.');
        if ($loan->status !== 'menunggu')     throw new DomainException('Hanya pengajuan berstatus menunggu yang dapat dibatalkan.');

        $loan->update(['status' => 'dibatalkan']);
        ActivityLog::record('loan.cancelled', 'loan', $loan->id);
    }

    // Petugas menyetujui: cek stok ulang, ubah status, kurangi stok (satu transaksi database)
    public function approve(Loan $loan, User $petugas): void
    {
        DB::transaction(function () use ($loan, $petugas) {
            $loan = Loan::whereKey($loan->id)->lockForUpdate()->first();
            if ($loan->status !== 'menunggu') throw new DomainException('Pengajuan sudah diproses.');

            $books = Book::whereIn('id', $loan->details()->pluck('book_id'))->lockForUpdate()->get();
            foreach ($books as $book) {
                if ($book->stock_available < 1) throw new DomainException("Stok \"{$book->title}\" tidak mencukupi.");
            }
            foreach ($books as $book) {
                $book->decrement('stock_available');
            }

            $loan->update([
                'status'       => 'dipinjam',
                'approved_at'  => now(),
                'loan_date'    => today(),
                'due_date'     => today()->addDays(config('library.loan_days')),
                'processed_by' => $petugas->id,
            ]);
            ActivityLog::record('loan.approved', 'loan', $loan->id);
        });
    }

    // Petugas menolak dengan alasan
    public function reject(Loan $loan, User $petugas, string $reason): void
    {
        if ($loan->status !== 'menunggu') throw new DomainException('Pengajuan sudah diproses.');

        $loan->update(['status' => 'ditolak', 'notes' => $reason, 'processed_by' => $petugas->id]);
        ActivityLog::record('loan.rejected', 'loan', $loan->id, ['alasan' => $reason]);
    }

    // Petugas mencatat pengembalian satu buku dengan kondisinya: utuh / rusak / hilang
    public function returnItem(LoanDetail $detail, User $petugas, string $kondisi): void
    {
        DB::transaction(function () use ($detail, $petugas, $kondisi) {
            $detail = LoanDetail::whereKey($detail->id)->lockForUpdate()->first();
            if ($detail->item_status !== 'dipinjam') throw new DomainException('Item ini sudah diproses.');

            $loan = $detail->loan;
            if ($loan->status !== 'dipinjam') throw new DomainException('Transaksi tidak sedang dipinjam.');

            $book = Book::withTrashed()->whereKey($detail->book_id)->lockForUpdate()->first();
            $data = ['kondisi' => $kondisi, 'returned_at' => now(), 'received_by' => $petugas->id];

            if ($kondisi === 'utuh') {
                $data['item_status'] = 'dikembalikan';
                $book->increment('stock_available');            // stok kembali
            } else {
                // rusak / hilang: denda = harga buku, buku keluar dari stok
                $data['denda'] = $book->price;
                $data['item_status'] = $kondisi === 'hilang' ? 'hilang' : 'dikembalikan';
                $book->decrement('stock_total');
            }
            $detail->update($data);

            // Transaksi selesai bila tidak ada item yang masih dipinjam
            if ($loan->details()->where('item_status', 'dipinjam')->doesntExist()) {
                $loan->update(['status' => 'selesai']);
            }
            ActivityLog::record('loan.returned', 'loan_detail', $detail->id, ['kondisi' => $kondisi]);
        });
    }

    // Petugas mencatat denda lunas setelah memeriksa bukti pembayaran dari email anggota
    public function markFinePaid(LoanDetail $detail, User $petugas): void
    {
        if (! $detail->hasUnpaidFine()) throw new DomainException('Tidak ada denda yang belum dibayar.');

        $detail->update(['denda_lunas_at' => now()]);
        ActivityLog::record('fine.paid', 'loan_detail', $detail->id, ['petugas' => $petugas->id]);
    }
}
