<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use App\Models\LoanDetail;
use App\Services\LoanService;
use DomainException;
use Illuminate\Http\Request;

class MemberController extends Controller
{
    public function dashboard()
    {
        $user = auth()->user();
        $loans = Loan::where('member_id', $user->id);

        $active  = (clone $loans)->where('status', 'dipinjam')->with('details.book')->orderBy('due_date')->get();
        $pending = (clone $loans)->where('status', 'menunggu')->with('details.book')->latest()->get();

        // Filter pinjaman aktif yang mendekati jatuh tempo (H-2 s/d Hari-H), sudah terlambat, atau dikirimi pengingat oleh petugas
        $dueAlerts = $active->filter(fn (Loan $l) => $l->isOverdue() || $l->isDueSoon() || $l->due_notified_at !== null);

        return view('member.dashboard', [
            'user'        => $user,
            'active'      => $active,
            'pending'     => $pending,
            'dueAlerts'   => $dueAlerts,
            'activeBooks' => $user->activeBookCount(),
            'nearestDue'  => $active->min('due_date'),
            'suspended'   => $user->isSuspended(),
        ]);
    }

    public function riwayat(Request $request)
    {
        $loans = Loan::where('member_id', auth()->id())
            ->when($request->status, fn ($q, $s) => $q->where('status', $s))
            ->with('details.book')
            ->latest('requested_at')
            ->paginate(8)
            ->withQueryString();

        return view('member.riwayat', compact('loans'));
    }

    public function cancel(Loan $loan, LoanService $loans)
    {
        try {
            $loans->cancel($loan, auth()->user());
        } catch (DomainException $e) {
            return back()->with('err', $e->getMessage());
        }

        return back()->with('ok', 'Pengajuan dibatalkan.');
    }

    public function denda()
    {
        $fines = LoanDetail::where('denda', '>', 0)
            ->whereHas('loan', fn ($q) => $q->where('member_id', auth()->id()))
            ->with('book', 'loan')->latest()->get();

        $unpaid = $fines->whereNull('denda_lunas_at')->sum('denda');

        return view('member.denda', [
            'fines'     => $fines,
            'unpaid'    => $unpaid,
            'suspended' => auth()->user()->isSuspended(),
        ]);
    }
}
