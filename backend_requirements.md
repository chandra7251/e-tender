# Backend Requirements — Mobile App Feature Gaps

**Tanggal:** 24 Mei 2026  
**Dibuat oleh:** Analisis mobile app vs backend  
**Tujuan:** Endpoint-endpoint ini dibutuhkan mobile app tapi belum ada di backend Laravel

---

## Ringkasan Endpoint yang Dibutuhkan

| # | Method | Endpoint | Prioritas | Keterangan |
|---|--------|----------|-----------|------------|
| 1 | GET | `/api/tenders/{tender}/participants/check` | 🔴 Tinggi | Cek apakah vendor yang login adalah peserta tender ini |
| 2 | GET | `/api/vendors/tenders` | 🟡 Sedang | List tender yang diikuti vendor yang login |
| 3 | GET | `/api/vendors/results` | 🟡 Sedang | List hasil semua tender yang diikuti vendor |
| 4 | field | `is_participant` di `GET /api/tenders/{tender}` | 🟡 Sedang | Tambah field is_participant ke TenderResource |

---

## Detail Setiap Endpoint

---

### 1. 🔴 GET `/api/tenders/{tender}/participants/check`

**Kenapa dibutuhkan:**  
Mobile app perlu tahu apakah vendor yang sedang login sudah terdaftar sebagai peserta tender, untuk:
- Menyembunyikan tombol "Ikuti Tender" jika sudah join
- Menampilkan pesan "Anda sudah terdaftar" di halaman detail tender
- Menentukan akses ke fitur aanwijzing/bidding

**Workaround saat ini (tidak ideal):**  
Mobile app memanggil `GET /api/tenders/{id}/bids/me` dan mendeteksi dari HTTP status code:
- 200 → dianggap peserta
- 403 → bukan peserta
- 404 → dianggap peserta (belum bid)

Ini tidak akurat karena vendor bisa jadi peserta tapi belum bid (404 bukan berarti bukan peserta).

**Response yang diharapkan:**
```json
// HTTP 200
{
  "status": true,
  "message": "OK",
  "data": {
    "is_participant": true,
    "joined_at": "2025-01-15T10:30:00Z"
  }
}
```

**Implementasi Laravel:**
```php
// routes/api.php
Route::get('/tenders/{tender}/participants/check', [TenderParticipantController::class, 'check']);

// TenderParticipantController.php
public function check(Tender $tender): JsonResponse
{
    $vendor = auth()->user()->vendor;
    
    $participant = $tender->participants()
        ->where('vendor_id', $vendor->id)
        ->first();
    
    return $this->success([
        'is_participant' => !is_null($participant),
        'joined_at' => $participant?->created_at,
    ]);
}
```

**Middleware:** `auth:sanctum` (harus login)

---

### 2. 🟡 GET `/api/vendors/tenders`

**Kenapa dibutuhkan:**  
Vendor ingin melihat daftar tender yang sudah mereka ikuti (join sebagai peserta). Ini berbeda dari `GET /api/tenders` yang menampilkan SEMUA tender.

**Response yang diharapkan:**
```json
// HTTP 200
{
  "status": true,
  "message": "OK",
  "data": [
    {
      "id": 1,
      "title": "Pengadaan Komputer 2025",
      "status": "bidding",
      "start_date": "2025-01-01",
      "end_date": "2025-03-31",
      "bidding_start": "2025-02-01",
      "bidding_end": "2025-02-28",
      "aanwijzing_date": "2025-01-20",
      "joined_at": "2025-01-10T09:00:00Z"
    }
  ]
}
```

**Implementasi Laravel:**
```php
// routes/api.php
Route::get('/vendors/tenders', [VendorController::class, 'myTenders']);

// VendorController.php
public function myTenders(): JsonResponse
{
    $vendor = auth()->user()->vendor;
    
    $tenders = $vendor->tenders()  // melalui relasi participants
        ->where('status', '!=', 'draft')
        ->orderBy('created_at', 'desc')
        ->get();
    
    return $this->success(TenderResource::collection($tenders));
}
```

**Middleware:** `auth:sanctum`

---

### 3. 🟡 GET `/api/vendors/results`

**Kenapa dibutuhkan:**  
Tab "Hasil" di mobile app ingin menampilkan semua hasil tender yang vendor ikuti, tidak hanya tender yang masih aktif.

**Workaround saat ini (tidak ideal):**  
Mobile app memanggil `GET /api/tenders` lalu mem-filter `status === 'closed' || status === 'finished'` di sisi client. Kelemahannya:
- Menampilkan SEMUA tender closed/finished, bukan hanya yang vendor ikuti
- Vendor bisa melihat tender yang tidak pernah ia join

**Response yang diharapkan:**
```json
// HTTP 200
{
  "status": true,
  "message": "OK",
  "data": [
    {
      "tender_id": 1,
      "tender_title": "Pengadaan Komputer 2025",
      "tender_status": "finished",
      "is_winner": false,
      "my_bid_amount": 45000000,
      "winner_company": "PT Maju Jaya",
      "winning_bid_amount": 42000000,
      "decided_at": "2025-03-15T14:00:00Z"
    }
  ]
}
```

**Implementasi Laravel:**
```php
// routes/api.php
Route::get('/vendors/results', [VendorController::class, 'myResults']);

// VendorController.php
public function myResults(): JsonResponse
{
    $vendor = auth()->user()->vendor;
    
    // Ambil tender yang vendor ikuti dan sudah closed/finished
    $results = $vendor->tenders()
        ->whereIn('status', ['closed', 'finished'])
        ->with(['tenderResult', 'bids' => fn($q) => $q->where('vendor_id', $vendor->id)])
        ->get()
        ->map(function ($tender) use ($vendor) {
            $myBid = $tender->bids->first();
            $result = $tender->tenderResult;
            
            return [
                'tender_id'          => $tender->id,
                'tender_title'       => $tender->title,
                'tender_status'      => $tender->status,
                'is_winner'          => $result?->winner_vendor_id === $vendor->id,
                'my_bid_amount'      => $myBid?->bid_amount,
                'winner_company'     => $result?->winnerVendor?->company_name,
                'winning_bid_amount' => $result?->winning_bid_amount,
                'decided_at'         => $result?->decided_at,
            ];
        });
    
    return $this->success($results);
}
```

**Middleware:** `auth:sanctum`

---

### 4. 🟡 Tambah `is_participant` ke TenderResource

**Kenapa dibutuhkan:**  
Alternatif yang lebih efisien dari endpoint `/participants/check` — cukup sertakan field `is_participant` di response `GET /api/tenders/{tender}`, sehingga mobile tidak perlu 2 request.

**Perubahan di TenderResource:**
```php
// app/Http/Resources/TenderResource.php
public function toArray($request): array
{
    $vendor = $request->user()?->vendor;
    
    $isParticipant = false;
    if ($vendor) {
        $isParticipant = $this->participants()
            ->where('vendor_id', $vendor->id)
            ->exists();
    }
    
    return [
        'id'              => $this->id,
        'title'           => $this->title,
        'description'     => $this->description,
        'specification'   => $this->specification,
        'status'          => $this->status,
        'start_date'      => $this->start_date,
        'end_date'        => $this->end_date,
        'aanwijzing_date' => $this->aanwijzing_date,
        'bidding_start'   => $this->bidding_start,
        'bidding_end'     => $this->bidding_end,
        'is_participant'  => $isParticipant,  // ← field baru
        'created_at'      => $this->created_at,
    ];
}
```

---

## Prompt untuk Developer Backend

Salin prompt berikut dan berikan ke developer backend Laravel:

---

> **[BACKEND TASK — Mobile App Integration]**
>
> Mobile app (Ionic/Angular) membutuhkan beberapa endpoint tambahan. Tolong implementasikan endpoint-endpoint berikut di Laravel:
>
> **Prioritas Tinggi:**
>
> **1. `GET /api/tenders/{tender}/participants/check`** (auth:sanctum)
> - Cek apakah vendor yang login sudah join tender ini
> - Response: `{ "status": true, "data": { "is_participant": true/false, "joined_at": "..." } }`
>
> **Prioritas Sedang:**
>
> **2. `GET /api/vendors/tenders`** (auth:sanctum)
> - List tender yang vendor yang login sudah join (sebagai peserta)
> - Filter: exclude status `draft`
> - Response: array of TenderResource + field `joined_at`
>
> **3. `GET /api/vendors/results`** (auth:sanctum)
> - List hasil tender yang vendor sudah ikuti (status closed/finished)
> - Response: `[{ tender_id, tender_title, tender_status, is_winner, my_bid_amount, winner_company, winning_bid_amount, decided_at }]`
>
> **4. Tambah `is_participant` ke `TenderResource`**
> - Di `GET /api/tenders/{tender}`, sertakan field `is_participant: boolean`
> - True jika user yang login (via token) adalah peserta tender, false jika bukan atau belum login
>
> Semua endpoint baru harus:
> - Dilindungi middleware `auth:sanctum`
> - Return format: `{ "status": true/false, "message": "...", "data": ... }`
> - Handle error 403 jika vendor belum verified
>
> Referensi struktur response yang sudah ada: lihat `TenderResource`, `BidResource`, `VendorResource` yang sudah ada.

---

## Dampak ke Mobile Jika Backend Sudah Diimplementasi

Setelah backend selesai, update mobile sebagai berikut:

1. **Saat `is_participant` sudah ada di TenderResource:**
   - Ganti `checkParticipation()` di `TenderService` (yang pakai workaround bids/me) dengan baca langsung `tender.is_participant`

2. **Saat `/vendors/tenders` sudah ada:**
   - Tidak perlu implementasi tambahan di mobile (opsional untuk fitur "My Tenders" di masa depan)

3. **Saat `/vendors/results` sudah ada:**
   - Update `ResultHistoryPage.loadResults()`:
   ```typescript
   // Ganti ini:
   this.tenderService.getTenders().pipe(filter...)
   // Dengan:
   this.vendorService.getMyResults() // endpoint baru
   ```
   - Update tab "Hasil" untuk hanya tampilkan tender yang vendor ikuti (lebih akurat)
