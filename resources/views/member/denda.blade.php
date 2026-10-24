@extends('layouts.app')
@section('title', 'Tagihan Denda - Alice Hourai Collection')

@section('content')
<div class="container">
    <div class="page-head"><h1>Tagihan Denda</h1></div>

    @if ($suspended)
        <div class="alert alert-warn"><i class="fa-solid fa-triangle-exclamation"></i>
            Akses peminjaman ditangguhkan sampai buku dikembalikan utuh atau denda dibayar.</div>
    @endif

    <div class="card stat">
        <div class="num {{ $unpaid > 0 ? 'warn' : 'ok' }}">Rp {{ number_format($unpaid, 0, ',', '.') }}</div>
        <div class="lbl">Total belum dibayar</div>
    </div>

    @if ($fines->isEmpty())
        <div class="empty card mt"><i class="fa-solid fa-receipt"></i><p>Tidak ada tagihan denda.</p></div>
    @else
        <div class="tbl-wrap mt">
            <table class="tbl">
                <thead><tr><th>Buku</th><th>Kondisi</th><th>Denda (= harga buku)</th><th>Status</th><th>Tanggal</th></tr></thead>
                <tbody>
                @foreach ($fines as $f)
                    <tr>
                        <td>{{ $f->book->title }}</td>
                        <td><x-badge :status="$f->kondisi" /></td>
                        <td>Rp {{ number_format($f->denda, 0, ',', '.') }}</td>
                        <td>@if ($f->denda_lunas_at) <x-badge status="lunas" /> @else <x-badge status="belum" label="Belum dibayar" /> @endif</td>
                        <td>{{ $f->returned_at?->format('d/m/Y') }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    @endif

    <section class="section">
        <h2>Cara Membayar</h2>
        <div class="grid g3">
            <div class="card step"><div class="no">1</div><b>Terima email sanksi</b><p class="muted small">Petugas mengirim email berisi tagihan ke alamat email Anda.</p></div>
            <div class="card step"><div class="no">2</div><b>Balas dengan bukti</b><p class="muted small">Lampirkan bukti pembayaran dan nomor ISBN/kode buku yang dipinjam.</p></div>
            <div class="card step"><div class="no">3</div><b>Petugas mencatat lunas</b><p class="muted small">Setelah diperiksa, denda dicatat lunas dan akses peminjaman dibuka.</p></div>
        </div>
    </section>
</div>
@endsection
