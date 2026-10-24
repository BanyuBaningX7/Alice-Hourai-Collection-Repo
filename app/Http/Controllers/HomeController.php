<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Category;
use App\Models\User;

class HomeController extends Controller
{
    // Beranda: pencarian, kategori, buku unggulan, cara meminjam
    public function index()
    {
        $categories = Category::withCount(['books' => fn ($q) => $q->aktif()])->orderBy('name')->get();
        $featured   = Book::aktif()->with('category')->latest()->take(4)->get();
        $stats = [
            'buku'     => Book::aktif()->count(),
            'kategori' => $categories->count(),
            'anggota'  => User::where('role', 'anggota')->count(),
        ];

        return view('beranda', compact('categories', 'featured', 'stats'));
    }
}
