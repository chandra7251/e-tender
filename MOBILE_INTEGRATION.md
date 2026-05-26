# 📱 Mobile Developer Integration Report
**Tanggal:** 26 Mei 2026  
**Dari:** Backend Team  
**Untuk:** Mobile Developer (Ionic / Angular / Flutter)  
**Base URL Dev:** `http://127.0.0.1:8080/api`

---

## Bagian 1 — Permintaanmu di `backend_requirements.md` Sudah Selesai ✅

Semua 4 endpoint yang kamu request sudah live. Tinggal sambungkan dari sisi mobile.

---

### ✅ 1. `GET /api/tenders/{tender}/participants/check`

**Auth:** Bearer Token (semua vendor login bisa akses, termasuk pending)

**Response:**
```json
{
  "status": "success",
  "message": "OK",
  "data": {
    "is_participant": true,
    "joined_at": "2026-05-16T10:30:00+07:00"
  }
}
```

**Implementasi mobile:**
```typescript
// Ganti workaround lama (cek via bids/me) dengan ini:
checkParticipation(tenderId: number): Observable<boolean> {
  return this.http.get<ApiResponse>(`/tenders/${tenderId}/participants/check`).pipe(
    map(res => res.data.is_participant)
  );
}
```

---

### ✅ 2. `GET /api/vendors/tenders`

**Auth:** Bearer Token

**Response:**
```json
{
  "status": "success",
  "message": "OK",
  "data": [
    {
      "id": 1,
      "title": "Pengadaan Laptop 2026",
      "status": "bidding",
      "start_date": "2026-05-14T...",
      "end_date": "2026-06-13T...",
      "bidding_start": "2026-05-23T...",
      "bidding_end": "2026-06-02T...",
      "aanwijzing_date": "2026-05-19T...",
      "is_participant": true,
      "joined_at": "2026-05-16T..."
    }
  ]
}
```

**Implementasi mobile:**
```typescript
getMyTenders(): Observable<Tender[]> {
  return this.http.get<ApiResponse>('/vendors/tenders').pipe(
    map(res => res.data)
  );
}
```

---

### ✅ 3. `GET /api/vendors/results`

**Auth:** Bearer Token

**Response:**
```json
{
  "status": "success",
  "message": "OK",
  "data": [
    {
      "tender_id": 2,
      "tender_title": "Pengadaan Komputer IT 2026",
      "tender_status": "finished",
      "is_winner": false,
      "my_bid_amount": 97000000,
      "winner_company": "PT Approved Maju",
      "winning_bid_amount": 92500000,
      "decided_at": "2026-05-18T..."
    }
  ]
}
```

**Implementasi mobile:**
```typescript
// Update ResultHistoryPage — ganti filter manual dengan endpoint ini:
getMyResults(): Observable<VendorResult[]> {
  return this.http.get<ApiResponse>('/vendors/results').pipe(
    map(res => res.data)
  );
}

// Di halaman Hasil:
// ❌ Ganti: this.tenderService.getTenders().pipe(filter(t => t.status === 'finished'))
// ✅ Dengan: this.vendorService.getMyResults()
```

---

### ✅ 4. Field `is_participant` di `GET /api/tenders/{tender}`

Field `is_participant` sudah tersedia di setiap response detail tender.  
Jika request **tanpa token** (guest), nilainya selalu `false`.

**Implementasi mobile:**
```typescript
// Tidak perlu request tambahan — baca langsung dari response tender detail:
this.tenderService.getTenderDetail(id).subscribe(res => {
  this.tender = res.data;
  this.isParticipant = res.data.is_participant; // langsung tersedia
});
```

---

---

## Bagian 2 — Perubahan di Luar `backend_requirements.md` (Aksi Diperlukan)

---

### 🆕 A. Endpoint Baru: `POST /api/auth/refresh` *(Fix "Harus Login Terus")*

**Auth:** Bearer Token (kirim token yang akan/sudah expired)  
**Throttle:** Maks 10 request/menit

**Request:**
```http
POST /api/auth/refresh
Authorization: Bearer {token_lama}
```

**Response:**
```json
{
  "status": "success",
  "message": "Token berhasil diperbarui.",
  "data": {
    "token": "eyJ0eXAiOiJKV1QiLCJhbGci...",
    "token_type": "bearer",
    "expires_in": 3600
  }
}
```

**Implementasi mobile — auto-refresh interceptor:**
```typescript
// auth.interceptor.ts
intercept(req: HttpRequest<any>, next: HttpHandler): Observable<HttpEvent<any>> {
  return next.handle(req).pipe(
    catchError(error => {
      // Jika 401 dan bukan dari endpoint refresh itu sendiri
      if (error.status === 401 && !req.url.includes('/auth/refresh')) {
        return this.authService.refreshToken().pipe(
          switchMap(newToken => {
            const retryReq = req.clone({
              setHeaders: { Authorization: `Bearer ${newToken}` }
            });
            return next.handle(retryReq);
          }),
          catchError(() => {
            // Refresh gagal → paksa logout
            this.authService.logout();
            return throwError(() => error);
          })
        );
      }
      return throwError(() => error);
    })
  );
}

// auth.service.ts
refreshToken(): Observable<string> {
  return this.http.post<ApiResponse>('/auth/refresh', {}, {
    headers: { Authorization: `Bearer ${this.getToken()}` }
  }).pipe(
    map(res => {
      const newToken = res.data.token;
      this.saveToken(newToken);
      return newToken;
    })
  );
}
```

---

### ⚠️ B. Rate Limiting di Auth Endpoints (HTTP 429)

Backend sekarang membatasi request ke endpoint auth. Jika terlalu banyak request dalam 1 menit, response **HTTP 429**.

| Endpoint | Batas |
|---|---|
| `POST /api/auth/login` | 5 request/menit |
| `POST /api/auth/register` | 10 request/menit |
| `POST /api/auth/forgot-password` | 3 request/menit |
| `POST /api/auth/refresh` | 10 request/menit |

**Response 429:**
```json
{
  "message": "Too Many Attempts."
}
```

**Implementasi mobile:**
```typescript
// Tambah di error interceptor:
if (error.status === 429) {
  this.toastService.show('Terlalu banyak percobaan. Tunggu beberapa saat dan coba lagi.');
}
```

---

### ⚠️ C. File Dokumen Tidak Punya URL Publik

File dokumen vendor sekarang disimpan di **disk private** — tidak bisa diakses via URL langsung.

```typescript
// ❌ Tidak berlaku lagi:
const fileUrl = `${BASE_URL}/storage/vendor-documents/1/akta.pdf`;

// ✅ Gunakan endpoint download dengan token:
// GET /api/vendors/documents/{document_id}/download
```

**Endpoint download:**
```http
GET /api/vendors/documents/{document_id}/download
Authorization: Bearer {token}
```

Response: stream binary file (PDF/JPG/PNG) + header `Content-Disposition: attachment`.

**Implementasi mobile:**
```typescript
downloadDocument(docId: number): Observable<Blob> {
  return this.http.get(`/vendors/documents/${docId}/download`, {
    headers: { Authorization: `Bearer ${this.getToken()}` },
    responseType: 'blob'
  });
}

// Kemudian simpan ke device atau buka di viewer:
this.vendorService.downloadDocument(docId).subscribe(blob => {
  const url = URL.createObjectURL(blob);
  window.open(url); // atau gunakan Capacitor Filesystem
});
```

---

### ✅ D. Error Server Sudah Konsisten JSON

Semua error dari `api/*` termasuk error 500 sekarang selalu return JSON (tidak ada lagi HTML error page):

```json
{
  "status": false,
  "message": "Terjadi kesalahan server. Silakan coba beberapa saat lagi.",
  "data": null
}
```

> Tidak ada aksi khusus diperlukan. Ini perbaikan, bukan breaking change.

---

---

## ⚠️ Koreksi dari `backend_requirements.md`

Dokumen `backend_requirements.md` yang sebelumnya ditulis memiliki **dua ketidaksesuaian** dengan implementasi aktual:

---

### Koreksi 1 — Format Field `status` (PENTING)

| | Di `backend_requirements.md` | Implementasi Aktual |
|---|---|---|
| **Tipe** | `boolean` (`true`/`false`) | `string` (`"success"`/`"error"`) |
| **Contoh success** | `"status": true` | `"status": "success"` |
| **Contoh error** | `"status": false` | `"status": "error"` |

**Sesuaikan parsing di mobile:**
```typescript
// ❌ Parsing lama (dari backend_requirements.md):
if (response.status === true) { ... }

// ✅ Parsing yang benar:
if (response.status === 'success') { ... }

// Atau definisikan interface/type yang benar:
interface ApiResponse<T> {
  status: 'success' | 'error';  // bukan boolean
  message: string;
  data: T | null;
  errors?: any;
}
```

---

### Koreksi 2 — Auth Middleware

| | Di `backend_requirements.md` | Implementasi Aktual |
|---|---|---|
| **Middleware** | `auth:sanctum` | `auth:api` (JWT via tymon/jwt-auth) |

> Dari sisi mobile **tidak ada perubahan** — cara kirim token tetap sama: `Authorization: Bearer {token}`. Koreksi ini hanya relevan jika kamu membaca source code backend.

---

---

## Ringkasan Checklist untuk Mobile Developer

| # | Item | Prioritas | Aksi |
|---|---|---|---|
| 1 | Implementasi `/participants/check` | 🔴 Tinggi | Ganti workaround lama |
| 2 | Implementasi `/vendors/tenders` | 🟡 Sedang | Buat service method |
| 3 | Update halaman Hasil via `/vendors/results` | 🟡 Sedang | Ganti filter manual |
| 4 | Baca `is_participant` dari TenderResource | 🟡 Sedang | Kurangi 1 HTTP request |
| 5 | **Implementasi auto-refresh token** | 🔴 **Tinggi** | Fix "harus login terus" |
| 6 | Handle HTTP **429** di error interceptor | 🟡 Sedang | Tampilkan pesan ke user |
| 7 | Hapus asumsi file punya URL publik | 🟡 Sedang | Gunakan endpoint download |
| 8 | **Fix parsing `status`** (`"success"`/`"error"` bukan `true`/`false`) | 🔴 **Tinggi** | Update interface & semua kondisi |

---

## Akun Demo untuk Testing

| Akun | Password | Keterangan |
|---|---|---|
| `vendor.approved@example.com` | `password` | Vendor approved, sudah join tender bidding & finished |
| `vendor.kedua@example.com` | `password` | Vendor approved kedua |
| `vendor.pending@example.com` | `password` | Vendor pending — untuk test 403 |
| `vendor.rejected@example.com` | `password` | Vendor rejected — untuk test 403 |
| `admin@example.com` | `password` | Admin (web only, bukan API) |
