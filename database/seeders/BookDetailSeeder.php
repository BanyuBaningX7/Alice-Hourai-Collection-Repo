<?php

namespace Database\Seeders;

use App\Models\Book;
use Illuminate\Database\Seeder;

/**
 * Mengisi deskripsi dan sampul untuk buku contoh (dicocokkan lewat ISBN).
 * Aman dijalankan berulang kali:  php artisan db:seed --class=BookDetailSeeder
 * File sampul ada di storage/app/public/covers (diakses lewat public/storage setelah storage:link).
 */
class BookDetailSeeder extends Seeder
{
    public static function details(): array
    {
        return [
            '9786020000011' => ['jejak-awan',
                'Lara, pengantar surat di desa pegunungan, menemukan jejak kaki bercahaya yang muncul di antara kabut setiap fajar. Mengikuti jejak itu, ia menyusuri rahasia keluarganya dan belajar bahwa kehilangan bisa menjadi awal sebuah perjalanan. Novel lembut tentang keberanian kecil, persahabatan, dan kenangan yang melayang seperti awan.'],
            '9786020000028' => ['hutan-biru',
                'Tiga sahabat menyusuri hutan yang konon bercahaya biru saat bulan purnama untuk mencari mata air legendaris. Di antara sungai berarus deras, jembatan akar, dan hewan-hewan langka, mereka harus bekerja sama sebelum musim hujan menutup jalan pulang. Petualangan penuh debar yang mengajarkan arti menjaga alam.'],
            '9786020000035' => ['ujung-galaksi',
                'Tahun 2412, kapal riset Nawasena menerima sinyal misterius dari tepi galaksi. Dr. Arini memimpin awak kapal menembus lorong ruang-waktu untuk menemukan sumbernya dan menghadapi pertanyaan terbesar manusia: apakah kita sendirian? Fiksi ilmiah tentang sains, etika, dan keberanian menatap hal yang belum diketahui.'],
            '9786020000042' => ['cahaya-senja',
                'Kumpulan kisah dan renungan tentang bangkit setelah gagal, bersyukur pada hal sederhana, dan menemukan makna di akhir hari yang melelahkan. Setiap bab ditutup latihan singkat yang dapat dipraktikkan sebelum matahari terbenam. Cocok dibaca perlahan sambil menikmati senja.'],
            '9786020000059' => ['nusantara-kuno',
                'Menelusuri kerajaan-kerajaan awal di kepulauan Nusantara: jalur perdagangan rempah, prasasti, candi, hingga hubungan dengan dunia luar. Disusun dari bukti arkeologi dan naskah kuno dengan bahasa populer, lengkap dengan peta dan lini masa untuk pembaca umum maupun pelajar.'],
            '9786020000066' => ['strategi-usaha',
                'Panduan praktis merintis dan mengembangkan usaha kecil: memahami pelanggan, menetapkan harga, mengelola arus kas, hingga membangun merek dengan modal terbatas. Dilengkapi studi kasus pelaku UMKM Indonesia dan lembar kerja sederhana untuk menyusun rencana usaha.'],
            '9786020000073' => ['langkah-seorang-guru',
                'Kisah hidup seorang guru desa yang mengajar di sekolah beratap seng selama empat dekade. Dari berjalan kaki menyeberangi sungai demi murid-muridnya hingga menyaksikan mereka menjadi dokter, petani, dan pemimpin, buku ini memotret ketekunan dan arti mendidik dengan hati.'],
            '9786020000080' => ['hikayat-lama',
                'Antologi hikayat dan cerita rakyat Melayu klasik yang dituturkan turun-temurun: kisah raja bijak, ksatria setia, dan hewan yang pandai berbicara. Bahasa disesuaikan agar mudah dibaca tanpa menghilangkan nuansa tutur lama, disertai catatan tentang asal-usul tiap cerita.'],
        ];
    }

    public function run(): void
    {
        foreach (self::details() as $isbn => [$cover, $description]) {
            Book::where('isbn', $isbn)->update([
                'cover_path'  => "covers/{$cover}.svg",
                'description' => $description,
            ]);
        }
    }
}
