# Step 1: Database & SQL

## Deskripsi
Tahap ini bertujuan untuk membangun struktur database, mengisi data awal (dummy data), dan menyusun kueri SQL untuk melakukan perhitungan diskon dinamis sesuai dengan aturan bisnis yang ditetapkan.

## Struktur Database
1. **Database**: `db_skill_test`
2. **Tabel `user`**: Menyimpan data pengguna.
   - `id`: Primary Key, Auto Increment.
   - `name`: Nama pengguna.
   - `email`: Email pengguna.
3. **Tabel `master_items`**: Menyimpan data barang dan estimasi harga.
   - `id`: Primary Key, Auto Increment.
   - `id_name`: Foreign Key yang merujuk ke `user.id`.
   - `items`: Nama barang.
   - `estimate_price`: Harga perkiraan (Tipe `DECIMAL` untuk presisi penyimpanan).

## Logika Bisnis (Diskon)
Perhitungan diskon dilakukan menggunakan logika `CASE WHEN` berdasarkan `estimate_price`:
1. **< 50.000**: Diskon 2% (0.02).
2. **50.000 s/d 1.500.000**: Diskon 3.5% (0.035).
3. **> 1.500.000**: Diskon 5% (0.05).

Rumus perhitungan `fix_price`:
`estimate_price - (estimate_price * discount)`

## Penanganan Format Output
Untuk memenuhi kebutuhan tampilan di mana angka desimal tidak boleh memiliki nol berlebih di belakang koma (contoh: `20000.00` menjadi `20000` dan `0.02000` menjadi `0.02`), solusi menggunakan fungsi `CAST(... AS DOUBLE)` pada bagian `SELECT`.

Tipe data `DOUBLE` pada MySQL/MariaDB secara otomatis memangkas *trailing zeros* saat ditampilkan ke klien, memberikan output yang bersih sesuai spesifikasi visual soal.