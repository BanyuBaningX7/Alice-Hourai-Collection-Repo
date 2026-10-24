{{-- Rail ikon kanan (disembunyikan di login, registrasi, logout, profil, dan Admin Panel) --}}
<aside class="rail" aria-label="Navigasi cepat">
    @foreach (\App\Support\Menu::rail(auth()->user()) as [$route, $pattern, $icon, $title])
        <a href="{{ route($route) }}" title="{{ $title }}" class="{{ request()->routeIs($pattern) ? 'active' : '' }}"><i class="fa-solid {{ $icon }}"></i></a>
    @endforeach
</aside>
