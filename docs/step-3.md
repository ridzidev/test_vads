# Step 3: Modul Master Customer (Frontend)

## Deskripsi
Implementasi halaman web "Master Customer" sesuai Soal 2, yang berfungsi untuk mengambil data dari API publik (`randomuser.me`), memanipulasi struktur datanya (JSON Flattening), dan menampilkannya dalam tabel interaktif.

## Implementasi Teknis

### 1. Arsitektur Frontend
- **Framework View**: Laravel Blade Templates.
- **Styling**: Tailwind CSS (via CDN) untuk tata letak responsif dan desain modern sesuai wireframe.
- **Scripting**: jQuery & AJAX untuk pengambilan data asinkronus tanpa reload halaman.

### 2. Alur Data (Workflow)
1.  **User Request**: Pengguna mengakses route `/master-customer`.
2.  **Controller**: `CustomerController` memuat view `master_customer.blade.php`.
3.  **Client-Side Fetching**:
    - Saat halaman siap (`document.ready`), jQuery melakukan request AJAX ke `https://randomuser.me/api?results=10&page=1`.
4.  **Data Manipulation (Inti Soal 2)**:
    - Data mentah (Nested JSON) diterima dari API.
    - Script melakukan *mapping* untuk meratakan (flattening) objek.
    - **Contoh Manipulasi**:
      - `user.name.first` + `last` digabung menjadi single string `name`.
      - `user.login.uuid` diekstrak ke level root objek menjadi `login_uuid`.
5.  **Rendering**: Data yang sudah dimanipulasi disisipkan ke dalam tabel HTML (DOM Manipulation).

### 3. Poin Penting Kode (Snippet Logika)

**Manipulasi JSON (Javascript):**
Logika utama untuk mengubah struktur data sebelum ditampilkan:
```javascript
// Mapping dari Nested Object ke Flat Object
const manipulatedData = rawData.map(user => {
    return {
        name: `${user.name.title} ${user.name.first} ${user.name.last}`, // String Template
        email: user.email,
        login_uuid: user.login.uuid,     // Flattening
        login_username: user.login.username,
        // ... field lainnya
    };
});
