<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use App\Models\LoanDetail;
use Carbon\Carbon;
use Illuminate\Http\Request;

// Dipakai bersama oleh petugas dan admin (tampilan memakai layout sesuai role)
class ReportController extends Controller
{
    private function layout(): string
    {
        return auth()->user()->isAdmin() ? 'layouts.admin' : 'layouts.app';
    }

    public function transaksi(Request $request)
    {
        $loans = Loan::with('member', 'details.book')
            ->when($request->status, fn ($q, $s) => $q->where('status', $s))
            ->when($request->q, fn ($q, $s) => $q->whereHas('member', fn ($m) => $m
                ->where('username', 'like', "%{$s}%")->orWhere('name', 'like', "%{$s}%")))
            ->when($request->dari, fn ($q, $d) => $q->whereDate('requested_at', '>=', $d))
            ->when($request->sampai, fn ($q, $d) => $q->whereDate('requested_at', '<=', $d))
            ->latest('requested_at')->paginate(12)->withQueryString();

        return view('shared.transaksi', ['loans' => $loans, 'layout' => $this->layout()]);
    }

    // Laporan ditampilkan di layar (tanpa ekspor PDF/Excel)
    public function laporan(Request $request)
    {
        $from = Carbon::parse($request->input('dari', now()->startOfMonth()->toDateString()))->startOfDay();
        $to   = Carbon::parse($request->input('sampai', now()->toDateString()))->endOfDay();

        $summary = Loan::whereBetween('requested_at', [$from, $to])
            ->selectRaw('status, count(*) as total')->groupBy('status')->pluck('total', 'status');

        $rows = LoanDetail::with('loan.member', 'book')
            ->whereHas('loan', fn ($q) => $q->whereBetween('requested_at', [$from, $to]))
            ->latest()->paginate(15)->withQueryString();

        $late = Loan::with('member', 'details.book')->where('status', 'dipinjam')
            ->whereDate('due_date', '<', today())->orderBy('due_date')->get();

        $fineBase = LoanDetail::where('denda', '>', 0)
            ->whereHas('loan', fn ($q) => $q->whereBetween('requested_at', [$from, $to]));
        $fineTotal  = (clone $fineBase)->sum('denda');
        $fineUnpaid = (clone $fineBase)->whereNull('denda_lunas_at')->sum('denda');

        return view('shared.laporan', [
            'layout' => $this->layout(), 'from' => $from, 'to' => $to, 'summary' => $summary,
            'rows' => $rows, 'late' => $late, 'fineTotal' => $fineTotal, 'fineUnpaid' => $fineUnpaid,
        ]);
    }
}
