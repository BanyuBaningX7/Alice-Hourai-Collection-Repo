{{-- Pesan sukses / gagal / error validasi --}}
<div class="toast-wrap">
    @if (session('ok'))  <div class="toast ok"><i class="fa-solid fa-circle-check ok"></i> {{ session('ok') }}</div> @endif
    @if (session('err')) <div class="toast err"><i class="fa-solid fa-circle-exclamation danger"></i> {{ session('err') }}</div> @endif
    @if (isset($errors) && $errors->any() && ! View::hasSection('auth'))
        <div class="toast err"><i class="fa-solid fa-circle-exclamation danger"></i> {{ $errors->first() }}</div>
    @endif
</div>

{{-- Dialog konfirmasi yang dipakai semua form ber-atribut data-confirm --}}
<dialog id="confirm-dialog">
    <h3>Konfirmasi</h3>
    <p class="muted" data-msg></p>
    <div class="row">
        <button type="button" class="btn btn-outline btn-sm" data-no>Batal</button>
        <button type="button" class="btn btn-primary btn-sm" data-yes>Ya, lanjutkan</button>
    </div>
</dialog>
