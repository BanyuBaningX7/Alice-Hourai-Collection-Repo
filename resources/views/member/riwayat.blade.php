@extends('layouts.app')
@section('title', 'Riwayat Pinjam - Alice Hourai Collection')

@section('content')
<div class="container">
    <div class="page-head"><h1>Riwayat Pinjam</h1></div>

    <div class="chips">
        <a href="{{ route('member.riwayat') }}" class="chip {{ request('status') ? '' : 'active' }}">Semua</a>
        @foreach (['menunggu', 'dipinjam', 'selesai', 'ditolak', 'dibatalkan'] as $s)
            <a href="{{ route('member.riwayat', ['status' => $s]) }}" class="chip {{ request('status') === $s ? 'active' : '' }}">{{ ucfirst($s) }}</a>
        @endforeach
    </div>

    @if ($loans->isEmpty())
        <div class="empty card mt"><i class="fa-solid fa-clock-rotate-left"></i><p>Belum ada transaksi.</p></div>
    @else
        <div class="tbl-wrap mt">
            <table class="tbl">
                <thead><tr><th>ID</th><th>Buku</th><th>Diajukan</th><th>Dipinjam</th><th>Jatuh tempo</th><th>Status</th><th>Aksi</th></tr></thead>
                <tbody>
                @foreach ($loans as $loan)
                    <tr>
                        <td>#{{ $loan->id }}</td>
                        <td>{{ $loan->details->map(fn ($d) => $d->book->title)->implode(', ') }}</td>
                        <td>{{ $loan->requested_at->format('d/m/Y') }}</td>
                        <td>{{ $loan->loan_date?->format('d/m/Y') ?? '-' }}</td>
                        <td>{{ $loan->due_date?->format('d/m/Y') ?? '-' }}</td>
                        <td>
                            <x-badge :status="$loan->displayStatus()" />
                            @if ($loan->status === 'ditolak' && $loan->notes) <div class="muted small">Alasan: {{ $loan->notes }}</div> @endif
                        </td>
                        <td>
                            @if ($loan->status === 'menunggu')
                                <form method="POST" action="{{ route('member.batal', $loan) }}" data-confirm="Batalkan pengajuan ini?" data-yes="Ya, batalkan">@csrf
                                    <button class="link-btn danger">Batalkan</button>
                                </form>
                            @else <span class="muted">-</span> @endif
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        @include('partials.pagination', ['p' => $loans])
    @endif
</div>
@endsection
