<?php

return [
    'accepted'  => ':attribute harus disetujui.',
    'alpha_dash' => ':attribute hanya boleh berisi huruf, angka, strip, dan garis bawah.',
    'confirmed' => 'Konfirmasi :attribute tidak cocok.',
    'current_password' => 'Kata sandi lama salah.',
    'email'     => ':attribute harus berupa alamat email yang valid.',
    'exists'    => ':attribute yang dipilih tidak valid.',
    'image'     => ':attribute harus berupa gambar.',
    'in'        => ':attribute yang dipilih tidak valid.',
    'integer'   => ':attribute harus berupa angka bulat.',
    'max'       => [
        'string'  => ':attribute maksimal :max karakter.',
        'numeric' => ':attribute maksimal :max.',
        'file'    => ':attribute maksimal :max KB.',
    ],
    'min'       => [
        'string'  => ':attribute minimal :min karakter.',
        'numeric' => ':attribute minimal :min.',
    ],
    'mimes'     => ':attribute harus berformat: :values.',
    'numeric'   => ':attribute harus berupa angka.',
    'required'  => ':attribute wajib diisi.',
    'size'      => [
        'string'  => ':attribute harus tepat :size karakter.',
        'numeric' => ':attribute harus bernilai :size.',
    ],
    'string'    => ':attribute harus berupa teks.',
    'unique'    => ':attribute sudah dipakai.',

    'attributes' => [
        'name' => 'Nama', 'username' => 'Username', 'email' => 'Email', 'phone' => 'Telepon',
        'password' => 'Kata sandi', 'current_password' => 'Kata sandi lama', 'role' => 'Role',
        'status' => 'Status', 'category_id' => 'Kategori', 'title' => 'Judul', 'author' => 'Penulis',
        'publisher' => 'Penerbit', 'year' => 'Tahun', 'isbn' => 'ISBN', 'price' => 'Harga',
        'stock_total' => 'Stok total', 'location' => 'Lokasi rak', 'description' => 'Deskripsi',
        'cover' => 'Sampul', 'reason' => 'Alasan', 'kondisi' => 'Kondisi',
    ],
];
