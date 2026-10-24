@extends('layouts.app')
@section('title', 'Denda & Pelunasan')

@section('content')
<div class="container">
    <div class="page-head"><h1>Denda &amp; Pelunasan</h1></div>

    <div class="chips">
        <a href="{{ route('petugas.denda') }}" class="chip {{ request('status') ? '' : 'active' }}">Semua</a>
        <a href="{{ route('petugas.denda', ['status' => 'belum']) }}" class="chip {{ request('status') === 'belum' ? 'active' : '' }}">Belum dibayar</a>
        <a href="{{ route('petugas.denda', ['status' => 'lunas']) }}" class="chip {{ request('status') === 'lunas' ? 'active' : '' }}">Lunas</a>
    </div>

    <div class="alert alert-info mt"><i class="fa-solid fa-circle-info"></i>
        <span>Alur: kirim email sanksi &rarr; anggota membalas dengan bukti bayar dan ISBN/kode buku &rarr; periksa &rarr; catat lunas. Pembayaran tidak diproses di dalam aplikasi.</span></div>

    @if ($fines->isEmpty())
        <div class="empty card"><i class="fa-solid fa-receipt"></i><p>Tidak ada data denda.</p></div>
    @else
        <div class="tbl-wrap"><table class="tbl">
            <thead><tr><th>Anggota</th><th>Buku</th><th>Kondisi</th><th>Denda</th><th>Status</th><th>Aksi</th></tr></thead>
            <tbody>@foreach ($fines as $f)
                @php
                    $m = $f->loan->member;
                    $subject = rawurlencode('Sanksi Denda Buku - Alice Hourai Collection');
                    $body = rawurlencode("Yth. {$m->name},\n\nBuku \"{$f->book->title}\" dinyatakan {$f->kondisi}. Denda sebesar Rp " . number_format($f->denda, 0, ',', '.') . ".\n\nMohon balas email ini dengan melampirkan bukti pembayaran dan nomor ISBN/kode buku ({$f->book->isbn}).\n\nTerima kasih.\nPetugas Alice Hourai Collection");
                @endphp
                <tr>
                    <td>{{ $m->name }}<div class="muted small">{{ $m->email }}</div></td>
                    <td>{{ $f->book->title }}<div class="muted small">ISBN {{ $f->book->isbn ?? '-' }}</div></td>
                    <td><x-badge :status="$f->kondisi" /></td>
                    <td>Rp {{ number_format($f->denda, 0, ',', '.') }}</td>
                    <td>@if ($f->denda_lunas_at) <x-badge status="lunas" /> @else <x-badge status="belum" label="Belum dibayar" /> @endif</td>
                    <td>
                        @unless ($f->denda_lunas_at)
                            <div class="actions">
                                <a class="btn btn-outline btn-sm" href="mailto:{{ $m->email }}?subject={{ $subject }}&amp;body={{ $body }}"><i class="fa-regular fa-envelope"></i> Email Sanksi</a>
                                <button type="button" class="btn btn-primary btn-sm" onclick="document.getElementById('lunas-{{ $f->id }}').showModal()">Catat Lunas</button>
                            </div>
                            <dialog id="lunas-{{ $f->id }}">
                                <h3>Catat denda lunas</h3>
                                <form method="POST" action="{{ route('petugas.lunas', $f) }}">@csrf
                                    <label class="check"><input type="checkbox" name="bukti" value="1"> Bukti pembayaran sudah diterima lewat email</label>
                                    <label class="check"><input type="checkbox" name="isbn" value="1"> Nomor ISBN/kode buku sesuai ({{ $f->book->isbn ?? '-' }})</label>
                                    <div class="row">
                                        <button type="button" class="btn btn-outline btn-sm" onclick="this.closest('dialog').close()">Batal</button>
                                        <button class="btn btn-primary btn-sm">Catat Lunas</button>
                                    </div>
                                </form>
                            </dialog>
                        @else <span class="muted small">{{ $f->denda_lunas_at->format('d/m/Y') }}</span> @endunless
                    </td>
                </tr>
            @endforeach</tbody>
        </table></div>
        @include('partials.pagination', ['p' => $fines])
    @endif
</div>
@endsection
