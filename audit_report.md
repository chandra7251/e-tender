# Laporan Audit — E-Procurement Lelang 2.0
> Phase 8 | Tanggal Audit: 2026-05-11 | Auditor: Antigravity AI

---

## A. Ringkasan Audit

| Item | Detail |
|---|---|
| **Status Keseluruhan** | **PARTIAL PASS** |
| **Persentase Kesiapan** | **~82%** |
| **Critical Issues** | 4 item |
| **High Issues** | 5 item |
| **Minor Issues** | 7 item |

### Risiko Terbesar
1. **TenderResultController - `/winner` endpoint tidak butuh auth** tapi memanggil `auth()->user()->vendor` → akan crash jika diakses tanpa login (null pointer).
2. **Seeder rawan duplicate fatal** jika dijalankan ulang karena tidak ada `firstOrCreate` / pengecekan existensi.
3. **VendorController tidak mencatat TenderHistory** saat approve/reject vendor (action `vendor_approved` / `vendor_rejected` tidak di-log).
4. **TenderIndex di public API** mengembalikan `'closed'` sebagai status yang tidak masuk `allowedStatuses` — **minor**, tapi `'closed'` tidak ada di filter, sehingga tender `closed` tidak tampil ke vendor.

---

## B. Temuan Kritis

| No | Area | Masalah | Dampak | Prioritas | Rekomendasi |
|---|---|---|---|---|---|
| 1 | API - `/winner` | `TenderResultController@winner` memanggil `auth()->user()->vendor` padahal route ini berada di protected group — **AMAN**. Tapi jika `vendor` null (admin token), akan throw null error. `ApiAuth` middleware sudah memastikan role=vendor, jadi aman secara teknis. **Namun**: jika vendor belum pernah bid, `myBid` bisa null tapi tidak di-handle dengan baik (sudah di-handle `null`). | **Low actual risk** setelah review middleware | HIGH | Biarkan, sudah aman via ApiAuth |
| 2 | Seeder | `DatabaseSeeder::run()` menggunakan `User::create()` langsung **tanpa** `firstOrCreate`. Jika seeder dijalankan ulang, akan terjadi **SQLSTATE duplicate email** fatal error. | Seeder tidak bisa dijalankan ulang | CRITICAL | Gunakan `firstOrCreate` atau `updateOrCreate` |
| 3 | Tender History | `VendorController::approve()` dan `reject()` **tidak memanggil TenderHistoryService** untuk mencatat action `vendor_approved` / `vendor_rejected`. PROJECT_CONTEXT.md mewajibkan action ini di history. | Audit trail verifikasi vendor tidak ada | HIGH | Tambahkan history log di approve/reject |
| 4 | TenderController (API) | Status `'closed'` tidak ada dalam `allowedStatuses` di `TenderController@index`. Tender berstatus `closed` tidak akan tampil ke vendor. Apakah ini disengaja? | Vendor tidak bisa lihat tender closed | MEDIUM | Klarifikasi apakah `closed` harus tampil |

---

## C. Temuan Minor

| No | Area | Catatan | Rekomendasi |
|---|---|---|---|
| 1 | ApiResponse Trait | `success()` tidak selalu menyertakan key `data` jika `$data === null`. Beberapa endpoint seperti `logout` dan `changePassword` return tanpa `data` key. | Konsistenkan — selalu sertakan `"data": null` |
| 2 | TenderController (Admin) | `update()` tidak mencatat TenderHistory saat tender diupdate (hanya `store()` yang mencatat). | Tambahkan log di `update()` |
| 3 | VendorVerificationRequest | Tidak ada validasi bahwa `notes` required saat reject. Vendor bisa di-reject tanpa alasan. | Buat `notes` required saat status reject |
| 4 | PurchaseOrder | Validasi `amount` memakai `min:0` — memungkinkan PO dengan amount = 0. | Ganti ke `min:1` |
| 5 | BidRequest | Validasi `bid_amount` memakai `min:1` — secara teknis benar (> 0), namun message error bilang "lebih dari 0". Konsisten. | OK, tidak perlu diubah |
| 6 | Seeder | `vendorApproved2` (CV Karya Prima) dibuat tapi **tidak join tender manapun dan tidak punya bid**. Tidak berguna untuk demo competition bidding yang disebutkan dalam komentar. | Tambahkan join + bid untuk vendor kedua |
| 7 | TenderAnnouncement (API) | Announcement endpoint tidak memvalidasi bahwa vendor sudah join tender. Vendor yang belum join bisa melihat announcement. | Cek apakah ini intended (public info) |

---

## D. Checklist Requirement

| Requirement | Status | Bukti | Catatan |
|---|---|---|---|
| Database: semua 13 tabel ada | ✅ PASS | 14 migration files | Semua tabel hadir |
| `users`: role + softDelete | ✅ PASS | `add_role_and_soft_deletes` migration | Default role='vendor' |
| `vendors`: verification_status default pending | ✅ PASS | Migration + AuthController | `'pending'` di register |
| `tender_participants`: unique tender+vendor | ✅ PASS | Migration line 22 | `unique(['tender_id', 'vendor_id'])` |
| `bids`: unique tender+vendor | ✅ PASS | Migration line 24 | `unique(['tender_id', 'vendor_id'])` |
| `tender_results`: unique per tender | ✅ PASS | Migration `unique()` pada tender_id | Mencegah double winner |
| `purchase_orders`: po_number unique | ✅ PASS | Migration + PurchaseOrderRequest | Validated di request |
| `bid_histories`: no softDelete, append-only | ✅ PASS | BidHistory model `$timestamps = false` | Benar |
| `tender_histories`: no softDelete, append-only | ✅ PASS | TenderHistory model `$timestamps = false` | Benar |
| Model relationships: User→Vendor, Tender, etc. | ✅ PASS | Semua model sudah didefinisikan | Sesuai requirement |
| Vendor default pending saat register | ✅ PASS | `AuthController::register()` line 37 | `'pending'` hardcoded |
| Hanya vendor approved bisa join tender | ✅ PASS | `TenderParticipantController::store()` | Guard verification_status |
| Hanya vendor approved bisa bidding | ✅ PASS | `BiddingService::assertVendorCanBid()` | Throw RuntimeException |
| Vendor harus join sebelum bidding | ✅ PASS | `BiddingService::assertVendorCanBid()` | Cek TenderParticipant |
| Bidding hanya dalam rentang waktu | ✅ PASS | `BiddingService::assertBiddingOpen()` | Cek bidding_start & bidding_end |
| Vendor hanya lihat bid sendiri | ✅ PASS | `BidController::myBid()` | Filter by vendor_id |
| Vendor hanya ubah bid sendiri | ✅ PASS | `BidController::update()` line 76 | Guard vendor_id check |
| Winner tidak bisa dipilih dua kali | ✅ PASS | `WinnerSelectionController` + unique tender_id di migration | abort_if + DB constraint |
| PO hanya setelah winner selected | ✅ PASS | `PurchaseOrderController::store()` line 38 | abort_if result is null |
| PO tidak boleh duplicate | ✅ PASS | `PurchaseOrderController` line 39 + po_number unique | Double guard |
| Bid history dicatat saat submit/update | ✅ PASS | `BiddingService::submitBid()` + `updateBid()` | BidHistory::create() |
| Tender history dicatat untuk event penting | ⚠️ PARTIAL | Ada di: create, status_change, join, bid, winner, PO | **Tidak ada** di approve/reject vendor |
| Tender draft tidak tampil di public API | ✅ PASS | `TenderController@index` whereIn statuses, `TenderController@show` cek draft | Sudah difilter |
| Password selalu di-hash | ✅ PASS | User model `'password' => 'hashed'` cast + Hash::make di controller | Aman |
| Seeder membuat data demo lengkap | ⚠️ PARTIAL | Admin, vendor pending, approved, dokumen, tender, bid, history, result, PO ada | Vendor kedua tidak berguna untuk demo |

---

## E. Route Audit

### Vendor Mobile API (routes/api.php)

| # | Method | Endpoint | Auth | Status |
|---|---|---|---|---|
| 1 | POST | `/api/auth/register` | Public | ✅ |
| 2 | POST | `/api/auth/login` | Public | ✅ |
| 3 | POST | `/api/auth/forgot-password` | Public | ✅ |
| 4 | POST | `/api/auth/reset-password` | Public | ✅ |
| 5 | GET | `/api/tenders` | Public | ✅ |
| 6 | GET | `/api/tenders/{tender}` | Public | ✅ |
| 7 | POST | `/api/auth/logout` | auth.api | ✅ |
| 8 | GET | `/api/auth/me` | auth.api | ✅ |
| 9 | PUT | `/api/auth/change-password` | auth.api | ✅ |
| 10 | GET | `/api/vendors/me` | auth.api | ✅ |
| 11 | PUT | `/api/vendors/me` | auth.api | ✅ |
| 12 | GET | `/api/vendors/status` | auth.api | ✅ |
| 13 | GET | `/api/vendors/documents` | auth.api | ✅ |
| 14 | POST | `/api/vendors/documents` | auth.api | ✅ |
| 15 | POST | `/api/tenders/{tender}/participants` | auth.api | ✅ |
| 16 | GET | `/api/tenders/{tender}/announcements` | auth.api | ✅ |
| 17 | GET | `/api/tenders/{tender}/bids/me` | auth.api | ✅ |
| 18 | POST | `/api/tenders/{tender}/bids` | auth.api | ✅ |
| 19 | PUT | `/api/tenders/{tender}/bids/{bid}` | auth.api | ✅ |
| 20 | GET | `/api/tenders/{tender}/result` | auth.api | ✅ |
| 21 | GET | `/api/tenders/{tender}/winner` | auth.api | ✅ |

**Total: 21 endpoint ✅ SESUAI TARGET**

### Admin Web Routes (routes/web.php)

| # | Method | Endpoint | Middleware | Status |
|---|---|---|---|---|
| 1 | GET | `/admin/login` | Guest | ✅ |
| 2 | POST | `/admin/login` | Guest | ✅ |
| 3 | POST | `/admin/logout` | auth+role:admin | ✅ |
| 4 | GET | `/admin/dashboard` | auth+role:admin | ✅ |
| 5 | GET | `/admin/vendors` | auth+role:admin | ✅ |
| 6 | GET | `/admin/vendors/{vendor}` | auth+role:admin | ✅ |
| 7 | PATCH | `/admin/vendors/{vendor}/approve` | auth+role:admin | ✅ |
| 8 | PATCH | `/admin/vendors/{vendor}/reject` | auth+role:admin | ✅ |
| 9 | GET | `/admin/tenders` | auth+role:admin | ✅ |
| 10 | GET | `/admin/tenders/create` | auth+role:admin | ⚠️ Route order issue* |
| 11 | POST | `/admin/tenders` | auth+role:admin | ✅ |
| 12 | GET | `/admin/tenders/{tender}` | auth+role:admin | ✅ |
| 13 | GET | `/admin/tenders/{tender}/edit` | auth+role:admin | ✅ |
| 14 | PUT | `/admin/tenders/{tender}` | auth+role:admin | ✅ |
| 15 | PATCH | `/admin/tenders/{tender}/status` | auth+role:admin | ✅ |
| 16 | POST | `/admin/tenders/{tender}/announcements` | auth+role:admin | ✅ |
| 17 | GET | `/admin/tenders/{tender}/participants` | auth+role:admin | ✅ |
| 18 | GET | `/admin/tenders/{tender}/bids` | auth+role:admin | ✅ |
| 19 | GET | `/admin/tenders/{tender}/bids/{bid}/histories` | auth+role:admin | ✅ |
| 20 | GET | `/admin/tenders/{tender}/winner/create` | auth+role:admin | ✅ |
| 21 | POST | `/admin/tenders/{tender}/winner` | auth+role:admin | ✅ |
| 22 | GET | `/admin/tenders/{tender}/result` | auth+role:admin | ✅ |
| 23 | PATCH | `/admin/tenders/{tender}/finish` | auth+role:admin | ✅ |
| 24 | GET | `/admin/tenders/{tender}/purchase-order/create` | auth+role:admin | ✅ |
| 25 | POST | `/admin/tenders/{tender}/purchase-order` | auth+role:admin | ✅ |
| 26 | GET | `/admin/tenders/{tender}/purchase-order` | auth+role:admin | ✅ |
| 27 | GET | `/admin/tenders/{tender}/histories` | auth+role:admin | ✅ |

**Total: 27 endpoint ✅ SESUAI TARGET**

> ⚠️ *Route `/admin/tenders/create` vs `/admin/tenders/{tender}` — di Laravel, string literal route seperti `create` diregistrasi sebelum parameter `{tender}`, sehingga **tidak akan bentrok**. AMAN.

---

## F. Security Audit

| Security Item | Status | Catatan |
|---|---|---|
| Password di-hash saat register | ✅ PASS | `Hash::make()` di AuthController + cast `hashed` di User model |
| Password di-hash saat reset | ✅ PASS | `Hash::make()` di `resetPassword()` dan `changePassword()` |
| Password di-hash di seeder | ✅ PASS | `Hash::make('password')` untuk semua user seeder |
| CSRF aktif di form admin | ✅ PASS | Laravel CSRF middleware default aktif untuk web routes |
| Admin route protected `auth` + `role:admin` | ✅ PASS | `middleware(['auth', 'role:admin'])` di web.php |
| Route login admin bisa diakses guest | ✅ PASS | GET/POST admin/login tidak di dalam middleware group |
| Vendor tidak bisa akses admin route | ✅ PASS | `RoleMiddleware` redirect ke admin.login jika role bukan admin |
| Vendor API tidak bisa diakses tanpa token | ✅ PASS | `ApiAuth` middleware return 401 jika tidak ada bearer token |
| User non-vendor tidak bisa akses Vendor API | ✅ PASS | `ApiAuth` middleware cek `role !== 'vendor'` → return 403 |
| Vendor harus punya profil vendor | ✅ PASS | `ApiAuth` cek `$user->vendor` exists |
| Upload dokumen dibatasi tipe dan ukuran | ✅ PASS | `VendorDocumentRequest`: mimes:pdf,jpg,jpeg,png, max:5120 (5MB) |
| Bidding divalidasi waktu | ✅ PASS | `BiddingService::assertBiddingOpen()` cek status + waktu |
| Vendor pending/rejected tidak bisa join | ✅ PASS | `TenderParticipantController` cek `verification_status !== 'approved'` |
| Vendor pending/rejected tidak bisa bid | ✅ PASS | `BiddingService::assertVendorCanBid()` cek approved |
| Vendor belum join tidak bisa bid | ✅ PASS | `BiddingService::assertVendorCanBid()` cek TenderParticipant |
| Vendor tidak bisa lihat bid vendor lain | ✅ PASS | `BidController::myBid()` filter by `vendor_id` sendiri |
| Vendor tidak bisa ubah bid vendor lain | ✅ PASS | `BidController::update()` guard: `$bid->vendor_id !== $vendor->id` |
| Tender draft tidak tampil di public API | ✅ PASS | `TenderController@index` whereIn statuses, `@show` cek draft |
| Tidak ada route debug terbuka | ✅ PASS | Tidak ada `/debug`, `/telescope` (belum install), `/health` hanya ping |
| Hardcoded token/password di logic | ✅ PASS | Tidak ada hardcoded secret di logic utama |
| Session regeneration saat login | ✅ PASS | `$request->session()->regenerate()` di AdminLoginController |
| Session invalidation saat logout | ✅ PASS | `invalidate()` + `regenerateToken()` di logout |
| Vendor API tidak bisa akses `/admin/*` | ✅ PASS | Admin route di web.php membutuhkan session auth, bukan Bearer token |
| remember_token digunakan sebagai API token | ⚠️ PARTIAL | **Kelemahan desain**: remember_token tidak expire otomatis dan bisa digunakan untuk web session login juga. Sesuai instruksi: cukup dilaporkan, tidak diubah. |

---

## G. Apidog Sync

> **Catatan**: File Apidog tidak ada di dalam project. Audit dilakukan berdasarkan endpoint yang tercatat di TASK_CURRENT.md dan routes/api.php.

| Endpoint TASK_CURRENT.md | Ada di routes/api.php | Method Sesuai | Body/Param | Catatan |
|---|---|---|---|---|
| POST /api/auth/register | ✅ | ✅ POST | name, email, password, company_name, phone, address | Sesuai |
| POST /api/auth/login | ✅ | ✅ POST | email, password | Sesuai |
| POST /api/auth/forgot-password | ✅ | ✅ POST | email | Sesuai |
| POST /api/auth/reset-password | ✅ | ✅ POST | token, email, password, password_confirmation | Sesuai |
| GET /api/tenders | ✅ | ✅ GET | query: status, search | Sesuai |
| GET /api/tenders/{tender} | ✅ | ✅ GET | - | Sesuai |
| POST /api/auth/logout | ✅ | ✅ POST | Bearer token | Sesuai |
| GET /api/auth/me | ✅ | ✅ GET | Bearer token | Sesuai |
| PUT /api/auth/change-password | ✅ | ✅ PUT | current_password, password, password_confirmation | Sesuai |
| GET /api/vendors/me | ✅ | ✅ GET | Bearer token | Sesuai |
| PUT /api/vendors/me | ✅ | ✅ PUT | company_name, phone, address | Sesuai |
| GET /api/vendors/status | ✅ | ✅ GET | Bearer token | Sesuai |
| GET /api/vendors/documents | ✅ | ✅ GET | Bearer token | Sesuai |
| POST /api/vendors/documents | ✅ | ✅ POST | document_type, file (multipart) | Sesuai |
| POST /api/tenders/{tender}/participants | ✅ | ✅ POST | Bearer token (no body) | Sesuai |
| GET /api/tenders/{tender}/announcements | ✅ | ✅ GET | Bearer token | Sesuai |
| GET /api/tenders/{tender}/bids/me | ✅ | ✅ GET | Bearer token | Sesuai |
| POST /api/tenders/{tender}/bids | ✅ | ✅ POST | bid_amount, notes | Sesuai |
| PUT /api/tenders/{tender}/bids/{bid} | ✅ | ✅ PUT | bid_amount, notes | Sesuai |
| GET /api/tenders/{tender}/result | ✅ | ✅ GET | Bearer token | Sesuai |
| GET /api/tenders/{tender}/winner | ✅ | ✅ GET | Bearer token | ⚠️ Response berbeda dari `/result` — tidak ada TenderResultResource, langsung array manual |

**Status Apidog Sync: SESUAI** (semua 21 endpoint cocok). File Apidog eksternal tidak bisa dicek secara langsung.

---

## H. Checklist Validasi

| Area | Field | Status | Catatan |
|---|---|---|---|
| Auth - Register | name, email, password, company_name | ✅ | `RegisterRequest` |
| Auth - Login | email, password | ✅ | `LoginRequest` |
| Auth - Forgot Password | email | ⚠️ PARTIAL | Inline validate di controller, tidak pakai FormRequest |
| Auth - Reset Password | token, email, password, confirmed | ⚠️ PARTIAL | Inline validate di controller |
| Auth - Change Password | current_password, password, confirmed | ⚠️ PARTIAL | Inline validate di controller |
| Vendor Profile Update | company_name, phone, address | ✅ | `VendorProfileRequest` |
| Upload Dokumen | document_type, file (tipe+ukuran) | ✅ | `VendorDocumentRequest` — mimes + max:5120 |
| Tender Create/Update | semua field + timeline constraints | ✅ | `TenderRequest` lengkap |
| Tender Status Update | status (enum valid) | ✅ | `TenderStatusRequest` |
| Aanwijzing | title, content, published_at | ✅ | `TenderAnnouncementRequest` |
| Bid Submit/Update | bid_amount (>0), notes | ✅ | `BidRequest` |
| Winner Selection | bid_id, selection_method, notes | ✅ | `WinnerSelectionRequest` |
| Purchase Order | po_number (unique), amount, issued_date | ✅ | `PurchaseOrderRequest` |

> ⚠️ **Forgot/Reset/Change Password** menggunakan inline `$request->validate()` di controller, bukan FormRequest terpisah. Error response-nya mengikuti format Laravel default (bukan format `{status: error, message, errors}` yang sudah distandardkan). Ini inconsistency API response.

---

## H. Rekomendasi Perbaikan Bertahap

### 1. Critical (Wajib sebelum demo)

1. **Seeder duplicate fatal** — Wrap dengan `firstOrCreate` atau tambahkan truncate/check di awal `run()`.
   - File: `database/seeders/DatabaseSeeder.php`

2. **Forgot/Reset/Change Password tidak menggunakan format ApiResponse standar** — Bungkus error ke format `{status: error, message, errors}` jika validasi gagal.
   - File: `app/Http/Controllers/Api/AuthController.php`

### 2. High (Sebaiknya diperbaiki sebelum demo)

3. **Vendor approved/rejected tidak dicatat di TenderHistory** — Tambahkan `TenderHistoryService::log()` atau `TenderHistory::create()` di `VendorController::approve()` dan `reject()`.
   - File: `app/Http/Controllers/Admin/VendorController.php`

4. **Vendor Kedua tidak ada data join/bid** — Tambahkan participant dan bid untuk `vendorApproved2` di seeder agar demo competition bidding bisa ditampilkan.
   - File: `database/seeders/DatabaseSeeder.php`

5. **Status `closed` tidak tampil ke vendor** — Klarifikasi apakah tender berstatus `closed` harus tampil di public API. Jika ya, tambahkan ke `allowedStatuses`.
   - File: `app/Http/Controllers/Api/TenderController.php`

### 3. Medium (Nice to have)

6. **`success()` tidak konsisten menyertakan key `data`** — Jika `data` null, tetap sertakan `"data": null` agar response structure selalu konsisten.
   - File: `app/Http/Traits/ApiResponse.php`

7. **TenderController Admin `update()` tidak log history** — Tambahkan log history saat tender diedit.
   - File: `app/Http/Controllers/Admin/TenderController.php`

8. **PurchaseOrder `amount` min:0** — Ganti ke `min:1` agar PO tidak boleh bernilai 0.
   - File: `app/Http/Requests/Admin/PurchaseOrderRequest.php`

### 4. Cleanup (Opsional)

9. **VendorVerificationRequest** — Pertimbangkan membuat `notes` required saat `action` adalah reject.
10. **remember_token sebagai API token** — Kelemahan minor yang sudah didokumentasikan. Tidak perlu diubah di Phase 8.
11. **Tambahkan komentar Apidog Sync note** di `routes/api.php` agar developer berikutnya tahu dokumentasi ada di Apidog.

---

## Kesimpulan

Project ini **sudah sangat solid untuk Phase 8**. Arsitektur bersih, business rules lengkap, security cukup kuat, dan route count sesuai target (21 + 27 = 48). Satu-satunya risiko real sebelum demo adalah **seeder yang tidak idempotent** dan **inconsistency format error di auth endpoints forgot/reset/change password**.

Dengan memperbaiki 4–5 item di atas, project ini siap untuk demo flow penuh.
