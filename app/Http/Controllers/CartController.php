<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Services\LoanService;
use DomainException;
use Illuminate\Http\Request;

// "Keranjang" pengajuan disimpan di session sampai anggota menekan Kirim Pengajuan
class CartController extends Controller
{
    public function show()
    {
        $ids   = session('cart', []);
        $books = Book::aktif()->with('category')->whereIn('id', $ids)->get();

        return view('member.ajukan', [
            'books'  => $books,
            'active' => auth()->user()->activeBookCount(),
            'max'    => config('library.max_books'),
        ]);
    }

    public function add(Book $book)
    {
        $cart = session('cart', []);
        $max  = config('library.max_books');

        if ($book->status !== 'aktif' || $book->stock_available < 1) {
            return back()->with('err', 'Buku tidak tersedia untuk dipinjam.');
        }
        if (! in_array($book->id, $cart)) {
            if (auth()->user()->activeBookCount() + count($cart) + 1 > $max) {
                return back()->with('err', "Maksimal {$max} buku aktif (diajukan atau dipinjam).");
            }
            $cart[] = $book->id;
        }
        session(['cart' => $cart]);

        return redirect()->route('ajukan')->with('ok', 'Buku ditambahkan ke pengajuan.');
    }

    public function remove(Book $book)
    {
        session(['cart' => array_values(array_diff(session('cart', []), [$book->id]))]);

        return back();
    }

    public function store(LoanService $loans)
    {
        try {
            $loans->request(auth()->user(), session('cart', []));
        } catch (DomainException $e) {
            return back()->with('err', $e->getMessage());
        }

        session()->forget('cart');

        return redirect()->route('member.riwayat')
            ->with('ok', 'Pengajuan terkirim, menunggu persetujuan petugas.');
    }
}
