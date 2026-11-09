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

    // Log aktivitas: mendukung pencarian dengan kata Bahasa Indonesia maupun nama pengguna
    public function log(Request $request)
    {
        $logs = ActivityLog::with('user')
            ->when($request->q, function ($q, $s) {
                $matchedActions = collect(ActivityLog::ACTION_LABELS)
                    ->filter(fn ($label) => stripos($label, $s) !== false)
                    ->keys()
                    ->all();

                $q->where(function ($w) use ($s, $matchedActions) {
                    $w->where('action', 'like', "%{$s}%")
                      ->orWhere('entity_type', 'like', "%{$s}%")
                      ->orWhereHas('user', fn ($u) => $u->where('username', 'like', "%{$s}%")->orWhere('name', 'like', "%{$s}%"));
                    if (! empty($matchedActions)) {
                        $w->orWhereIn('action', $matchedActions);
                    }
                });
            })
            ->when($request->dari, fn ($q, $d) => $q->whereDate('created_at', '>=', $d))
            ->latest('created_at')->paginate(15)->withQueryString();

        return view('admin.log', compact('logs'));
    }
}
