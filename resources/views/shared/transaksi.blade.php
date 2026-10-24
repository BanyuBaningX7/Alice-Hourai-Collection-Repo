@extends($layout)
@section('title', 'Seluruh Transaksi')
@section('heading', 'Seluruh Transaksi')

@section('content')
<div class="{{ $layout === 'layouts.app' ? 'container' : '' }}">
    @if ($layout === 'layouts.app') <div class="page-head"><h1>Seluruh Transaksi</h1></div> @endif

    @if (auth()->user()->isAdmin())
        <div class="alert alert-info"><i class="fa-solid fa-circle-info"></i> Admin hanya memantau. Persetujuan peminjaman dilakukan oleh petugas.</div>
    @endif

    <form method="GET" class="row" style="margin-bottom:16px">
        <div class="search"><i class="fa-solid fa-magnifying-glass"></i><input name="q" value="{{ request('q') }}" placeholder="Cari anggota..."></div>
        <select name="status" class="select" style="width:auto">
            <option value="">Semua status</option>
            @foreach (['menunggu', 'dipinjam', 'selesai', 'ditolak', 'dibatalkan'] as $s)
                <option value="{{ $s }}" @selected(request('status') === $s)>{{ ucfirst($s) }}</option>
            @endforeach
        </select>
        <input type="date" name="dari" value="{{ request('dari') }}" class="input" style="width:auto" aria-label="Dari tanggal">
        <input type="date" name="sampai" value="{{ request('sampai') }}" class="input" style="width:auto" aria-label="Sampai tanggal">
        <button class="btn btn-primary btn-sm">Filter</button>
    </form>

    @if ($loans->isEmpty())
        <div class="empty card"><i class="fa-solid fa-right-left"></i><p>Tidak ada transaksi.</p></div>
    @else
        <div class="tbl-wrap"><table class="tbl">
            <thead><tr><th>ID</th><th>Anggota</th><th>Buku</th><th>Diajukan</th><th>Jatuh tempo</th><th>Status</th><th>Denda</th></tr></thead>
            <tbody>@foreach ($loans as $loan)
                <tr><td>#{{ $loan->id }}</td><td>{{ $loan->member->name }}</td>
                    <td>{{ $loan->details->map(fn ($d) => $d->book->title)->implode(', ') }}</td>
                    <td>{{ $loan->requested_at->format('d/m/Y') }}</td><td>{{ $loan->due_date?->format('d/m/Y') ?? '-' }}</td>
                    <td><x-badge :status="$loan->displayStatus()" /></td>
                    <td>@php $fine = $loan->details->sum('denda'); @endphp {{ $fine > 0 ? 'Rp ' . number_format($fine, 0, ',', '.') : '-' }}</td></tr>
            @endforeach</tbody>
        </table></div>
        @include('partials.pagination', ['p' => $loans])
    @endif
</div>
@endsection
