# CURRENT TASK — PHASE 8

## Phase Name
Final Testing + Security Review + Cleanup + Documentation Sync

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
- vendor API

Sekarang lanjut Phase 8.

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

Jangan membuat fitur baru kecuali bug fix kecil.

---

# Scope Phase 8

## 1. Route Audit

Cek semua route:
- admin web route
- vendor API route

Pastikan:
- route admin memakai auth + role:admin
- route vendor API memakai auth sesuai strategi yang sudah dipilih
- route public hanya register/login/forgot password
- tidak ada route debug yang terbuka

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
- upload dokumen divalidasi
- bidding divalidasi waktu
- vendor pending/rejected tidak bisa join/bid
- vendor tidak bisa melihat bid vendor lain

Jangan install package baru.

---

## 3. Validation Review

Cek validasi:
- tender timeline
- vendor verification
- upload file
- bid amount
- winner selection
- PO unique number
- reset password

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

Seeder harus bisa dijalankan ulang tanpa konflik fatal jika memungkinkan.

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

Gunakan Tailwind CSS.
Jangan pakai Bootstrap/AdminLTE.

---

## 6. API Response Consistency

Pastikan semua API vendor memakai format:

Success:
{
  "status": "success",
  "message": "...",
  "data": ...
}

Error:
{
  "status": "error",
  "message": "...",
  "errors": ...
}

---

## 7. Apidog Sync Check

Cek apakah endpoint backend sama dengan dokumentasi Apidog:
- method
- URL
- body
- params
- response
- auth

Jika ada beda, beri laporan.
Jangan otomatis mengubah dokumentasi Apidog karena file Apidog ada di luar project.

---

## 8. Demo Flow Checklist

Pastikan flow demo bisa dilakukan:

Admin:
1. login admin
2. lihat dashboard
3. lihat vendor pending
4. approve vendor
5. buat tender
6. update status tender
7. tambah aanwijzing
8. lihat participant
9. lihat bids
10. pilih winner
11. generate PO
12. lihat history

Vendor API:
1. register vendor
2. login
3. upload dokumen
4. lihat status
5. lihat tender
6. join tender
7. lihat aanwijzing
8. submit bid
9. update bid
10. lihat result

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

Jangan edit migration kecuali ada bug fatal dan saya setuju.

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

Wajib:
- jelaskan setiap bug yang ditemukan
- jelaskan file yang diubah
- jelaskan cara test ulang
- jaga scope hanya final polish dan bug fix

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