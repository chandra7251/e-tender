# PROJECT CONTEXT — E-Procurement Tender & Bidding System

## 1. Project Overview

Project ini adalah sistem E-Procurement / Tender & Bidding System berbasis Laravel.

Sistem ini bukan aplikasi listing tender biasa. Sistem harus mendukung workflow procurement end-to-end:

1. Vendor register.
2. Vendor upload dokumen perusahaan.
3. Admin memverifikasi vendor.
4. Admin membuat tender dan timeline.
5. Vendor melihat tender.
6. Vendor join tender.
7. Admin membuat aanwijzing / announcement.
8. Vendor melakukan bidding dalam periode waktu tertentu.
9. Admin memonitor bidding.
10. Admin menentukan pemenang.
11. Sistem menghasilkan hasil tender dan PO sederhana.
12. Sistem menyimpan histori proses tender.

---

## 2. Technology Stack

Project ini menggunakan teknologi berikut:

### Backend
- Laravel 13
- Laravel REST API
- Laravel Web Route untuk admin dashboard
- Blade untuk tampilan admin
- Session authentication untuk admin web
- CSRF protection untuk form admin
- Role-based access control menggunakan kolom `users.role`

### Mobile / Vendor App
- Ionic
- Angular
- Mobile app hanya bertugas sebagai client
- Semua business logic tetap diproses di backend Laravel

### Web Admin Dashboard
- Laravel Web
- Blade Template
- Tailwind CSS untuk styling admin dashboard
- Session + CSRF
- Admin dashboard berjalan langsung di Laravel

### Database
- MySQL atau PostgreSQL
- Untuk development saat ini boleh menggunakan database yang sudah dikonfigurasi di `.env`

### API Documentation
- Apidog
- Dokumentasi API dipakai untuk menjelaskan fitur dan kontrak API project

### Development Tool
- Antigravity
- Claude Sonnet 4.6 sebagai coding assistant

---

## 3. Role System

Sistem memiliki 2 role utama.

### Admin / Procurement

Platform:
- Laravel Web Dashboard

Tugas:
- login admin
- dashboard monitoring
- vendor management
- verifikasi vendor
- tender management
- timeline management
- aanwijzing management
- participant monitoring
- bidding monitoring
- winner selection
- result management
- purchase order
- history/audit

### Vendor

Platform:
- Mobile App / API Client

Tugas:
- register
- login
- forgot password
- reset password
- profile
- upload dokumen
- melihat status verifikasi
- melihat tender
- join tender
- melihat aanwijzing
- submit/update bidding
- melihat bid sendiri
- melihat hasil tender

---

## 4. Important Security Rule

### Admin Web

Admin web menggunakan:
- session authentication
- CSRF protection
- role:admin middleware

Admin route berada di:
- `routes/web.php`

Admin disebut:
- Admin Web Routes

Admin tidak disebut sebagai REST API murni karena menggunakan session dan CSRF.

### Vendor Mobile API

Vendor mobile/API menggunakan:
- custom token-based authentication
- Bearer token
- middleware `auth.api`

Implementasi sementara:
- token dikirim melalui header `Authorization: Bearer <token>`
- token disimpan pada kolom `users.remember_token`
- protected vendor route memakai middleware `auth.api`

Catatan:
- Jangan menggunakan Sanctum.
- Jangan install Passport.
- Jangan mengganti auth strategy pada Phase 8.
- Jika ditemukan kelemahan auth, cukup laporkan atau perbaiki minor tanpa mengganti arsitektur.
- Vendor API idealnya juga memastikan user memiliki role `vendor`.

### CSRF Rule

CSRF bukan pengganti login.  
CSRF hanya proteksi request web session.

---

## 5. Current Development Scope

Project saat ini berada pada Phase 8:

Final Testing + Security Review + Cleanup + Documentation Sync.

Fitur utama yang sudah dibuat:
- database migration
- model relationship
- seeder dasar
- admin authentication
- admin dashboard
- vendor management
- vendor verification
- tender management
- aanwijzing
- participant monitoring
- bidding monitoring
- winner selection
- result management
- purchase order
- tender history
- vendor mobile API

Scope saat ini:
- audit route
- audit security
- audit validation
- audit API response
- audit seeder
- cleanup UI admin
- sinkronisasi dengan dokumentasi Apidog
- demo flow testing

Jangan membuat fitur baru besar.
Jangan mengubah arsitektur.
Jangan membuat tabel baru.
Jangan mengganti auth strategy.
Jangan menjalankan migrate:fresh tanpa izin.

---

## 6. Database Tables Required

Tabel utama project:

1. users
2. vendors
3. vendor_documents
4. tenders
5. tender_participants
6. tender_announcements
7. bids
8. bid_histories
9. tender_results
10. purchase_orders
11. tender_histories
12. password_reset_tokens
13. sessions

Tabel Laravel default seperti cache dan jobs boleh tetap ada.

---

## 7. Tables Not Allowed

Jangan membuat tabel tambahan berikut tanpa izin:

- payments
- contracts
- chats
- chat_messages
- notifications
- reviews
- vendor_reviews
- categories
- tender_categories
- document_reviews

Alasannya: belum ada requirement wajib.

---

## 8. Table Design Summary

### users

Digunakan untuk admin dan vendor login.

Tambahan wajib:
- role: admin/vendor
- soft delete

### vendors

Menyimpan data perusahaan vendor.

Field utama:
- user_id
- company_name
- phone
- address
- verification_status: pending/approved/rejected
- verification_notes
- verified_by
- verified_at
- soft delete

Rule:
- vendor baru default pending
- hanya approved vendor yang bisa ikut tender

### vendor_documents

Menyimpan dokumen perusahaan vendor.

Field utama:
- vendor_id
- document_type: legalitas/izin_usaha/dokumen_pendukung
- file_name
- file_path
- mime_type
- file_size
- uploaded_at
- soft delete

### tenders

Menyimpan data tender dan timeline.

Field utama:
- created_by
- title
- description
- specification
- start_date
- end_date
- aanwijzing_date
- bidding_start
- bidding_end
- status: draft/open/aanwijzing/bidding/closed/finished
- soft delete

### tender_participants

Menyimpan vendor yang join tender.

Field utama:
- tender_id
- vendor_id
- joined_at
- soft delete

Constraint:
- unique tender_id + vendor_id

### tender_announcements

Menyimpan informasi aanwijzing / update tender.

Field utama:
- tender_id
- created_by
- title
- content
- published_at
- soft delete

### bids

Menyimpan bid aktif/terakhir vendor.

Field utama:
- tender_id
- vendor_id
- bid_amount
- notes
- submitted_at
- soft delete

Constraint:
- unique tender_id + vendor_id

### bid_histories

Menyimpan histori perubahan bid.

Field utama:
- bid_id
- tender_id
- vendor_id
- old_bid_amount
- new_bid_amount
- notes
- changed_at
- created_at

Catatan:
- tidak pakai soft delete
- append-only audit log

### tender_results

Menyimpan hasil tender dan pemenang.

Field utama:
- tender_id
- winner_vendor_id
- winning_bid_id
- winning_bid_amount
- selection_method: lowest_price/admin_consideration
- notes
- decided_by
- decided_at
- soft delete

### purchase_orders

Menyimpan PO sederhana hasil tender.

Field utama:
- tender_result_id
- tender_id
- vendor_id
- po_number
- amount
- issued_date
- notes
- generated_by
- soft delete

### tender_histories

Menyimpan histori proses tender.

Field utama:
- tender_id
- actor_id
- action
- old_status
- new_status
- description
- metadata
- created_at

Catatan:
- tidak pakai soft delete
- append-only audit log

Contoh action:
- tender_created
- status_changed
- vendor_joined
- bid_submitted
- bid_updated
- winner_selected
- po_generated
- vendor_approved
- vendor_rejected

---

## 9. Model Relationship

### User
- hasOne Vendor
- hasMany Tender as createdTenders
- hasMany TenderAnnouncement
- hasMany TenderHistory as histories

### Vendor
- belongsTo User
- hasMany VendorDocument
- hasMany TenderParticipant
- hasMany Bid
- hasMany TenderResult as wonResults

### VendorDocument
- belongsTo Vendor

### Tender
- belongsTo User as creator
- hasMany TenderParticipant
- hasMany TenderAnnouncement
- hasMany Bid
- hasOne TenderResult
- hasOne PurchaseOrder
- hasMany TenderHistory

### TenderParticipant
- belongsTo Tender
- belongsTo Vendor

### TenderAnnouncement
- belongsTo Tender
- belongsTo User as creator

### Bid
- belongsTo Tender
- belongsTo Vendor
- hasMany BidHistory

### BidHistory
- belongsTo Bid
- belongsTo Tender
- belongsTo Vendor

### TenderResult
- belongsTo Tender
- belongsTo Vendor as winner
- belongsTo Bid as winningBid
- belongsTo User as decider
- hasOne PurchaseOrder

### PurchaseOrder
- belongsTo TenderResult
- belongsTo Tender
- belongsTo Vendor
- belongsTo User as generator

### TenderHistory
- belongsTo Tender
- belongsTo User as actor

---

## 10. Seeder Requirement

Seeder dasar wajib membuat:

### Admin
- name: Admin Procurement
- email: admin@example.com
- password: password
- role: admin

### Vendor Pending
- user email: vendor.pending@example.com
- company_name: PT Pending Jaya
- verification_status: pending

### Vendor Approved
- user email: vendor.approved@example.com
- company_name: PT Approved Maju
- verification_status: approved

### Vendor Documents
Untuk vendor approved:
- legalitas
- izin_usaha

### Tender
Minimal 2 tender:
- 1 tender draft
- 1 tender open/bidding dengan timeline valid

### Participant
- vendor approved join ke tender aktif

### Bid
- vendor approved punya bid dummy

### Bid History
- 1 histori bid dummy

### Optional Result/PO
Boleh buat 1 tender result dan 1 PO jika datanya konsisten.

---

## 11. Business Rules

### Vendor Verification
- Vendor baru status pending.
- Admin dapat approve/reject vendor.
- Vendor rejected tidak boleh join tender.
- Vendor pending tidak boleh join tender.
- Hanya vendor approved yang boleh join tender.

### Tender Status
Status valid:
- draft
- open
- aanwijzing
- bidding
- closed
- finished

### Public Tender API
Tender listing/detail boleh public.

Rule:
- tender draft tidak boleh tampil di public API
- data internal admin tidak boleh tampil di public API
- response public hanya boleh berisi data yang aman untuk vendor/public

### Bidding
Vendor hanya boleh submit bid jika:
- vendor approved
- vendor sudah join tender
- waktu sekarang berada di antara bidding_start dan bidding_end

Bidding harus ditolak jika:
- belum mulai
- sudah selesai
- vendor belum approved
- vendor belum join tender

Vendor hanya boleh:
- melihat bid miliknya sendiri
- mengubah bid miliknya sendiri

Vendor tidak boleh:
- melihat bid vendor lain
- mengubah bid vendor lain

### Winner Selection
Admin hanya boleh memilih pemenang dari vendor yang:
- sudah menjadi participant
- punya bid valid

Selection method:
- lowest_price
- admin_consideration

Winner tidak boleh dipilih dua kali untuk tender yang sama.

### Purchase Order
PO hanya boleh dibuat setelah winner selected.  
PO tidak boleh duplicate untuk tender yang sama.

---

## 12. Route Count Target

Endpoint fitur utama yang diharapkan:

### Vendor Mobile API
- Lokasi: `routes/api.php`
- Jumlah: 21 endpoint

### Admin Web Routes
- Lokasi: `routes/web.php`
- Jumlah: 27 endpoint

### Total Fitur Utama
- 48 endpoint

Catatan:
- Route root `/` hanya redirect ke admin login.
- Route `/` tidak dihitung sebagai endpoint fitur utama.
- Admin disebut Admin Web Routes, bukan Admin API.

---

## 13. API Response Format

Semua Vendor API harus menggunakan format response berikut.

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

---

## 14. Frontend Styling Rule

Untuk admin web Laravel:
- Gunakan Tailwind CSS.
- Jangan pakai Bootstrap.
- Jangan pakai AdminLTE.
- Jangan pakai template dashboard berat.
- Buat UI sederhana, rapi, responsif, dan mudah dikembangkan.

Komponen minimal:
- sidebar
- navbar
- card statistik
- table
- form
- badge status
- button action
- empty state

---

## 15. Development Rules for AI Agent

Wajib:
- baca file ini sebelum mengubah kode
- baca TASK_CURRENT.md sebelum mengubah kode
- ikuti scope task yang diberikan user
- jangan membuat fitur di luar requirement
- jangan menghapus file default Laravel
- jangan membuat tabel tambahan tanpa izin
- jangan mengganti auth strategy tanpa izin
- jangan menjalankan destructive command tanpa izin

Dilarang:
- migrate:fresh tanpa izin
- install package auth tanpa izin
- membuat UI baru tanpa diminta
- mengubah nama tabel seenaknya
- mengganti workflow bisnis
- membuat fitur payment/contract/chat/review tanpa izin

Pada Phase 8:
- fokus audit
- bug fixing kecil
- security review
- cleanup
- dokumentasi sync
- demo flow check