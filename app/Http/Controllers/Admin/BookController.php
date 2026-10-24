<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Book;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BookController extends Controller
{
    private function rules(?Book $book = null): array
    {
        return [
            'category_id' => 'required|exists:categories,id',
            'title'       => 'required|string|max:200',
            'author'      => 'required|string|max:150',
            'publisher'   => 'nullable|string|max:150',
            'year'        => 'nullable|integer|min:1000|max:2100',
            'isbn'        => ['nullable', 'string', 'max:20', Rule::unique('books', 'isbn')->ignore($book?->id)],
            'price'       => 'required|numeric|min:0',
            'stock_total' => 'required|integer|min:0',
            'location'    => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'status'      => 'required|in:aktif,nonaktif',
            'cover'       => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ];
    }

    public function index(Request $request)
    {
        $books = Book::with('category')
            ->search($request->q)
            ->when($request->category, fn ($q, $c) => $q->where('category_id', $c))
            ->when($request->status, fn ($q, $s) => $q->where('status', $s))
            ->orderBy('title')->paginate(10)->withQueryString();

        return view('admin.buku.index', ['books' => $books, 'categories' => Category::orderBy('name')->get()]);
    }

    public function create()
    {
        return view('admin.buku.form', [
            'book' => new Book(['status' => 'aktif', 'stock_total' => 1]),
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate($this->rules());
        $data['stock_available'] = $data['stock_total'];
        if ($request->hasFile('cover')) {
            $data['cover_path'] = $request->file('cover')->store('covers', 'public');
        }
        unset($data['cover']);

        $book = Book::create($data);
        ActivityLog::record('book.created', 'book', $book->id);

        return redirect()->route('admin.buku.index')->with('ok', 'Buku berhasil ditambahkan.');
    }

    public function edit(Book $book)
    {
        return view('admin.buku.form', ['book' => $book, 'categories' => Category::orderBy('name')->get()]);
    }

    public function update(Request $request, Book $book)
    {
        $data = $request->validate($this->rules($book));

        // Stok tersedia mengikuti stok total; buku yang sedang dipinjam tidak ikut berubah
        $borrowed = $book->stock_total - $book->stock_available;
        if ($data['stock_total'] < $borrowed) {
            return back()->withInput()->with('err', "Stok total tidak boleh di bawah jumlah buku yang sedang dipinjam ({$borrowed}).");
        }
        $data['stock_available'] = $data['stock_total'] - $borrowed;

        if ($request->hasFile('cover')) {
            $data['cover_path'] = $request->file('cover')->store('covers', 'public');
        }
        unset($data['cover']);

        $book->update($data);
        ActivityLog::record('book.updated', 'book', $book->id);

        return redirect()->route('admin.buku.index')->with('ok', 'Buku diperbarui.');
    }

    // Soft delete: buku diarsipkan, riwayat peminjaman tetap aman
    public function destroy(Book $book)
    {
        ActivityLog::record('book.archived', 'book', $book->id);
        $book->delete();

        return back()->with('ok', 'Buku diarsipkan.');
    }
}
