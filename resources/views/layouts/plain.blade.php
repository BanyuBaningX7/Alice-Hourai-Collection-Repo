<!DOCTYPE html>
<html lang="id">
<head>
    @include('partials.head')
    <title>@yield('title', 'Alice Hourai Collection')</title>
</head>
<body>
    <div id="loader"><div class="brand">ALICE HOURAI</div><div class="ring"></div></div>
    @include('partials.flash')
    @yield('content')
    <script src="{{ asset('js/alice.js') }}"></script>
</body>
</html>
