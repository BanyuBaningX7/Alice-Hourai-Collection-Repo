@extends('layouts.admin')
@section('title', 'Log Aktivitas')
@section('heading', 'Log Aktivitas')

@section('content')
<form method="GET" class="row" style="margin-bottom:16px">
    <div class="search"><i class="fa-solid fa-magnifying-glass"></i><input name="q" value="{{ request('q') }}" placeholder="Cari aksi (mis. loan.approved)"></div>
    <input type="date" name="dari" value="{{ request('dari') }}" class="input" style="width:auto">
    <button class="btn btn-primary btn-sm">Filter</button>
    <span class="badge b-nonaktif">Hanya baca</span>
</form>

@if ($logs->isEmpty())
    <div class="empty card"><i class="fa-solid fa-clock-rotate-left"></i><p>Belum ada log.</p></div>
@else
    <div class="tbl-wrap"><table class="tbl">
        <thead><tr><th>Waktu</th><th>Pengguna</th><th>Aksi</th><th>Objek</th><th>Keterangan</th></tr></thead>
        <tbody>@foreach ($logs as $l)
            <tr><td>{{ $l->created_at?->format('d/m/Y H:i') }}</td><td>{{ $l->user->username ?? 'sistem' }}</td>
                <td><code>{{ $l->action }}</code></td><td>{{ $l->entity_type }} #{{ $l->entity_id }}</td>
                <td class="muted small">{{ $l->metadata ? json_encode($l->metadata) : '-' }}</td></tr>
        @endforeach</tbody>
    </table></div>
    @include('partials.pagination', ['p' => $logs])
@endif
@endsection
