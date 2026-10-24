@php $norail = View::hasSection('norail'); @endphp
<!DOCTYPE html>
<html lang="id">
<head>
    @include('partials.head')
    <title>@yield('title', 'Alice Hourai Collection')</title>
</head>
<body class="{{ $norail ? '' : 'has-rail' }}">
    <div id="loader"><div class="brand">ALICE HOURAI</div><div class="ring"></div></div>

    @php $items = \App\Support\Menu::items(auth()->user()); @endphp
    <header class="topbar">
        <nav class="nav">
            @foreach ($items as [$label, $route, $pattern, $icon])
                <a href="{{ route($route) }}" class="{{ request()->routeIs($pattern) ? 'active' : '' }}">{{ $label }}</a>
            @endforeach
        </nav>
        @guest
            <a href="{{ route('login') }}" class="btn btn-outline btn-sm">Login / Sign</a>
        @else
            <a href="{{ route('profil') }}" class="user-pill"><span class="avatar-sm"><i class="fa-solid fa-user"></i></span>{{ auth()->user()->username }}</a>
        @endguest
    </header>

    @unless ($norail) @include('partials.rail') @endunless
    @include('partials.flash')

    <main>@yield('content')</main>

    <footer class="footer">&copy; {{ date('Y') }} Alice Hourai Collection</footer>
    @include('partials.bottom-nav')
    <script src="{{ asset('js/alice.js') }}"></script>
</body>
</html>
