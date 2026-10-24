// 1) Loading screen: tampil minimal 700 ms lalu hilang
const t0 = performance.now();
window.addEventListener('load', () => {
  const wait = Math.max(0, 700 - (performance.now() - t0));
  setTimeout(() => document.getElementById('loader')?.classList.add('hide'), wait);
});
window.addEventListener('pageshow', e => { if (e.persisted) document.getElementById('loader')?.classList.add('hide'); });

// 2) Toast hilang otomatis
document.querySelectorAll('.toast').forEach(t => setTimeout(() => { t.style.transition = 'opacity .4s'; t.style.opacity = 0; setTimeout(() => t.remove(), 400); }, 4500));

// 3) Konfirmasi: <form data-confirm="Pesan"> akan memunculkan dialog sebelum dikirim
const dlg = document.getElementById('confirm-dialog');
if (dlg) {
  let pending = null;
  document.querySelectorAll('form[data-confirm]').forEach(f => f.addEventListener('submit', e => {
    if (f.dataset.ok) return;            // sudah dikonfirmasi
    e.preventDefault(); pending = f;
    dlg.querySelector('[data-msg]').textContent = f.dataset.confirm;
    dlg.querySelector('[data-yes]').textContent = f.dataset.yes || 'Ya, lanjutkan';
    dlg.showModal();
  }));
  dlg.querySelector('[data-yes]').addEventListener('click', () => { dlg.close(); if (pending) { pending.dataset.ok = 1; pending.requestSubmit(); } });
  dlg.querySelector('[data-no]').addEventListener('click', () => { dlg.close(); pending = null; });
}

// 4) Tampilkan / sembunyikan kata sandi
document.querySelectorAll('[data-toggle-pw]').forEach(b => b.addEventListener('click', () => {
  const i = document.getElementById(b.dataset.togglePw);
  i.type = i.type === 'password' ? 'text' : 'password';
}));

// 5) Hitung karakter kata sandi (form Admin Panel mengikuti role yang dipilih)
const pw = document.getElementById('password'), role = document.getElementById('role'), hint = document.getElementById('pw-hint');
if (pw && hint) {
  const update = () => {
    const isAdmin = role ? role.value === 'admin' : hint.dataset.role === 'admin';
    hint.textContent = (isAdmin ? 'Minimal 30 karakter' : 'Tepat 10 karakter') + ' (boleh huruf, angka, simbol). Terisi: ' + pw.value.length + '/' + (isAdmin ? '30+' : '10');
  };
  pw.addEventListener('input', update); role?.addEventListener('change', update); update();
}
