{{-- Navigasi bawah untuk layar kecil --}}
<nav class="bottom-nav">
    @foreach (collect(\App\Support\Menu::items(auth()->user()))->take(5) as [$label, $route, $pattern, $icon])
        <a href="{{ route($route) }}" class="{{ request()->routeIs($pattern) ? 'active' : '' }}"><i class="fa-solid {{ $icon }}"></i>{{ explode(' ', $label)[0] }}</a>
    @endforeach
</nav>
