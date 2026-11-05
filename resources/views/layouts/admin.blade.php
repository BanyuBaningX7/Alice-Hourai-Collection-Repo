<!DOCTYPE html>
<html lang="id">
<head>
    @include('partials.head')
    <title>@yield('title', 'Admin Panel') - Alice Hourai Collection</title>
</head>
<body>
    @include('partials.loader')
    @include('partials.flash')
    <div class="admin">
        <aside class="admin-side">
            <div><div class="logo">ALICE HOURAI</div><span class="tag">ADMIN PANEL</span></div>
            <nav>
                @php
                    $menu = [['Dashboard', 'admin.dashboard', 'admin.dashboard', 'fa-gauge'], ['Akun & Role', 'admin.pengguna.index', 'admin.pengguna.*', 'fa-users-gear'],
                             ['Data Buku', 'admin.buku.index', 'admin.buku.*', 'fa-book'], ['Kategori', 'admin.kategori.index', 'admin.kategori.*', 'fa-tags'],
                             ['Seluruh Transaksi', 'admin.transaksi', 'admin.transaksi', 'fa-right-left'], ['Laporan', 'admin.laporan', 'admin.laporan', 'fa-chart-column'],
                             ['Log Aktivitas', 'admin.log', 'admin.log', 'fa-clock-rotate-left']];
                @endphp
                @foreach ($menu as [$label, $route, $pattern, $icon])
                    <a href="{{ route($route) }}" class="{{ request()->routeIs($pattern) ? 'active' : '' }}"><i class="fa-solid {{ $icon }}"></i>{{ $label }}</a>
                @endforeach
            </nav>
            <div class="bottom">
                <a href="{{ route('beranda') }}" class="btn btn-outline btn-sm btn-block" style="margin-bottom:10px">Lihat Situs</a>
                <form method="POST" action="{{ route('logout') }}" data-confirm="Keluar dari akun?" data-yes="Keluar">@csrf
                    <button class="btn btn-danger btn-sm btn-block">Logout</button>
                </form>
            </div>
        </aside>
        <div class="admin-main">
            <div class="admin-head">
                <h1>@yield('heading', 'Admin Panel')</h1>
                <a href="{{ route('profil') }}" class="user-pill"><span class="avatar-sm"><i class="fa-solid fa-user"></i></span>{{ auth()->user()->username }}</a>
            </div>
            <div class="admin-body" data-page>@yield('content')</div>
        </div>
    </div>
    <script src="{{ asset('js/alice.js') }}"></script>
</body>
</html>
