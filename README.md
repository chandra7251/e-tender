# ZETA E-Procurement v2.0

> Sistem Pengadaan Barang & Jasa Digital Terintegrasi
> Laravel 13 · JWT Auth · REST API · Admin Panel

---

## Tech Stack

| Layer        | Teknologi                                |
|--------------|------------------------------------------|
| Backend      | Laravel 13 (PHP 8.3)                     |
| Auth API     | JWT (tymon/jwt-auth)                     |
| Database     | MySQL 8.x                                |
| PDF Export   | barryvdh/laravel-dompdf                  |
| Push Notif   | FCM (Firebase Cloud Messaging)           |
| Queue        | Database Driver                          |
| Admin UI     | Blade + Tailwind CSS CDN + Alpine.js     |
| Mobile API   | RESTful JSON API (Capacitor/Flutter)     |

---

## Fitur Utama

### Admin Panel (`/admin`)
| Menu              | Deskripsi                                                       |
|-------------------|-----------------------------------------------------------------|
| Dashboard         | Statistik real-time: tender, vendor, penawaran, kontrak         |
| Vendor            | Manajemen vendor, approval/reject, dokumen, rating              |
| Pengajuan Vendor  | Antrian registrasi + badge notif pending                        |
| Blacklist         | Daftar hitam vendor                                             |
| Tender            | CRUD tender, status, foto, Bill of Quantity (BQ)               |
| Evaluasi          | Kriteria, scoring, metode 2-amplop, ranking                     |
| Pemenang          | Seleksi pemenang, hasil tender, BAST                            |
| **Sanggahan**     | Manajemen sanggahan & banding vendor                            |
| **Kontrak**       | Kontrak digital, TTD elektronik, milestone monitoring           |
| Audit Log         | Log aktivitas seluruh pengguna                                  |
| Laporan           | Export laporan tender & vendor (PDF/Excel)                      |
| **Pengaturan**    | White-label instansi: logo, warna, nama, header/footer dokumen  |

### REST API (Mobile)
| Grup          | Endpoint Utama                                                     |
|---------------|--------------------------------------------------------------------||
| Auth          | Register, Login, Forgot/Reset Password, Verify Email, 2FA          |
| Vendor        | Profil, dokumen, kualifikasi, sertifikasi, rating, status          |
| Tender        | List, detail, join, pengumuman, hasil, **BQ items**                |
| Penawaran     | Submit bid, update, riwayat, **BidItems per item BQ**              |
| Notifikasi    | FCM token, inbox notifikasi                                        |
| **Sanggahan** | Ajukan sanggahan/banding, cek status                               |
| **Kontrak**   | Lihat kontrak, TTD vendor, progress milestone                      |
| Admin API     | Dashboard stats, ranking, export PDF, webhook, settings, BQ        |

---

## Setup Lokal

### 1. Clone & Install
```bash
git clone <repo-url> lelang-2.0
cd lelang-2.0
composer install
cp .env.example .env
```

### 2. Konfigurasi `.env`
```env
APP_NAME=ZETA
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_DATABASE=lelang_2
DB_USERNAME=root
DB_PASSWORD=

JWT_SECRET=   # generate: php artisan jwt:secret
FCM_PROJECT_ID=   # Firebase Project ID untuk push notif
MAIL_FROM_ADDRESS=noreply@zeta.co.id
MAIL_FROM_NAME="ZETA E-Procurement"
```

### 3. Migrate & Seed
```bash
php artisan key:generate
php artisan jwt:secret
php artisan migrate:fresh --seed
php artisan storage:link
```

### 4. Jalankan
```bash
# Development (tanpa queue)
php artisan serve

# Production-like (dengan queue worker)
php artisan serve &
php artisan queue:work --queue=default
```

---

## Default Login Admin

| Field    | Value                                       |
|----------|---------------------------------------------|
| URL      | `http://localhost:8000/admin/login`         |
| Email    | `admin@vandrafcy.my.id`                     |
| Password | `rahasia`                                   |

---

## API Base URL & Auth

```
Base URL: http://<HOST>:8000/api
```

Header wajib untuk endpoint protected:
```
Authorization: Bearer <JWT_TOKEN>
Content-Type: application/json
Accept: application/json
```

### Standard Response Format
```json
{ "status": true, "message": "OK", "data": { ... } }
{ "status": false, "message": "Error message", "errors": { ... } }
```

---

## Database Schema (28 tabel)

```
users                   vendors                 vendor_documents
vendor_submissions      vendor_submission_photos vendor_qualifications
vendor_certifications   vendor_ratings
tenders                 tender_photos           tender_requirements
tender_announcements    tender_participants      tender_histories
tender_evaluation_criteria tender_items         tender_complaints
tender_results          bids                    bid_histories
bid_evaluations         bid_items               purchase_orders
contracts               contract_deliveries      instansi_settings
activity_logs           webhook_subscriptions
```

---

## Fitur Advanced

- **Two-Envelope Evaluation** — Evaluasi 2 amplop (teknis + harga) Perpres 16/2018
- **Digital Contract** — Hash SHA-256 & QR Code verifikasi keaslian
- **Bill of Quantity (BQ)** — Rincian item tender + penawaran per item (BidItem)
- **Sanggahan & Banding** — Mekanisme keberatan vendor sesuai regulasi
- **Webhook System** — Event-driven integration ke sistem eksternal
- **FCM Push Notification** — Notifikasi real-time ke mobile app
- **PDF Export** — Rekap tender, BA Evaluasi, BA Pemenang, Kontrak
- **White-Label** — Branding instansi: logo, warna, nama, header/footer PDF
- **Audit Log** — Jejak aktivitas seluruh pengguna admin & vendor
- **2FA Support** — Two-factor authentication (toggle via pengaturan)

---

## Changelog

### v2.0.0 (Jun 2026)
- Tambah: Bill of Quantity / Tender Items + BidItem per baris
- Tambah: Sanggahan & Banding vendor
- Tambah: Kontrak Digital + TTD Elektronik + Milestone
- Tambah: Vendor Kualifikasi & Sertifikasi
- Tambah: Two-Envelope Evaluation
- Tambah: White-Label / Instansi Settings
- Tambah: Webhook Subscriptions
- Tambah: FCM Push Notification
- Tambah: Export PDF (Rekap, BA Evaluasi, BA Pemenang, Kontrak)
- Tambah: Vendor Rating & Blacklist
- Tambah: Audit Log System

### v1.0.0 (v `lelang-priority2`)
- Core tender & bidding system
- Vendor management & document upload
- Admin panel dasar
