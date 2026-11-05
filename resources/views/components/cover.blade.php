@props(['book', 'large' => false])
@php
    $coverUrl = null;
    if (! empty($book->cover_path)) {
        $pubFile = public_path('storage/' . $book->cover_path);
        $srcFile = storage_path('app/public/' . $book->cover_path);
        if (! is_file($pubFile) && is_file($srcFile)) {
            @mkdir(dirname($pubFile), 0755, true);
            @copy($srcFile, $pubFile);
        }
        if (is_file($pubFile)) {
            $coverUrl = asset('storage/' . $book->cover_path);
        }
    }
@endphp
@if ($coverUrl)
    <img src="{{ $coverUrl }}" alt="Sampul {{ $book->title }}" class="cover {{ $large ? 'lg' : '' }}">
@else
    <div class="cover cov-{{ $book->id % 5 }} {{ $large ? 'lg' : '' }}">{{ strtoupper(substr($book->title, 0, 1)) }}</div>
@endif