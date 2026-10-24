<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Category;
use Illuminate\Http\Request;

class KatalogController extends Controller
{
    // Daftar buku dengan pencarian (judul/penulis/ISBN) dan filter kategori
    public function index(Request $request)
    {
        $books = Book::aktif()->with('category')
            ->search($request->q)
            ->when($request->category, fn ($q, $c) => $q->where('category_id', $c))
            ->when($request->boolean('tersedia'), fn ($q) => $q->where('stock_available', '>', 0))
            ->orderBy('title')
            ->paginate(8)
            ->withQueryString();

        $categories = Category::orderBy('name')->get();

        return view('katalog.index', compact('books', 'categories'));
    }

    public function show(Book $book)
    {
        abort_if($book->status !== 'aktif', 404);

        $book->load('category');
        $related = Book::aktif()->where('category_id', $book->category_id)
            ->where('id', '!=', $book->id)->take(3)->get();

        return view('katalog.show', compact('book', 'related'));
    }
}
