@php $norail = View::hasSection('norail'); @endphp
<!DOCTYPE html>
<html lang="id">
<head>
    @include('partials.head')
    <title>@yield('title', 'Alice Hourai Collection')</title>
</head>
<body class="{{ $norail ? '' : 'has-rail' }}">
    @include('partials.loader')

    @php
        $user = auth()->user();
        $items = \App\Support\Menu::items($user);
        $showNotifBell = $user && ($user->isAnggota() || $user->isPetugas());
        $dueNotifs = $showNotifBell ? $user->dueNotifications() : collect();
    @endphp
    <header class="topbar">
        <nav class="nav">
            @foreach ($items as [$label, $route, $pattern, $icon])
                <a href="{{ route($route) }}" class="{{ request()->routeIs($pattern) ? 'active' : '' }}">{{ $label }}</a>
            @endforeach
        </nav>
        @guest
            <a href="{{ route('login') }}" class="btn btn-outline btn-sm">Login / Sign</a>
        @else
            <div class="topbar-right">
                @if ($showNotifBell)
                    <div class="notif-wrap" data-notif-wrap>
                        <button type="button" class="notif-btn {{ $dueNotifs->isNotEmpty() ? 'has-alert' : '' }}" data-notif-toggle aria-expanded="false" aria-label="Notifikasi Jatuh Tempo" title="Notifikasi Jatuh Tempo">
                            <i class="fa-solid fa-bell"></i>
                            @if ($dueNotifs->isNotEmpty())
                                <span class="notif-badge">{{ $dueNotifs->count() }}</span>
                            @endif
                        </button>
                        <div class="notif-panel" data-notif-panel hidden>
                            <div class="notif-head">
                                <b><i class="fa-solid fa-bell accent" style="margin-right:6px"></i>Notifikasi Jatuh Tempo</b>
                                <span class="muted small">{{ $dueNotifs->count() }} pemberitahuan</span>
                            </div>
                            <div class="notif-list">
                                @forelse ($dueNotifs as $nl)
                                    <a href="{{ $user->isAnggota() ? route('member.riwayat', ['status' => 'dipinjam']) : route('petugas.aktif') }}" class="notif-item">
                                        <div class="row between" style="font-size:12.5px;margin-bottom:3px">
                                            <b>Pinjaman #{{ $nl->id }}{{ $user->isAnggota() ? '' : ' · ' . $nl->member->name }}</b>
                                            <span class="{{ $nl->isOverdue() ? 'danger' : 'warn' }} small"><b>{{ $nl->dueStatusLabel() }}</b></span>
                                        </div>
                                        <div class="muted small">{{ $nl->details->map(fn ($d) => $d->book->title)->implode(', ') }}</div>
                                        <div class="muted small">Batas kembali: {{ $nl->due_date?->format('d/m/Y') }}</div>
                                        @if ($nl->due_notification_note)
                                            <div class="small accent" style="margin-top:4px"><i class="fa-solid fa-comment-dots"></i> "{{ $nl->due_notification_note }}"</div>
                                        @endif
                                    </a>
                                @empty
                                    <div class="notif-empty">Tidak ada peminjaman yang mendekati atau melewati jatuh tempo.</div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                @endif

                <a href="{{ route('profil') }}" class="user-pill"><span class="avatar-sm"><i class="fa-solid fa-user"></i></span>{{ $user->username }}</a>
            </div>
        @endguest
    </header>

    @unless ($norail) @include('partials.rail') @endunless
    @include('partials.flash')

    <main data-page>@yield('content')</main>

    @include('partials.book-modal')

    <footer class="footer">&copy; {{ date('Y') }} Alice Hourai Collection</footer>
    @include('partials.bottom-nav')
    <script src="{{ asset('js/alice.js') }}"></script>
</body>
</html>
