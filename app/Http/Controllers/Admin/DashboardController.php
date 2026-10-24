<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Book;
use App\Models\Category;
use App\Models\Loan;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'buku'      => Book::count(),
            'anggota'   => User::where('role', 'anggota')->count(),
            'aktif'     => Loan::where('status', 'dipinjam')->count(),
            'terlambat' => Loan::where('status', 'dipinjam')->whereDate('due_date', '<', today())->count(),
        ];

        // Peminjaman 6 bulan terakhir (dipakai untuk grafik batang CSS)
        $perMonth = collect(range(5, 0))->map(function ($i) {
            $start = now()->subMonths($i)->startOfMonth();
            return [
                'label' => $start->translatedFormat('M'),
                'total' => Loan::whereBetween('requested_at', [$start, $start->copy()->endOfMonth()])
                               ->whereIn('status', ['dipinjam', 'selesai'])->count(),
            ];
        });

        $perCategory = Category::withCount('books')->orderByDesc('books_count')->take(6)->get();
        $logs = ActivityLog::with('user')->latest('created_at')->take(6)->get();

        return view('admin.dashboard', compact('stats', 'perMonth', 'perCategory', 'logs'));
    }

    // Log aktivitas: hanya dibaca
    public function log(Request $request)
    {
        $logs = ActivityLog::with('user')
            ->when($request->q, fn ($q, $s) => $q->where('action', 'like', "%{$s}%"))
            ->when($request->dari, fn ($q, $d) => $q->whereDate('created_at', '>=', $d))
            ->latest('created_at')->paginate(15)->withQueryString();

        return view('admin.log', compact('logs'));
    }
}
