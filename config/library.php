<?php

// Aturan perpustakaan. Ubah angka di sini bila kebijakan berubah.
return [
    'max_books'        => 2,   // maksimal buku aktif (diajukan + dipinjam) per anggota
    'loan_days'        => 7,   // masa pinjam maksimal (hari)
    'due_warning_days' => 2,   // deteksi notifikasi peringatan jatuh tempo mulai H-2
];
