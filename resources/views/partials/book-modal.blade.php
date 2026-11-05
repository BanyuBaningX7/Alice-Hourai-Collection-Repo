{{-- Pop-up detail buku: diisi lewat JavaScript dari atribut data-book pada kartu (tanpa query tambahan) --}}
@php
    $mUser = auth()->user();
    $mRole = $mUser ? $mUser->role : 'guest';
    $mSuspended = $mUser && $mUser->isAnggota() ? $mUser->isSuspended() : false;   // dihitung sekali per halaman
@endphp
<dialog id="book-dialog" class="book-dialog" aria-labelledby="bd-title"
        data-role="{{ $mRole }}" data-suspended="{{ $mSuspended ? 1 : 0 }}" data-cart="{{ json_encode(session('cart', [])) }}">
    <button type="button" class="bd-close" data-bd-close aria-label="Tutup"><i class="fa-solid fa-xmark"></i></button>
    <div class="bd-grid">
        <div class="bd-cover" data-f="cover"></div>
        <div class="bd-body">
            <h2 id="bd-title" data-f="title"></h2>
            <p class="muted" data-f="author"></p>
            <div class="row" style="margin-bottom:14px">
                <span class="badge b-dipinjam" data-f="category"></span>
                <span class="badge" data-f="stock"></span>
            </div>

            <dl class="info-grid">
                <div><dt>Penerbit</dt><dd data-f="publisher"></dd></div>
                <div><dt>Tahun</dt><dd data-f="year"></dd></div>
                <div><dt>ISBN</dt><dd data-f="isbn"></dd></div>
                <div><dt>Lokasi rak</dt><dd data-f="location"></dd></div>
                <div><dt>Harga buku</dt><dd data-f="price"></dd></div>
            </dl>

            <p class="bd-desc" data-f="desc"></p>

            <div class="alert alert-info"><i class="fa-solid fa-circle-info"></i>
                <span>Masa pinjam maksimal {{ config('library.loan_days') }} hari. Maksimal {{ config('library.max_books') }} buku aktif per anggota.</span></div>

            <div class="bd-actions">
                <a id="bd-login" class="btn btn-primary" href="{{ route('login') }}" hidden>Login untuk meminjam</a>
                <form id="bd-form" method="POST" hidden>@csrf
                    <button class="btn btn-primary"><i class="fa-solid fa-book-medical"></i> Ajukan Pinjam</button></form>
                <a id="bd-incart" class="btn btn-outline" href="{{ route('ajukan') }}" hidden>Sudah di pengajuan - Lihat</a>
                <button id="bd-disabled" type="button" class="btn btn-primary" disabled hidden>Tidak dapat dipinjam</button>
                <a class="btn btn-outline" data-f="url" href="#">Buka halaman lengkap</a>
            </div>
            <div id="bd-suspended" class="alert alert-warn" hidden><i class="fa-solid fa-triangle-exclamation"></i> Akses peminjaman ditangguhkan. Kembalikan buku yang terlambat atau lunasi denda.</div>
            <p id="bd-staff" class="muted small" hidden>Hanya akun anggota yang dapat meminjam buku.</p>
        </div>
    </div>
</dialog>
