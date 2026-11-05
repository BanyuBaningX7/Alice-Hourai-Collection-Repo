@props(['book'])
@php
    // Pastikan file sampul tersedia di public/storage (otomatis salin dari storage/app/public bila symlink belum dibuat)
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

    // Semua data untuk pop-up sudah ada pada baris buku ini, jadi membuka pop-up TIDAK menambah query database.
    $popup = [
        'id'        => $book->id,
        'title'     => $book->title,
        'author'    => $book->author,
        'category'  => $book->category->name ?? '-',
        'publisher' => $book->publisher ?? '-',
        'year'      => $book->year ?? '-',
        'isbn'      => $book->isbn ?? '-',
        'location'  => $book->location ?? '-',
        'price'     => 'Rp ' . number_format((float) ($book->price ?? 0), 0, ',', '.'),
        'desc'      => $book->description,
        'stock'     => (int) ($book->stock_available ?? 0),
        'total'     => (int) ($book->stock_total ?? 0),
        'cover'     => $coverUrl,
        'cov'       => $book->id % 5,
        'cart'      => route('cart.add', $book),
        'url'       => route('katalog.show', $book),
    ];
@endphp
<a href="{{ route('katalog.show', $book) }}" class="card book hover" data-book="{{ json_encode($popup, JSON_UNESCAPED_UNICODE) }}">
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
