# Arsitektur Aplikasi Web Chat VADS

## 1. Struktur Direktori (Monorepo)
Kita memisahkan aplikasi menjadi dua bagian utama dalam satu repositori untuk memudahkan manajemen source code sesuai permintaan soal.

```text
test_vads/                  <-- Root Repository
├── backend/                <-- Laravel Framework (API + Frontend UI Views)
│   ├── app/                <-- Logic API & Controller
│   ├── resources/views/    <-- Frontend Views (Blade)
│   └── public/             <-- Assets (CSS/JS)
├── realtime-server/        <-- Node.js Service (Socket.io)
│   ├── index.js            <-- Entry point socket server
│   └── package.json        <-- Dependencies (socket.io, express)
├── database/               <-- Folder khusus dump SQL
│   └── dump-soal-3.sql     <-- Hasil Step 1
├── docs/                   <-- Dokumentasi pengerjaan
│   ├── step-1.md
│   ├── step-2.md
│   └── arsitektur.md
└── README.md






===

curl -X POST http://127.0.0.1:8000/api/get-data \
-H "Content-Type: application/json" \
-H "secretKey: Qw3rty09!@#" \
-H "Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJsYXJhdmVsLWFwaSIsInN1YiI6InVzZXJfdGVzdCIsImlhdCI6MTc2NDQwNDc3OCwiZXhwIjoxNzY0NDA4Mzc4fQ.GU-sxXQG_lO_0IvQpXnCRzMCYuCevrNu568lzpSfC5c" \
-d '{"name_customers":"jonatan christie", "date_request":"2025-11-25 17:02:00"}'