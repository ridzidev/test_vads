# Step 2: Backend API (Laravel)

## Deskripsi
Implementasi API menggunakan Laravel 11 untuk menangani otentikasi JWT Manual dan pengambilan data transaksi dengan kalkulasi logika bisnis yang ketat.

## 1. Persiapan Environment
- **Framework**: Laravel 11.x
- **PHP Version**: 8.3
- **Database**: MySQL (User khusus: `dev_user`)
- **Dependencies**: Native PHP (Tanpa library JWT pihak ketiga untuk performa ringan).

## 2. Implementasi Endpoint

### A. Generate Token (Soal 4)
- **URL**: `POST /api/get-token`
- **Logic**: 
  - Menerima `name` dan `date_request`.
  - Membuat signature HMAC SHA256 (HS256) secara manual.
  - Payload token diset expire dalam 1 jam.

### B. Get Data Transaction (Soal 5)
- **URL**: `POST /api/get-data`
- **Security Headers**:
  - `secretKey`: `Qw3rty09!@#`
  - `Authorization`: `Bearer <JWT_TOKEN>`
- **Logic**:
  - Validasi ketat terhadap token (signature & expiration) dan secret key.
  - Query ke database menggunakan `JOIN` dan `CASE WHEN` untuk menghitung diskon dinamis.
  - Formatting output JSON khusus:
    - Mengubah titik desimal menjadi koma pada `dicount`.
    - Membulatkan `fix_price`.

## 3. Snippet Code Utama
**ApiController.php (Extract)**:
```php
// Logic Diskon
DB::raw("CASE 
    WHEN m.estimate_price < 50000 THEN 0.02
    WHEN m.estimate_price >= 50000 AND m.estimate_price <= 1500000 THEN 0.035
    WHEN m.estimate_price > 1500000 THEN 0.05
END as discount_val")

// Formatting
'dicount' => str_replace('.', ',', (string)(0 + $item->discount_val)), 
'fix_price' => (string)round($item->fix_price_val)
