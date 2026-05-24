# CURRENT TASK — PHASE 8

## Phase Name

Final Testing + Security Review + Cleanup + Documentation Sync

---

## Current Status

Semua fitur utama sudah selesai:

- database
- model
- seeder
- admin auth
- admin dashboard
- vendor verification
- tender management
- aanwijzing
- participant monitoring
- bidding monitoring
- winner selection
- result
- purchase order
- tender history
- vendor API

Sekarang lanjut Phase 8.

---

## Main Goal

Melakukan final review agar project stabil untuk demo dan sesuai dokumentasi API Apidog.

Fokus:
- bug fixing
- security check
- route check
- validation check
- UI cleanup
- API response consistency
- seeder consistency
- demo flow check
- sinkronisasi dokumentasi Apidog

Jangan membuat fitur baru kecuali bug fix kecil.

---

# Scope Phase 8

## 1. Route Audit

Cek semua route:

- admin web route
- vendor API route

Pastikan:

### Admin Web Routes
- route admin memakai `auth`
- route admin memakai `role:admin`
- route login admin bisa diakses guest
- route logout hanya untuk admin login
- route admin memakai session + CSRF
- route create/edit/show tidak bentrok
- root redirect `/` tidak dihitung sebagai endpoint fitur utama

### Vendor API Routes
- public route hanya:
  - register
  - login
  - forgot password
  - reset password
  - tender listing
  - tender detail
- protected route memakai `auth.api`
- protected route vendor harus memastikan user role vendor melalui middleware atau controller
- tender listing/detail public tidak boleh menampilkan tender draft
- tender listing/detail public tidak boleh membocorkan data internal admin

Expected route count:
- Vendor Mobile API: 21 endpoint
- Admin Web Routes: 27 endpoint
- Total fitur utama: 48 endpoint

Catatan:
- route `/` root redirect tidak dihitung sebagai fitur bisnis
- Admin disebut Admin Web Routes, bukan Admin API

Output:
- daftar route utama
- catatan route yang perlu diperbaiki

---

## 2. Security Review

Cek:

- password selalu di-hash
- CSRF aktif di form admin
- role admin aktif di admin route
- vendor tidak bisa akses endpoint admin
- admin tidak memakai endpoint vendor kecuali memang allowed
- protected vendor API tidak bisa diakses tanpa token
- protected vendor API tidak bisa diakses oleh user non-vendor
- upload dokumen divalidasi tipe dan ukuran file
- bidding divalidasi waktu
- vendor pending/rejected tidak bisa join tender
- vendor pending/rejected tidak bisa bidding
- vendor belum join tender tidak bisa bidding
- vendor tidak bisa melihat bid vendor lain
- vendor tidak bisa mengubah bid vendor lain
- tender draft tidak tampil di public API
- tidak ada route debug terbuka
- tidak ada hardcoded token/password di logic utama

Jangan install package baru.
Jangan mengganti auth strategy.

Jika kelemahan ditemukan pada `auth.api`, cukup laporkan atau perbaiki minor tanpa mengganti arsitektur.

---

## 3. Validation Review

Cek validasi:

### Auth
- register
- login
- forgot password
- reset password
- change password

### Vendor
- vendor profile update
- upload dokumen vendor

### Tender
- tender create
- tender update
- tender status update
- timeline tender:
  - start_date wajib
  - end_date setelah start_date
  - bidding_start wajib
  - bidding_end setelah bidding_start
  - bidding_start tidak boleh sebelum start_date
  - bidding_end tidak boleh setelah end_date

### Aanwijzing
- title wajib
- content wajib
- published_at valid

### Bidding
- bid_amount wajib
- bid_amount harus lebih dari 0
- bidding hanya dalam periode valid
- vendor harus approved
- vendor harus participant

### Winner Selection
- selected bid harus valid
- selected vendor harus participant
- selection_method valid:
  - lowest_price
  - admin_consideration
- winner tidak boleh duplicate untuk tender yang sama

### Purchase Order
- PO hanya setelah winner selected
- po_number unique
- issued_date valid
- amount valid

---

## 4. Seeder Review

Pastikan seeder membuat data demo yang cukup:

- admin
- vendor pending
- vendor approved
- dokumen vendor
- tender draft
- tender open/bidding
- participant
- bid
- bid history
- result optional
- PO optional

Pastikan:
- password di-hash
- data demo konsisten
- seeder tidak rawan duplicate fatal jika dijalankan ulang
- timeline tender dummy masuk akal
- vendor approved punya data yang cukup untuk demo
- vendor pending tetap tidak bisa join/bid

---

## 5. UI Cleanup Admin

Rapikan:

- sidebar
- navbar
- table
- forms
- badges
- buttons
- empty states
- flash message
- error message
- halaman detail

Gunakan Tailwind CSS.

Jangan pakai:
- Bootstrap
- AdminLTE
- template dashboard berat

UI cukup sederhana, rapi, konsisten, dan mudah didemokan.

---

## 6. API Response Consistency

Pastikan semua API vendor memakai format:

### Success
{
  "status": "success",
  "message": "...",
  "data": ...
}

### Error
{
  "status": "error",
  "message": "...",
  "errors": ...
}

Cek semua endpoint Vendor API:

### Public Auth
- POST /api/auth/register
- POST /api/auth/login
- POST /api/auth/forgot-password
- POST /api/auth/reset-password

### Public Tender
- GET /api/tenders
- GET /api/tenders/{tender}

### Protected Auth
- POST /api/auth/logout
- GET /api/auth/me
- PUT /api/auth/change-password

### Vendor Profile
- GET /api/vendors/me
- PUT /api/vendors/me
- GET /api/vendors/status

### Vendor Documents
- GET /api/vendors/documents
- POST /api/vendors/documents

### Tender Action
- POST /api/tenders/{tender}/participants
- GET /api/tenders/{tender}/announcements

### Bidding
- GET /api/tenders/{tender}/bids/me
- POST /api/tenders/{tender}/bids
- PUT /api/tenders/{tender}/bids/{bid}

### Result
- GET /api/tenders/{tender}/result
- GET /api/tenders/{tender}/winner

---

## 7. Apidog Sync Check

Cek apakah endpoint backend sama dengan dokumentasi Apidog:

- method
- URL
- body
- params
- response
- auth
- role access
- status code

Jika ada beda, beri laporan.

Jangan otomatis mengubah dokumentasi Apidog karena file Apidog ada di luar project.

Catatan:
- Vendor Mobile API berada di `routes/api.php`
- Admin Web Routes berada di `routes/web.php`
- Admin web route tidak harus dipaksa menjadi REST API

---

## 8. Demo Flow Checklist

Pastikan flow demo bisa dilakukan.

### Admin Flow
1. login admin
2. lihat dashboard
3. lihat vendor pending
4. approve vendor
5. buat tender
6. update status tender
7. tambah aanwijzing
8. lihat participant
9. lihat bids
10. lihat bid history
11. pilih winner
12. lihat result
13. generate PO
14. lihat PO
15. lihat history tender
16. logout admin

### Vendor API Flow
1. register vendor
2. login
3. upload dokumen
4. lihat status
5. lihat tender
6. lihat detail tender
7. join tender
8. lihat aanwijzing
9. submit bid
10. update bid
11. lihat bid sendiri
12. lihat winner
13. lihat result
14. logout

---

# Files That May Be Edited

Boleh edit file yang dibutuhkan untuk bug fix:

- routes/web.php
- routes/api.php
- controllers
- requests
- views
- services
- seeders
- models

Jangan edit migration kecuali ada bug fatal dan user setuju.

---

# Strict Rules

Jangan:
- membuat fitur baru besar
- membuat tabel baru
- install package baru
- mengubah arsitektur
- mengganti auth strategy
- menghapus fitur yang sudah ada
- menjalankan migrate:fresh tanpa izin
- mengganti session admin menjadi API token
- mengganti auth.api menjadi Sanctum/Passport tanpa izin
- membuat payment/contract/chat/review

Wajib:
- jelaskan setiap bug yang ditemukan
- jelaskan setiap file yang diubah
- jelaskan cara test ulang
- jaga scope hanya final polish dan bug fix
- pertahankan Admin Web Routes memakai session + CSRF
- pertahankan Vendor API memakai custom Bearer token `auth.api`
- pertahankan Tailwind CSS untuk admin UI

---

# Expected Output

Setelah selesai, jelaskan:

1. Bug yang ditemukan.
2. Bug yang diperbaiki.
3. File yang diubah.
4. Route yang dicek.
5. Security checklist hasil akhir.
6. Demo flow yang sudah siap.
7. Hal yang masih perlu manual testing.
8. Perbedaan backend dengan dokumentasi Apidog jika ada.
9. Rekomendasi final sebelum demo.

---

# Audit Mode Instruction

Jika user meminta audit saja:

- Jangan mengubah kode.
- Jangan membuat file baru.
- Jangan menjalankan command destructive.
- Berikan laporan audit saja.

Jika user meminta fix:

- Perbaiki hanya item bug/security/cleanup.
- Jangan membuat fitur baru besar.
- Jangan mengubah arsitektur.