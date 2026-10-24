<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use App\Models\LoanDetail;
use App\Models\User;
use App\Services\LoanService;
use DomainException;
use Illuminate\Http\Request;

class PetugasController extends Controller
{
    public function __construct(private LoanService $loans) {}

    public function dashboard()
    {
        $stats = [
            'menunggu' => Loan::where('status', 'menunggu')->count(),
            'aktif'    => Loan::where('status', 'dipinjam')->count(),
            'terlambat' => Loan::where('status', 'dipinjam')->whereDate('due_date', '<', today())->count(),
            'denda'    => LoanDetail::where('denda', '>', 0)->whereNull('denda_lunas_at')->count(),
        ];
        $queue = Loan::where('status', 'menunggu')->with('member', 'details.book')->oldest('requested_at')->take(5)->get();

        return view('petugas.dashboard', compact('stats', 'queue'));
    }

    // ---------- Pengajuan ----------
    public function pengajuan(Request $request)
    {
        $loans = Loan::where('status', 'menunggu')->with('member', 'details.book')
            ->when($request->q, fn ($q, $s) => $q->whereHas('member', fn ($m) => $m
                ->where('username', 'like', "%{$s}%")->orWhere('name', 'like', "%{$s}%")))
            ->oldest('requested_at')->paginate(10)->withQueryString();

        return view('petugas.pengajuan', compact('loans'));
    }

    public function pengajuanShow(Loan $loan)
    {
        $loan->load('member', 'details.book.category');

        return view('petugas.pengajuan_show', ['loan' => $loan, 'member' => $loan->member]);
    }

    public function approve(Loan $loan)
    {
        try {
            $this->loans->approve($loan, auth()->user());
        } catch (DomainException $e) {
            return back()->with('err', $e->getMessage());
        }

        return redirect()->route('petugas.pengajuan')->with('ok', 'Peminjaman disetujui.');
    }

    public function reject(Request $request, Loan $loan)
    {
        $data = $request->validate(['reason' => 'required|string|max:255']);
        try {
            $this->loans->reject($loan, auth()->user(), $data['reason']);
        } catch (DomainException $e) {
            return back()->with('err', $e->getMessage());
        }

        return redirect()->route('petugas.pengajuan')->with('ok', 'Pengajuan ditolak.');
    }

    // ---------- Pinjaman aktif ----------
    public function aktif(Request $request)
    {
        $loans = Loan::where('status', 'dipinjam')->with('member', 'details.book')
            ->when($request->q, fn ($q, $s) => $q->whereHas('member', fn ($m) => $m
                ->where('username', 'like', "%{$s}%")->orWhere('name', 'like', "%{$s}%")))
            ->orderBy('due_date')->paginate(10)->withQueryString();

        return view('petugas.aktif', compact('loans'));
    }

    // ---------- Pengembalian ----------
    public function pengembalian(Request $request)
    {
        $loans = Loan::where('status', 'dipinjam')->with('member', 'details.book')
            ->when($request->q, fn ($q, $s) => $q->where(fn ($w) => $w
                ->where('id', $s)
                ->orWhereHas('member', fn ($m) => $m->where('username', 'like', "%{$s}%")->orWhere('name', 'like', "%{$s}%"))))
            ->orderBy('due_date')->paginate(10)->withQueryString();

        return view('petugas.pengembalian', compact('loans'));
    }

    public function pengembalianShow(Loan $loan)
    {
        $loan->load('member', 'details.book');

        return view('petugas.pengembalian_show', compact('loan'));
    }

    public function kembali(Request $request, LoanDetail $detail)
    {
        $data = $request->validate(['kondisi' => 'required|in:utuh,rusak,hilang']);
        try {
            $this->loans->returnItem($detail, auth()->user(), $data['kondisi']);
        } catch (DomainException $e) {
            return back()->with('err', $e->getMessage());
        }

        $msg = $data['kondisi'] === 'utuh'
            ? 'Pengembalian dicatat.'
            : 'Pengembalian dicatat. Denda dibuat sebesar harga buku; kirim email sanksi dari menu Denda.';

        return back()->with('ok', $msg);
    }

    // ---------- Denda ----------
    public function denda(Request $request)
    {
        $fines = LoanDetail::where('denda', '>', 0)->with('book', 'loan.member')
            ->when($request->status === 'belum', fn ($q) => $q->whereNull('denda_lunas_at'))
            ->when($request->status === 'lunas', fn ($q) => $q->whereNotNull('denda_lunas_at'))
            ->latest()->paginate(10)->withQueryString();

        return view('petugas.denda', compact('fines'));
    }

    public function lunas(Request $request, LoanDetail $detail)
    {
        $request->validate(['bukti' => 'accepted', 'isbn' => 'accepted'], [
            'accepted' => 'Centang konfirmasi bukti pembayaran dan nomor buku terlebih dahulu.',
        ]);
        try {
            $this->loans->markFinePaid($detail, auth()->user());
        } catch (DomainException $e) {
            return back()->with('err', $e->getMessage());
        }

        return back()->with('ok', 'Denda dicatat lunas.');
    }

    // ---------- Seluruh anggota (hanya lihat) ----------
    public function anggota(Request $request)
    {
        $members = User::where('role', 'anggota')
            ->when($request->q, fn ($q, $s) => $q->where(fn ($w) => $w
                ->where('username', 'like', "%{$s}%")->orWhere('name', 'like', "%{$s}%")->orWhere('email', 'like', "%{$s}%")))
            ->when($request->status, fn ($q, $s) => $q->where('status', $s))
            ->withCount('loans')->orderBy('name')->paginate(12)->withQueryString();

        return view('petugas.anggota', compact('members'));
    }
}
