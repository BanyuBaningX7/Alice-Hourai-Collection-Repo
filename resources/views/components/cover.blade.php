@props(['book', 'large' => false])
@if ($book->cover_path)
    <img src="{{ asset('storage/'.$book->cover_path) }}" alt="Sampul {{ $book->title }}" class="cover {{ $large ? 'lg' : '' }}">
@else
    <div class="cover cov-{{ $book->id % 5 }} {{ $large ? 'lg' : '' }}">{{ strtoupper(substr($book->title, 0, 1)) }}</div>
@endif
