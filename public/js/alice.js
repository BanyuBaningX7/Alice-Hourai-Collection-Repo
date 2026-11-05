/* =====================================================================
   alice.js
   1) Injeksi style pengaman (agar tahan terhadap cache CSS browser)
   2) Loading screen pembuka (gaya Endfield):
      - Muncul saat pertama kali buka web DAN setiap kali di-reload (Ctrl+R / F5)
      - Intensitas akhir topografi dibatasi 65% (0.65)
      - Bar loading biru tombol Cari (#4080EC) tanpa glow, angka persen biru + glow
   3) Loading di dalam area konten saat pindah menu / kirim form
   4) Toast, dialog konfirmasi, kata sandi
   5) Pop-up detail buku (terkunci di tengah layar seperti Gambar ke-4, tidak bisa bergeser keluar batas)
   6) Tombol auto-scroll ke atas di pojok kanan bawah (kotak biru web, panah hitam)
   ===================================================================== */

// ---------- 1) Pengaman CSS Runtime (memastikan style baru selalu aktif walau CSS lama ter-cache) ----------
(function () {
  if (document.getElementById('alice-runtime-fix')) return;
  const st = document.createElement('style');
  st.id = 'alice-runtime-fix';
  st.textContent = `
    html.modal-lock, html.modal-lock body { overflow: hidden !important; overscroll-behavior: none !important; }
    dialog#book-dialog, .book-dialog {
      position: fixed !important; inset: 0 !important; margin: auto !important;
      max-width: 860px !important; width: calc(100% - 32px) !important;
      height: fit-content !important; max-height: calc(100vh - 48px) !important;
      padding: 30px !important; overflow-y: auto !important; overflow-x: hidden !important;
      overscroll-behavior: contain !important; box-sizing: border-box !important;
    }
    .book-dialog .row { justify-content: flex-start !important; margin-top: 0 !important; }
    #loader {
      position: fixed !important; inset: 0 !important; width: 100vw !important; height: 100vh !important;
      max-width: none !important; max-height: none !important; border-radius: 0 !important;
      box-shadow: none !important; transition: none !important;
    }
    #loader .ld-topo { opacity: min(0.65, var(--ld-topo, 0)) !important; }
    #loader .ld-bar span { background: #4080EC !important; box-shadow: none !important; filter: none !important; }
    #loader .ld-count i { background: #4080EC !important; box-shadow: 0 0 10px rgba(68,138,255,.65) !important; }
    #loader .ld-num, #loader .ld-num b, #loader .ld-num em {
      color: #4080EC !important;
      text-shadow: 0 0 16px rgba(68,138,255,.8), 0 0 32px rgba(68,138,255,.45) !important;
    }
    #loader.ld-cover, #loader.cover { background: #fff !important; width: 100vw !important; height: 100vh !important; border-radius: 0 !important; }
    #loader.ld-cover > :not(.ld-wipe), #loader.cover > :not(.ld-wipe) { visibility: hidden !important; }
    #loader.ld-cover .ld-wipe, #loader.cover .ld-wipe { transform: none !important; animation: none !important; }
    #loader.ld-fade, #loader.fade { opacity: 0 !important; pointer-events: none !important; transition: opacity 1.1s cubic-bezier(.4,0,.2,1) !important; }
    button.to-top, #to-top {
      position: fixed !important; left: auto !important; right: 20px !important; bottom: 22px !important;
      z-index: 900 !important; width: 44px !important; height: 44px !important;
      padding: 0 !important; margin: 0 !important; border: 0 !important; border-radius: 2px !important;
      background: #4080EC !important; color: #000 !important;
      display: grid !important; place-items: center !important; cursor: pointer !important;
      box-shadow: 0 0 18px rgba(68,138,255,.45), 0 4px 12px rgba(0,0,0,.5) !important;
      opacity: 0 !important; visibility: hidden !important; transform: translateY(10px) !important;
      pointer-events: none !important;
      transition: opacity .22s ease, transform .22s ease, visibility .22s, background .2s, box-shadow .2s !important;
    }
    body.has-rail button.to-top, body.has-rail #to-top { right: calc(var(--rail, 56px) + 18px) !important; }
    button.to-top.show, #to-top.show {
      opacity: 1 !important; visibility: visible !important; transform: translateY(0) !important; pointer-events: auto !important;
    }
    html.modal-lock #to-top { opacity: 0 !important; visibility: hidden !important; pointer-events: none !important; }
    button.to-top:hover, #to-top:hover {
      background: #448AFF !important; box-shadow: 0 0 26px rgba(68,138,255,.7), 0 6px 16px rgba(0,0,0,.55) !important;
      transform: translateY(-2px) !important;
    }
    @media (max-width: 820px) {
      body.has-rail button.to-top, body.has-rail #to-top, button.to-top, #to-top {
        right: 16px !important; bottom: 78px !important; width: 42px !important; height: 42px !important;
      }
    }
  `;
  document.head.appendChild(st);
})();

// ---------- 2) Loading screen pembuka (tampil saat pertama buka & setiap kali Reload / Ctrl+R) ----------
(function () {
  const el = document.getElementById('loader');
  if (!el) return;

  // Deteksi apakah halaman dimuat ulang (Ctrl+R / F5 / tombol Reload browser)
  const navEntry = performance.getEntriesByType && performance.getEntriesByType('navigation')[0];
  const isReload = (navEntry && navEntry.type === 'reload') || (performance.navigation && performance.navigation.type === 1);

  // Bersihkan flag aliceSeen segera setelah halaman terbuka agar Ctrl+R berikutnya selalu menampilkan loading screen
  const skipIntro = !isReload && document.documentElement.classList.contains('no-intro');
  try { sessionStorage.removeItem('aliceSeen'); } catch (e) {}

  if (isReload) {
    document.documentElement.classList.remove('no-intro');
  } else if (skipIntro) {
    el.remove();
    return;
  }

  const DURATION = 4000;      // lama hitungan 0 -> 100%
  const BLINK = 720;          // lama kedap-kedip saat 100% (3 kali)
  const HOLD = 300;           // jeda sebelum tirai
  const COVER = 1100;         // waktu tirai biru + putih menutup layar
  const WHITE_HOLD = 350;     // lama layar putih polos sebelum memudar
  const FADE = 1100;          // lama putih memudar sampai Beranda terlihat penuh
  const MAX_TOPO = 0.65;      // intensitas maksimal peta topografi (65%)

  const num = el.querySelector('[data-pct]');
  const fill = el.querySelector('[data-fill]');
  const status = el.querySelector('[data-status]');
  const count = el.querySelector('[data-count]');
  if (!num || !fill || !status || !count) { el.remove(); return; }

  const start = performance.now();
  let pct = 0;
  let loaded = document.readyState === 'complete';
  window.addEventListener('load', () => { loaded = true; });

  const remove = () => { if (el.isConnected) el.remove(); };

  function finish() {
    status.textContent = 'Siap';
    el.classList.add('ld-blink', 'blink');                       // 1) kedap-kedip
    setTimeout(() => {
      el.classList.add('ld-go', 'go');                           // 2) tirai biru lalu putih menutup
      setTimeout(() => {
        el.classList.add('ld-cover');                            // layar putih penuh
        setTimeout(() => {
          el.classList.add('ld-fade', 'fade');                   // 3) putih memudar, Beranda terlihat
          setTimeout(remove, FADE + 100);
        }, WHITE_HOLD);
      }, COVER);
    }, BLINK + HOLD);
    // pengaman: apa pun yang terjadi, loading screen pasti dibersihkan
    setTimeout(remove, BLINK + HOLD + COVER + WHITE_HOLD + FADE + 2500);
  }

  function tick(now) {
    const t = now - start;
    const target = (loaded && t >= DURATION) ? 100 : Math.min(94, (t / DURATION) * 100);
    pct += (target - pct) * 0.12;
    if (target === 100 && 100 - pct < 0.5) pct = 100;

    const p = Math.round(pct);
    const h = window.innerHeight;
    num.textContent = p;
    fill.style.height = (p >= 100 ? 100 : pct) + '%';
    // pola topografi: transparan di 0%, lalu timbul perlahan hingga maksimal 65% (0.65)
    const eased = Math.pow(pct / 100, 1.7);
    el.style.setProperty('--ld-topo', (eased * MAX_TOPO).toFixed(3));
    el.style.setProperty('--ld-grid', (eased * 0.22).toFixed(3));
    count.style.top = Math.min(h - 125, Math.max(30, (pct / 100) * h - 75)) + 'px';   // angka mengikuti ujung bar
    status.textContent = p < 100 ? 'Memuat...' : 'Siap';

    if (p >= 100) { finish(); return; }
    requestAnimationFrame(tick);
  }
  requestAnimationFrame(tick);
})();

// ---------- 3) Loading di dalam area konten (saat klik menu / kirim form) ----------
const page = document.querySelector('[data-page]');
const startLoading = () => {
  try { sessionStorage.setItem('aliceSeen', '1'); } catch (e) {}
  if (page) page.classList.add('is-loading');
};

document.addEventListener('click', (e) => {
  const a = e.target.closest('a[href]');
  if (!a || e.defaultPrevented || e.button !== 0 || e.ctrlKey || e.metaKey || e.shiftKey) return;
  if (a.hasAttribute('data-book') && document.getElementById('book-dialog')) return; // dibuka sebagai pop-up
  if (a.target === '_blank' || a.hasAttribute('download')) return;
  const href = a.getAttribute('href');
  if (!href || href.startsWith('#') || href.startsWith('javascript:')) return;
  const u = new URL(a.href, location.href);
  if (u.origin !== location.origin) return;                                   // mailto:, situs lain
  if (u.pathname === location.pathname && u.search === location.search) return;
  startLoading();
});

document.addEventListener('submit', (e) => {
  const f = e.target;
  if (e.defaultPrevented) return;                                              // menunggu dialog konfirmasi
  if (f.hasAttribute && f.hasAttribute('data-confirm') && !f.dataset.ok) return;
  startLoading();
});

// Tombol Back browser: pastikan tidak tertinggal dalam keadaan loading
window.addEventListener('pageshow', () => page && page.classList.remove('is-loading'));

// ---------- 4) Toast hilang otomatis ----------
document.querySelectorAll('.toast').forEach(t => setTimeout(() => {
  t.style.transition = 'opacity .4s'; t.style.opacity = 0; setTimeout(() => t.remove(), 400);
}, 4500));

// ---------- Dialog konfirmasi: <form data-confirm="Pesan"> ----------
const dlg = document.getElementById('confirm-dialog');
if (dlg) {
  let pending = null;
  document.querySelectorAll('form[data-confirm]').forEach(f => f.addEventListener('submit', e => {
    if (f.dataset.ok) return;
    e.preventDefault(); pending = f;
    dlg.querySelector('[data-msg]').textContent = f.dataset.confirm;
    dlg.querySelector('[data-yes]').textContent = f.dataset.yes || 'Ya, lanjutkan';
    dlg.showModal();
  }));
  dlg.querySelector('[data-yes]').addEventListener('click', () => { dlg.close(); if (pending) { pending.dataset.ok = 1; pending.requestSubmit(); } });
  dlg.querySelector('[data-no]').addEventListener('click', () => { dlg.close(); pending = null; });
}

// ---------- Tampilkan / sembunyikan kata sandi ----------
document.querySelectorAll('[data-toggle-pw]').forEach(b => b.addEventListener('click', () => {
  const i = document.getElementById(b.dataset.togglePw);
  if (i) i.type = i.type === 'password' ? 'text' : 'password';
}));

// ---------- Penghitung karakter kata sandi (mengikuti role) ----------
const pw = document.getElementById('password'), role = document.getElementById('role'), hint = document.getElementById('pw-hint');
if (pw && hint) {
  const update = () => {
    const isAdmin = role ? role.value === 'admin' : hint.dataset.role === 'admin';
    hint.textContent = (isAdmin ? 'Minimal 30 karakter' : 'Tepat 10 karakter') + ' (boleh huruf, angka, simbol). Terisi: ' + pw.value.length + '/' + (isAdmin ? '30+' : '10');
  };
  pw.addEventListener('input', update); role?.addEventListener('change', update); update();
}

// ---------- 5) Pop-up detail buku (Terkunci di tengah layar seperti Gambar ke-4, halaman belakang tidak bisa di-scroll) ----------
const bd = document.getElementById('book-dialog');
if (bd) {
  const f = (n) => bd.querySelector('[data-f="' + n + '"]');
  const byId = (i) => bd.querySelector('#' + i);
  let savedScrollY = 0;

  function renderFallbackCover(container, b) {
    container.replaceChildren();
    const d = document.createElement('div');
    d.className = 'cover lg cov-' + (b.cov ?? 0);
    d.textContent = (b.title || '?').charAt(0).toUpperCase();
    container.append(d);
  }

  function openBook(b) {
    savedScrollY = window.scrollY;

    f('title').textContent = b.title || '-';
    f('author').textContent = 'oleh ' + (b.author || '-');
    f('category').textContent = b.category || '-';
    const st = f('stock');
    st.textContent = b.stock > 0 ? 'Tersedia ' + b.stock + ' dari ' + b.total : 'Habis';
    st.className = 'badge ' + (b.stock > 0 ? 'b-tersedia' : 'b-habis');
    ['publisher', 'year', 'isbn', 'location', 'price'].forEach(k => { f(k).textContent = b[k] || '-'; });
    f('desc').textContent = b.desc || 'Belum ada deskripsi untuk buku ini.';
    f('url').href = b.url;

    // sampul (otomatis fallback ke sampul huruf jika file gambar gagal dimuat)
    const c = f('cover');
    c.replaceChildren();
    if (b.cover) {
      const img = document.createElement('img');
      img.src = b.cover;
      img.alt = 'Sampul ' + b.title;
      img.className = 'cover lg';
      img.addEventListener('error', () => renderFallbackCover(c, b), { once: true });
      c.append(img);
    } else {
      renderFallbackCover(c, b);
    }

    // tombol aksi menyesuaikan pengguna
    const role = bd.dataset.role, suspended = bd.dataset.suspended === '1';
    const cart = JSON.parse(bd.dataset.cart || '[]');
    const show = { login: false, form: false, incart: false, disabled: false, suspended: false, staff: false };
    if (role === 'guest') show.login = true;
    else if (role === 'anggota') {
      if (suspended) { show.suspended = true; show.disabled = true; }
      else if (b.stock < 1) show.disabled = true;
      else if (cart.includes(b.id)) show.incart = true;
      else show.form = true;
    } else show.staff = true;
    Object.keys(show).forEach(k => {
      const elBtn = byId('bd-' + k);
      if (elBtn) elBtn.hidden = !show[k];
    });
    const elDis = byId('bd-disabled');
    if (elDis) elDis.textContent = b.stock < 1 ? 'Stok habis' : 'Tidak dapat dipinjam';
    const elForm = byId('bd-form');
    if (elForm) elForm.action = b.cart;

    // Kunci scroll halaman belakang dan pastikan pop-up berada tepat di tengah viewport
    document.documentElement.classList.add('modal-lock');
    bd.setAttribute('inert', '');
    bd.showModal();
    bd.removeAttribute('inert');
    bd.scrollTop = 0;
    bd.querySelector('[data-bd-close]')?.focus({ preventScroll: true });
    if (window.scrollY !== savedScrollY) {
      window.scrollTo({ top: savedScrollY, behavior: 'instant' });
    }
  }

  function closeBook() {
    bd.close();
  }

  document.addEventListener('click', (e) => {
    const a = e.target.closest('a[data-book]');
    if (!a || e.button !== 0 || e.ctrlKey || e.metaKey || e.shiftKey) return;
    e.preventDefault();
    try {
      openBook(JSON.parse(a.dataset.book));
    } catch (err) {
      window.location.href = a.href;
    }
  });
  bd.querySelector('[data-bd-close]')?.addEventListener('click', closeBook);
  bd.querySelector('[data-f="url"]')?.addEventListener('click', () => {
    document.documentElement.classList.remove('modal-lock');
    bd.close();
  });
  bd.querySelector('#bd-form')?.addEventListener('submit', () => {
    document.documentElement.classList.remove('modal-lock');
    bd.close();
  });
  bd.addEventListener('close', () => {
    document.documentElement.classList.remove('modal-lock');
    if (window.scrollY !== savedScrollY) {
      window.scrollTo({ top: savedScrollY, behavior: 'instant' });
    }
  });
  bd.addEventListener('click', (e) => {                       // klik di luar kotak = tutup
    const r = bd.getBoundingClientRect();
    if (e.clientX < r.left || e.clientX > r.right || e.clientY < r.top || e.clientY > r.bottom) closeBook();
  });
}

// ---------- 6) Tombol Auto-Scroll ke Atas di Pojok Kanan Bawah ----------
(function () {
  let btn = document.getElementById('to-top');
  if (!btn) {
    btn = document.createElement('button');
    btn.type = 'button';
    btn.id = 'to-top';
    btn.className = 'to-top';
    btn.setAttribute('aria-label', 'Kembali ke atas');
    btn.title = 'Kembali ke atas';
    btn.innerHTML = '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#000000" stroke-width="2.8" stroke-linecap="square" stroke-linejoin="miter" aria-hidden="true"><polyline points="6 14 12 8 18 14"></polyline><polyline points="6 20 12 14 18 20"></polyline></svg>';
    document.body.appendChild(btn);
  }

  const toggleBtn = () => {
    const isModalOpen = document.documentElement.classList.contains('modal-lock');
    btn.classList.toggle('show', !isModalOpen && window.scrollY > 180);
  };

  window.addEventListener('scroll', toggleBtn, { passive: true });
  toggleBtn();

  btn.addEventListener('click', () => {
    window.scrollTo({ top: 0, behavior: 'smooth' });
  });
})();