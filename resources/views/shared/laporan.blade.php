@extends($layout)
@section('title', 'Laporan')
@section('heading', 'Laporan')

@section('content')
<div class="{{ $layout === 'layouts.app' ? 'container' : '' }}">
    @if ($layout === 'layouts.app') <div class="page-head"><h1>Laporan</h1></div> @endif

    <form method="GET" class="row" style="margin-bottom:16px">
        <label class="muted small">Dari</label><input type="date" name="dari" value="{{ $from->toDateString() }}" class="input" style="width:auto">
        <label class="muted small">Sampai</label><input type="date" name="sampai" value="{{ $to->toDateString() }}" class="input" style="width:auto">
        <button class="btn btn-primary btn-sm">Tampilkan</button>
        <span class="muted small">Laporan ditampilkan di layar.</span>
    </form>

    <div class="grid g4">
        @foreach (['menunggu', 'dipinjam', 'selesai', 'ditolak'] as $s)
            <div class="card stat"><div class="num">{{ $summary[$s] ?? 0 }}</div><div class="lbl">{{ ucfirst($s) }}</div></div>
        @endforeach
    </div>
    <div class="grid g3 mt">
        <div class="card stat"><div class="num">{{ $summary['dibatalkan'] ?? 0 }}</div><div class="lbl">Dibatalkan</div></div>
        <div class="card stat"><div class="num">Rp {{ number_format($fineTotal, 0, ',', '.') }}</div><div class="lbl">Total denda</div></div>
        <div class="card stat"><div class="num warn">Rp {{ number_format($fineUnpaid, 0, ',', '.') }}</div><div class="lbl">Denda belum lunas</div></div>
    </div>

    <section class="section">
        <h2>Transaksi pada periode</h2>
        @if ($rows->isEmpty())
            <div class="empty card"><p>Tidak ada data pada periode ini.</p></div>
        @else
            <div class="tbl-wrap"><table class="tbl">
                <thead><tr><th>Tanggal</th><th>Anggota</th><th>Buku</th><th>Status item</th><th>Kondisi</th><th>Denda</th></tr></thead>
                <tbody>@foreach ($rows as $r)
                    <tr><td>{{ $r->loan->requested_at->format('d/m/Y') }}</td><td>{{ $r->loan->member->name }}</td><td>{{ $r->book->title }}</td>
                        <td><x-badge :status="$r->item_status" /></td><td>{{ $r->kondisi ? ucfirst($r->kondisi) : '-' }}</td>
                        <td>{{ $r->denda ? 'Rp ' . number_format($r->denda, 0, ',', '.') : '-' }}</td></tr>
                @endforeach</tbody>
            </table></div>
            @include('partials.pagination', ['p' => $rows])
        @endif
    </section>

    <section class="section">
        <h2>Daftar keterlambatan (saat ini)</h2>
        @if ($late->isEmpty())
            <div class="empty card"><p>Tidak ada keterlambatan.</p></div>
        @else
            <div class="tbl-wrap"><table class="tbl">
                <thead><tr><th>ID</th><th>Anggota</th><th>Buku</th><th>Jatuh tempo</th><th>Terlambat</th></tr></thead>
                <tbody>@foreach ($late as $l)
                    <tr><td>#{{ $l->id }}</td><td>{{ $l->member->name }}</td>
                        <td>{{ $l->details->map(fn ($d) => $d->book->title)->implode(', ') }}</td>
                        <td>{{ $l->due_date->format('d/m/Y') }}</td>
                        <td><span class="danger">{{ (int) $l->due_date->diffInDays(today(), true) }} hari</span></td></tr>
                @endforeach</tbody>
            </table></div>
        @endif
    </section>
</div>
@endsection
