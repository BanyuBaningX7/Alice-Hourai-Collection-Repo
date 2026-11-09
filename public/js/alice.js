/* =====================================================================
   alice.js
   1) Injeksi style pengaman (memastikan seluruh perbaikan UI aktif seketika)
   2) Loading screen pembuka (muncul saat buka web & setiap kali Reload Ctrl+R)
   3) Animasi Loading Fitur di Tengah Layar (rapi, kapsul kaca tepat di tengah)
   4) Dropdown Select Kustom (radius border selalu rapi & menyatu saat dibuka)
   5) Toast, dialog konfirmasi, kata sandi
   6) Pop-up detail buku (terkunci di tengah layar)
   7) Tombol auto-scroll ke atas di pojok kanan bawah
   ===================================================================== */

// ---------- 1) Pengaman CSS Runtime ----------
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
    .empty, .card.empty, .empty.card {
      display: flex !important; flex-direction: column !important; align-items: center !important;
      justify-content: center !important; text-align: center !important; min-height: 92px !important;
      padding: 24px 20px !important; gap: 8px !important;
    }
    .empty p, .empty h3 { margin: 0 !important; line-height: 1.45 !important; }
    td.empty { display: table-cell !important; text-align: center !important; padding: 36px 16px !important; vertical-align: middle !important; }
    input[type="date"] { color-scheme: dark !important; min-height: 48px !important; }
    input[type="date"]::-webkit-calendar-picker-indicator {
      width: 22px !important; height: 22px !important; padding: 4px !important; margin-left: 8px !important;
      border-radius: 7px !important; cursor: pointer !important; opacity: 1 !important; filter: none !important;
      background-color: rgba(68,138,255,.2) !important;
      background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%2370A5FF' stroke-width='2.2' stroke-linecap='round' stroke-linejoin='round'%3E%3Crect x='3' y='4' width='18' height='18' rx='2' ry='2'/%3E%3Cline x1='16' y1='2' x2='16' y2='6'/%3E%3Cline x1='8' y1='2' x2='8' y2='6'/%3E%3Cline x1='3' y1='10' x2='21' y2='10'/%3E%3C/svg%3E") !important;
      background-repeat: no-repeat !important; background-position: center !important; background-size: 16px 16px !important;
      border: 1px solid rgba(68,138,255,.45) !important;
    }
    input[type="date"]::-webkit-calendar-picker-indicator:hover {
      background-color: #4080EC !important;
      background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23FFFFFF' stroke-width='2.2' stroke-linecap='round' stroke-linejoin='round'%3E%3Crect x='3' y='4' width='18' height='18' rx='2' ry='2'/%3E%3Cline x1='16' y1='2' x2='16' y2='6'/%3E%3Cline x1='8' y1='2' x2='8' y2='6'/%3E%3Cline x1='3' y1='10' x2='21' y2='10'/%3E%3C/svg%3E") !important;
      border-color: #448AFF !important; box-shadow: 0 0 12px rgba(68,138,255,.55) !important;
    }
    .c-select { position: relative; display: inline-block; vertical-align: middle; }
    .field .c-select { display: block; width: 100%; }
    .c-select-native { position: absolute !important; opacity: 0 !important; pointer-events: none !important; width: 0 !important; height: 0 !important; }
    .c-select-btn {
      display: inline-flex; align-items: center; justify-content: space-between; gap: 14px;
      width: 100%; min-height: 48px; padding: 11px 16px; border-radius: 12px;
      border: 1px solid rgba(255,255,255,.2); background: rgba(255,255,255,.05);
      color: #fff; font: inherit; cursor: pointer; backdrop-filter: blur(6px);
      transition: border-color .2s, box-shadow .2s, border-radius .15s, background .2s; white-space: nowrap;
    }
    .c-select.open .c-select-btn {
      border-color: #448AFF; box-shadow: 0 0 0 3px rgba(68,138,255,.22);
      border-bottom-left-radius: 0; border-bottom-right-radius: 0; background: #0D172A;
    }
    .c-select-btn i { font-size: 12px; color: #9AA4B2; transition: transform .2s ease, color .2s; }
    .c-select.open .c-select-btn i { transform: rotate(180deg); color: #448AFF; }
    .c-select-menu {
      position: absolute; left: 0; right: 0; top: 100%; min-width: 100%; z-index: 260;
      margin-top: -1px; padding: 6px; background: #0D172A;
      border: 1px solid #448AFF; border-top: 1px solid rgba(255,255,255,.12);
      border-radius: 0 0 12px 12px; box-shadow: 0 14px 32px rgba(0,0,0,.65);
      max-height: 240px; overflow-y: auto;
      opacity: 0; visibility: hidden; transform: translateY(-4px); pointer-events: none;
      transition: opacity .16s ease, transform .16s ease, visibility .16s;
    }
    .c-select.open .c-select-menu { opacity: 1; visibility: visible; transform: translateY(0); pointer-events: auto; }
    .c-select-item {
      display: flex; align-items: center; justify-content: space-between;
      width: 100%; padding: 9px 12px; border: 0; border-radius: 8px;
      background: transparent; color: #dfe6f3; font: inherit; font-size: 15px;
      text-align: left; cursor: pointer; transition: background .15s, color .15s;
    }
    .c-select-item:hover { background: rgba(68,138,255,.18); color: #fff; }
    .c-select-item.active { background: #4080EC; color: #fff; font-weight: 700; }
    [data-page].is-loading::before, [data-page].is-loading::after { display: none !important; content: none !important; }
    #page-loader {
      position: fixed; inset: 0; z-index: 850; display: grid; place-items: center;
      pointer-events: none; opacity: 0; visibility: hidden; transition: opacity .18s ease, visibility .18s;
    }
    body.has-rail #page-loader { padding-right: var(--rail, 56px); }
    .admin #page-loader { padding-left: 260px; }
    @media (max-width: 900px) { .admin #page-loader { padding-left: 0; } }
    @media (max-width: 820px) { body.has-rail #page-loader { padding-right: 0; padding-bottom: 64px; } }
    #page-loader.active { opacity: 1; visibility: visible; }
    #page-loader .pl-bar {
      position: fixed; left: 0; right: 0; top: 0; height: 3px;
      background: linear-gradient(90deg, transparent, #4080EC, #70A5FF, transparent) no-repeat;
      background-size: 38% 100%; animation: pbar .95s linear infinite;
    }
    #page-loader .pl-card {
      display: inline-flex; align-items: center; gap: 14px; padding: 14px 24px; border-radius: 999px;
      background: rgba(10,18,32,.92); border: 1px solid rgba(68,138,255,.4);
      backdrop-filter: blur(10px); box-shadow: 0 12px 32px rgba(0,0,0,.65), 0 0 22px rgba(68,138,255,.25);
    }
    #page-loader .pl-ring { position: relative; width: 24px; height: 24px; flex: none; }
    #page-loader .pl-ring::before { content: ""; position: absolute; inset: 0; border-radius: 50%; border: 2.5px solid rgba(68,138,255,.22); }
    #page-loader .pl-ring::after {
      content: ""; position: absolute; inset: 0; border-radius: 50%;
      border: 2.5px solid transparent; border-top-color: #4080EC; border-right-color: #70A5FF;
      animation: spin .7s cubic-bezier(.45,.05,.55,.95) infinite;
    }
    #page-loader .pl-text { font: 600 14.5px/1 'Roboto', sans-serif; letter-spacing: .04em; color: #fff; white-space: nowrap; }
  `;
  document.head.appendChild(st);
})();

// ---------- 2) Loading screen pembuka (tampil saat pertama buka & setiap kali Reload / Ctrl+R) ----------
(function () {
  const el = document.getElementById('loader');
  if (!el) return;

  const navEntry = performance.getEntriesByType && performance.getEntriesByType('navigation')[0];
  const isReload = (navEntry && navEntry.type === 'reload') || (performance.navigation && performance.navigation.type === 1);

  const skipIntro = !isReload && document.documentElement.classList.contains('no-intro');
  try { sessionStorage.removeItem('aliceSeen'); } catch (e) {}

  if (isReload) {
    document.documentElement.classList.remove('no-intro');
  } else if (skipIntro) {
    el.remove();
    return;
  }

  const DURATION = 4000;
  const BLINK = 720;
  const HOLD = 300;
  const COVER = 1100;
  const WHITE_HOLD = 350;
  const FADE = 1100;
  const MAX_TOPO = 0.65;

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
    el.classList.add('ld-blink', 'blink');
    setTimeout(() => {
      el.classList.add('ld-go', 'go');
      setTimeout(() => {
        el.classList.add('ld-cover');
        setTimeout(() => {
          el.classList.add('ld-fade', 'fade');
          setTimeout(remove, FADE + 100);
        }, WHITE_HOLD);
      }, COVER);
    }, BLINK + HOLD);
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
    const eased = Math.pow(pct / 100, 1.7);
    el.style.setProperty('--ld-topo', (eased * MAX_TOPO).toFixed(3));
    el.style.setProperty('--ld-grid', (eased * 0.22).toFixed(3));
    count.style.top = Math.min(h - 125, Math.max(30, (pct / 100) * h - 75)) + 'px';
    status.textContent = p < 100 ? 'Memuat...' : 'Siap';

    if (p >= 100) { finish(); return; }
    requestAnimationFrame(tick);
  }
  requestAnimationFrame(tick);
})();

// ---------- 3) Animasi Loading Fitur Tepat di Tengah Layar ----------
const page = document.querySelector('[data-page]');
let pageLoader = document.getElementById('page-loader');
if (!pageLoader) {
  pageLoader = document.createElement('div');
  pageLoader.id = 'page-loader';
  pageLoader.setAttribute('aria-hidden', 'true');
  pageLoader.innerHTML = '<div class="pl-bar"></div><div class="pl-card"><div class="pl-ring"></div><span class="pl-text">Memuat halaman...</span></div>';
  document.body.appendChild(pageLoader);
}

const startLoading = () => {
  try { sessionStorage.setItem('aliceSeen', '1'); } catch (e) {}
  if (page) page.classList.add('is-loading');
  if (pageLoader) pageLoader.classList.add('active');
};

const stopLoading = () => {
  if (page) page.classList.remove('is-loading');
  if (pageLoader) pageLoader.classList.remove('active');
};

document.addEventListener('click', (e) => {
  const a = e.target.closest('a[href]');
  if (!a || e.defaultPrevented || e.button !== 0 || e.ctrlKey || e.metaKey || e.shiftKey) return;
  if (a.hasAttribute('data-book') && document.getElementById('book-dialog')) return;
  if (a.target === '_blank' || a.hasAttribute('download')) return;
  const href = a.getAttribute('href');
  if (!href || href.startsWith('#') || href.startsWith('javascript:')) return;
  const u = new URL(a.href, location.href);
  if (u.origin !== location.origin) return;
  if (u.pathname === location.pathname && u.search === location.search) return;
  startLoading();
});

document.addEventListener('submit', (e) => {
  const f = e.target;
  if (e.defaultPrevented) return;
  if (f.hasAttribute && f.hasAttribute('data-confirm') && !f.dataset.ok) return;
  startLoading();
});

window.addEventListener('pageshow', stopLoading);

// ---------- 4) Custom Rounded Dropdown untuk <select class="select"> ----------
(function () {
  document.querySelectorAll('select.select').forEach((sel) => {
    if (sel.dataset.customized) return;
    sel.dataset.customized = '1';

    const wrap = document.createElement('div');
    wrap.className = 'c-select';
    if (sel.style.width) wrap.style.width = sel.style.width;

    sel.parentNode.insertBefore(wrap, sel);
    wrap.appendChild(sel);
    sel.classList.add('c-select-native');

    const btn = document.createElement('button');
    btn.type = 'button';
    btn.className = 'c-select-btn';

    const labelSpan = document.createElement('span');
    const selectedOpt = sel.options[sel.selectedIndex] || sel.options[0];
    labelSpan.textContent = selectedOpt ? selectedOpt.textContent : '';

    const icon = document.createElement('i');
    icon.className = 'fa-solid fa-chevron-down';

    btn.append(labelSpan, icon);
    wrap.appendChild(btn);

    const menu = document.createElement('div');
    menu.className = 'c-select-menu';

    Array.from(sel.options).forEach((opt) => {
      const item = document.createElement('button');
      item.type = 'button';
      item.className = 'c-select-item' + (opt.selected ? ' active' : '');
      item.textContent = opt.textContent;
      item.addEventListener('click', () => {
        sel.value = opt.value;
        labelSpan.textContent = opt.textContent;
        menu.querySelectorAll('.c-select-item').forEach(el => el.classList.remove('active'));
        item.classList.add('active');
        wrap.classList.remove('open');

        sel.dispatchEvent(new Event('change', { bubbles: true }));
      });
      menu.appendChild(item);
    });

    wrap.appendChild(menu);

    btn.addEventListener('click', (e) => {
      e.stopPropagation();
      const isOpen = wrap.classList.contains('open');
      document.querySelectorAll('.c-select.open').forEach(w => w.classList.remove('open'));
      if (!isOpen) wrap.classList.add('open');
    });
  });

  document.addEventListener('click', () => {
    document.querySelectorAll('.c-select.open').forEach(w => w.classList.remove('open'));
  });
})();

// ---------- 5) Toast hilang otomatis ----------
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

// ---------- 6) Pop-up detail buku (Terkunci di tengah layar) ----------
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
  bd.addEventListener('click', (e) => {
    const r = bd.getBoundingClientRect();
    if (e.clientX < r.left || e.clientX > r.right || e.clientY < r.top || e.clientY > r.bottom) closeBook();
  });
}

// ---------- 7) Tombol Auto-Scroll ke Atas di Pojok Kanan Bawah ----------
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

// ---------- 8) Dropdown Lonceng Notifikasi Jatuh Tempo (Tutup Otomatis Saat Klik di Luar) ----------
(function () {
  const wrap = document.querySelector('[data-notif-wrap]');
  if (!wrap) return;
  const btn = wrap.querySelector('[data-notif-toggle]');
  const panel = wrap.querySelector('[data-notif-panel]');
  if (!btn || !panel) return;

  const closePanel = () => {
    panel.hidden = true;
    btn.setAttribute('aria-expanded', 'false');
  };

  btn.addEventListener('click', (e) => {
    e.stopPropagation();
    const isHidden = panel.hidden;
    panel.hidden = !isHidden;
    btn.setAttribute('aria-expanded', isHidden ? 'true' : 'false');
  });

  document.addEventListener('click', (e) => {
    if (!panel.hidden && !wrap.contains(e.target)) {
      closePanel();
    }
  });

  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && !panel.hidden) {
      closePanel();
    }
  });
})();

// ---------- 9) Validasi & Pembatas Format Nomor Identitas (KTP / Kartu Pelajar / KTM / SIM / Paspor) ----------
(function () {
  const typeSelect = document.getElementById('identity_type');
  const numInput = document.getElementById('identity_number');
  if (!typeSelect || !numInput) return;

  const labelEl = document.querySelector('label[for="identity_number"]');
  let hintEl = document.getElementById('id-hint');
  if (!hintEl) {
    hintEl = document.createElement('div');
    hintEl.id = 'id-hint';
    hintEl.className = 'hint';
    numInput.insertAdjacentElement('afterend', hintEl);
  }

  const RULES = {
    'KTP': {
      label: 'Nomor Induk Kependudukan / NIK KTP (Wajib 16 Digit)',
      placeholder: 'Contoh: 3275012005080003 (16 digit angka)',
      max: 16,
      numericOnly: true,
      validate: (v) => {
        if (v.length !== 16) return { ok: false, msg: `Wajib tepat 16 digit angka sesuai KTP. Terisi: ${v.length}/16 digit.` };
        if (/^(\d)\1{15}$/.test(v) || v === '1234567890123456' || v.startsWith('00')) {
          return { ok: false, msg: 'Nomor NIK tidak valid (tidak boleh angka urut/kembar asal atau diawali 00).' };
        }
        return { ok: true, msg: '✓ Format NIK KTP valid (16/16 digit angka).' };
      }
    },
    'Kartu Pelajar': {
      label: 'Nomor Induk Siswa Nasional / NISN (Wajib 10 Digit)',
      placeholder: 'Contoh: 0065432198 (10 digit angka)',
      max: 10,
      numericOnly: true,
      validate: (v) => {
        if (v.length !== 10) return { ok: false, msg: `Wajib tepat 10 digit angka sesuai NISN Kartu Pelajar. Terisi: ${v.length}/10 digit.` };
        if (/^(\d)\1{9}$/.test(v) || v === '1234567890') {
          return { ok: false, msg: 'Nomor NISN tidak valid (tidak boleh angka urut/kembar asal).' };
        }
        return { ok: true, msg: '✓ Format NISN Kartu Pelajar valid (10/10 digit angka).' };
      }
    },
    'KTM': {
      label: 'Nomor Induk Mahasiswa / NIM (8 s/d 15 Karakter)',
      placeholder: 'Contoh: 2301010042 (8-15 karakter huruf/angka)',
      max: 15,
      numericOnly: false,
      validate: (v) => {
        if (v.length < 8 || v.length > 15) return { ok: false, msg: `Wajib 8 s/d 15 karakter sesuai NIM Kartu Mahasiswa. Terisi: ${v.length}/15.` };
        if (/^(.)\1+$/.test(v) || v === '12345678' || v === '1234567890') {
          return { ok: false, msg: 'Nomor NIM tidak boleh berisi karakter urut/kembar asal.' };
        }
        return { ok: true, msg: `✓ Format NIM Kartu Mahasiswa valid (${v.length} karakter).` };
      }
    },
    'SIM': {
      label: 'Nomor Surat Izin Mengemudi / SIM (12 s/d 16 Digit)',
      placeholder: 'Contoh: 3275012005080003 (12-16 digit angka)',
      max: 16,
      numericOnly: true,
      validate: (v) => {
        if (v.length < 12 || v.length > 16) return { ok: false, msg: `Wajib 12 s/d 16 digit angka sesuai kartu SIM. Terisi: ${v.length}/16 digit.` };
        if (/^(\d)\1+$/.test(v) || v.startsWith('123456789')) {
          return { ok: false, msg: 'Nomor SIM tidak valid (tidak boleh angka urut/kembar asal).' };
        }
        return { ok: true, msg: `✓ Format Nomor SIM valid (${v.length} digit angka).` };
      }
    },
    'Paspor': {
      label: 'Nomor Paspor (1–2 Huruf Diikuti 7 Digit Angka)',
      placeholder: 'Contoh: C1234567 atau AB1234567',
      max: 9,
      numericOnly: false,
      validate: (v) => {
        if (!/^[A-Z]{1,2}\d{7}$/.test(v)) {
          return { ok: false, msg: `Wajib 1–2 huruf kapital diikuti 7 angka (contoh: C1234567). Terisi: ${v.length}/9.` };
        }
        return { ok: true, msg: `✓ Format Nomor Paspor valid (${v}).` };
      }
    }
  };

  const applyRule = () => {
    const t = typeSelect.value || 'KTP';
    const rule = RULES[t] || RULES['KTP'];
    if (labelEl) labelEl.textContent = rule.label;
    numInput.placeholder = rule.placeholder;
    numInput.maxLength = rule.max;
    numInput.inputMode = rule.numericOnly ? 'numeric' : 'text';

    let val = numInput.value;
    if (rule.numericOnly) {
      val = val.replace(/\D+/g, '');
    } else {
      val = val.toUpperCase().replace(/[^A-Z0-9]/g, '');
    }
    if (val.length > rule.max) {
      val = val.slice(0, rule.max);
    }
    if (numInput.value !== val) {
      numInput.value = val;
    }

    const res = rule.validate(val);
    hintEl.textContent = res.msg;
    hintEl.classList.toggle('is-valid', res.ok);
    hintEl.classList.toggle('is-invalid', !res.ok && val.length > 0);
  };

  typeSelect.addEventListener('change', applyRule);
  numInput.addEventListener('input', applyRule);
  applyRule();
})();
