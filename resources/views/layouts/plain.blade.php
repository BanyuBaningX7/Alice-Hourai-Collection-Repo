<!DOCTYPE html>
<html lang="id">
<head>
    @include('partials.head')
    <title>@yield('title', 'Alice Hourai Collection')</title>
</head>
<body>
    @include('partials.loader')
    @include('partials.flash')
    <div data-page>@yield('content')</div>
    <script src="{{ asset('js/alice.js') }}"></script>
</body>
</html>
