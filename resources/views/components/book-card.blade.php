@props(['book'])
<a href="{{ route('katalog.show', $book) }}" class="card book hover">
    <x-cover :book="$book" />
    <div class="info">
        <div class="title">{{ $book->title }}</div>
        <div class="meta">{{ $book->author }}</div>
        <div class="meta"><i class="fa-regular fa-folder"></i> {{ $book->category->name ?? '-' }}</div>
    </div>
    @if ($book->stock_available > 0)
        <span class="badge b-tersedia">Tersedia {{ $book->stock_available }}</span>
    @else
        <span class="badge b-habis">Habis</span>
    @endif
    <i class="fa-solid fa-chevron-right muted small"></i>
</a>
