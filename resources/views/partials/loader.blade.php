{{-- Loading screen pembuka: hanya tampil pada kunjungan pertama dalam satu sesi tab (diatur public/js/alice.js) --}}
<div id="loader" role="status" aria-label="Memuat Alice Hourai Collection">
    <div class="ld-topo"></div>   {{-- pola peta topografi, makin jelas seiring persentase --}}
    <div class="ld-grid"></div>   {{-- kisi halus --}}
    <div class="ld-bar"><span data-fill></span></div>

    <div class="ld-count" data-count>
        <i></i>
        <div class="ld-num"><b data-pct>0</b><em>%</em></div>
        <small data-status>Memuat...</small>
    </div>

    <div class="ld-brand">
        <small>PERPUSTAKAAN DIGITAL</small>
        <h2>ALICE<br>HOURAI<span class="ld-sq"></span></h2>
        <span class="ld-sub">COLLECTION</span>   {{-- tulisan kecil di bawah Alice Hourai: ganti teksnya di sini --}}
    </div>

    <div class="ld-foot">
        <div class="ld-meta"><span class="ld-tri"></span><span>SISTEM INFORMASI KOLEKSI BUKU<br>VERSI 1.0</span></div>
        <div class="ld-line"></div>
        <p>Temukan buku / Masuk ke koleksi</p>
    </div>

    {{-- tirai transisi: biru accent lalu putih, menyapu dari kiri ke kanan --}}
    <div class="ld-wipe ld-wipe-blue"></div>
    <div class="ld-wipe ld-wipe-white"></div>
</div>
