Berikut adalah listing pekerjaan (To-Do List) berdasarkan dokumen PDF:

**1. Database & SQL (Soal 3)**
- Setup database MySQL/MariaDB.
- Membuat tabel `user` dan `master_items`.
- Input data dummy sesuai tabel di soal.
- Membuat script SQL (Query) untuk logika Diskon dan kalkulasi `fix_price`.

**2. Backend API (Soal 4 & 5)**
- Setup project Laravel (atau YII2).
- Membuat API Controller.
- Membuat endpoint POST untuk Generate Token.
- Membuat endpoint POST untuk Get Data Transaksi (menggunakan Bearer Token + Logic SQL poin 1).

**3. Modul Master Customer (Soal 2)**
- Membuat view/halaman Master Customer di Laravel.
- Integrasi fetch data dari API eksternal (`randomuser.me`).
- Implementasi script manipulasi JSON (Flattening data) di backend/frontend.
- Menampilkan hasil data ke tabel UI.

**4. Real-time Server (Soal 1 - Node.js)**
- Setup project Node.js + Express.
- Install library `socket.io`.
- Membuat logic socket event: Join Room, Send/Receive Message.
- Membuat logic Timer: Queue (3 menit wait) & Auto-close session.
- Membuat logic Bot Auto-reply & Idle detection.

**5. Modul Live Chat (Soal 1 - Frontend/Integration)**
- Membuat UI Landing Page Login (Show/Hide Password).
- Membuat UI Session Register (Form + Custom Image Captcha).
- Membuat UI Chat Room (User Interface).
- Membuat UI Service Desk Dashboard (Agent Interface).
- Integrasi Client Socket.io ke UI.

**6. Finalisasi (Soal 6)**
- Export Database (Dump SQL).
- Cek file `.gitignore` (exclude node_modules/vendor).
- Upload full source code ke GitHub.

====


DROP TABLE IF EXISTS master_items;
DROP TABLE IF EXISTS user;


=====


2. Tech Stack Versioning
OS: Linux Mint 22 (Environment Dev)
Backend: PHP 8.2+ (Laravel 10/11)
Database: MariaDB / MySQL 8.0
Realtime: Node.js v18/v20 + Socket.io v4
Frontend: Blade Templates + JQuery + TailwindCSS (via CDN/NPM)
3. Alur Komunikasi
User (Browser) <--> Laravel (Port 8000): Meminta halaman HTML & Login.
User (Browser) <--> Laravel API (Port 8000): Request Token & Data Transaksi (AJAX).
User (Browser) <--> Node.js (Port 3000): Koneksi WebSocket untuk Chat.