# SYSTEM REQUIREMENT SPECIFICATION (SRS)
## FINAL PROJECT — E-PROCUREMENT (TENDER & BIDDING SYSTEM)

---

## 1. TUJUAN SISTEM

Kalian WAJIB membangun sistem E-Procurement yang:
- dapat digunakan oleh vendor dan admin,
- mampu menjalankan proses tender secara end-to-end,
- memiliki alur verifikasi vendor,
- memiliki sistem bidding berbasis waktu,
- memiliki mekanisme penentuan pemenang,
- mampu menghasilkan hasil tender (PO sederhana),
- berjalan terintegrasi antara mobile app, web dashboard, dan backend.

---

## 2. DEFINISI ROLE SISTEM

Kalian WAJIB mengimplementasikan 2 role utama:

### 2.1 Vendor (Mobile / Web)

Vendor adalah peserta tender.

Vendor WAJIB bisa:
- register
- login
- upload dokumen
- melihat tender
- mengikuti tender
- mengikuti aanwijzing
- melakukan bidding
- melihat status tender
- melihat hasil tender

### 2.2 Admin / Procurement (Web)

Admin adalah pengelola sistem.

Admin WAJIB bisa:
- login
- mengelola vendor
- memverifikasi vendor
- membuat tender
- mengatur timeline tender
- membuka dan menutup bidding
- memonitor bidding
- menentukan pemenang
- menghasilkan hasil tender

---

## 3. MODUL SISTEM DAN REQUIREMENT DETAIL

### 3.1 MODULE: AUTHENTICATION

Requirement WAJIB:
- user dapat register (vendor)
- user dapat login
- user dapat logout
- sistem membedakan role:
  - vendor
  - admin
- user dapat update profile
- user dapat mengganti password

### 3.2 MODULE: VENDOR MANAGEMENT

#### 3.2.1 Vendor Registration

Vendor WAJIB bisa:
- melakukan registrasi
- mengisi data:
  - nama perusahaan
  - email
  - alamat
  - kontak

#### 3.2.2 Document Upload

Vendor WAJIB bisa:
- upload dokumen perusahaan
- dokumen bisa berupa:
  - legalitas
  - izin usaha
  - dokumen pendukung lainnya

#### 3.2.3 Vendor Verification

Admin WAJIB bisa:
- melihat data vendor
- melihat dokumen vendor
- mengubah status vendor menjadi:
  - pending
  - approved
  - rejected

Vendor yang belum approved TIDAK boleh mengikuti tender.

### 3.3 MODULE: TENDER MANAGEMENT

#### 3.3.1 Create Tender

Admin WAJIB bisa:
- membuat tender
- mengisi:
  - nama tender
  - deskripsi
  - spesifikasi
  - tanggal mulai
  - tanggal selesai
  - waktu bidding

#### 3.3.2 Tender Status

Tender WAJIB memiliki status:
- draft
- open
- aanwijzing
- bidding
- closed
- finished

#### 3.3.3 Tender Listing

Vendor WAJIB bisa:
- melihat daftar tender
- melihat detail tender

### 3.4 MODULE: AANWIJZING

Requirement WAJIB:

Sistem HARUS:
- menyediakan informasi tambahan tender
- memungkinkan admin menambahkan informasi atau update
- memungkinkan vendor membaca informasi tersebut

### 3.5 MODULE: TENDER PARTICIPATION

Requirement WAJIB:

Vendor WAJIB bisa:
- mendaftar ke tender
- sistem mencatat vendor sebagai peserta
- hanya vendor approved yang bisa ikut

### 3.6 MODULE: BIDDING SYSTEM

#### 3.6.1 Bidding Input

Vendor WAJIB bisa:
- mengajukan penawaran harga
- mengubah penawaran selama waktu bidding masih berjalan (opsional tapi dianjurkan)

#### 3.6.2 Bidding Time Control (WAJIB)

Sistem WAJIB:
- memiliki waktu mulai bidding
- memiliki waktu selesai bidding
- hanya menerima bidding dalam rentang waktu tersebut
- menolak bidding di luar waktu

#### 3.6.3 Bidding Monitoring

Admin WAJIB bisa:
- melihat seluruh penawaran
- melihat siapa vendor yang melakukan bidding
- melihat nilai penawaran

### 3.7 MODULE: WINNER SELECTION

Requirement WAJIB:

Admin HARUS bisa:
- melihat semua penawaran
- menentukan pemenang
- menyimpan hasil keputusan

Sistem WAJIB:
- mencatat vendor pemenang
- mencatat nilai penawaran pemenang

### 3.8 MODULE: RESULT & PO OUTPUT

Requirement WAJIB:

Sistem HARUS:
- menampilkan hasil tender
- menampilkan pemenang
- menampilkan detail penawaran
- menghasilkan dokumen sederhana hasil tender (PO)

Vendor WAJIB bisa:
- melihat hasil tender
- mengetahui apakah menang atau kalah

### 3.9 MODULE: ADMIN DASHBOARD

Admin WAJIB bisa melihat:
- jumlah vendor
- jumlah vendor approved
- jumlah tender aktif
- jumlah tender selesai
- jumlah peserta tender
- statistik bidding

---

## 4. DATA REQUIREMENT

### 4.1 Data Utama

Kalian WAJIB menyimpan:
- data vendor
- data dokumen vendor
- data tender
- data peserta tender
- data bidding
- data hasil tender
- data approval vendor

### 4.2 Data Bidding

Harus menyimpan:
- vendor_id
- tender_id
- nilai penawaran
- waktu bidding

### 4.3 Data Timeline

Harus menyimpan:
- waktu mulai tender
- waktu aanwijzing
- waktu mulai bidding
- waktu selesai bidding

---

## 5. SYSTEM FLOW

### 5.1 Flow Vendor Registration

1. vendor register
2. vendor upload dokumen
3. status = pending
4. admin verifikasi
5. status = approved/rejected

### 5.2 Flow Tender

1. admin membuat tender
2. tender status = draft
3. admin membuka tender
4. vendor melihat tender
5. vendor mendaftar sebagai peserta

### 5.3 Flow Aanwijzing

1. admin memberikan informasi tambahan
2. vendor membaca informasi

### 5.4 Flow Bidding

1. bidding dibuka
2. vendor mengajukan penawaran
3. sistem mencatat penawaran
4. waktu habis
5. sistem menutup bidding

### 5.5 Flow Winner Selection

1. admin melihat semua penawaran
2. admin memilih pemenang
3. sistem menyimpan pemenang
4. hasil tender ditampilkan

### 5.6 Flow Result

1. vendor melihat hasil tender
2. vendor mengetahui status menang/kalah
3. admin melihat hasil keseluruhan

---

## 6. NON-FUNCTIONAL REQUIREMENT

### 6.1 Performance
- response API < 3 detik

### 6.2 Security
- password harus di-hash
- validasi input wajib
- vendor tidak boleh melihat bidding vendor lain (opsional tapi dianjurkan)

### 6.3 Usability
- UI jelas
- timeline mudah dipahami
- status tender jelas

### 6.4 Reliability
- sistem tidak crash saat demo
- waktu bidding berjalan dengan benar

---

## 7. DEPLOYMENT REQUIREMENT

Kalian WAJIB:
- deploy backend API
- deploy web dashboard
- build mobile app
- demo LIVE

---

## 8. ACCEPTANCE CRITERIA

Project dianggap berhasil jika:
- vendor bisa register dan diverifikasi
- vendor bisa ikut tender
- bidding berjalan dalam waktu tertentu
- bidding berhenti saat waktu habis
- admin bisa melihat penawaran
- admin bisa menentukan pemenang
- hasil tender muncul
- sistem berjalan end-to-end

---

## PENEGASAN

Jika sistem kalian:
- tidak memiliki waktu bidding,
- tidak memiliki verifikasi vendor,
- tidak memiliki proses penentuan pemenang,
- hanya CRUD tender,

maka project dianggap tidak memenuhi standar E-Procurement.