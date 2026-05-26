# TECHNICAL BLUEPRINT
## FINAL PROJECT — E-PROCUREMENT (TENDER & BIDDING SYSTEM)

---

## 1. TUJUAN DOKUMEN

Dokumen ini menjelaskan arah teknis implementasi sistem E-Procurement yang WAJIB kalian pahami sebelum mulai coding.

Dokumen ini menjawab:
- sistem harus dibangun dengan arsitektur seperti apa,
- aplikasi vendor harus mampu melakukan apa,
- web admin harus mampu melakukan apa,
- backend Laravel harus mampu menangani proses apa saja,
- bagaimana alur tender dan bidding harus berjalan,
- data apa saja yang harus dicatat,
- bagaimana sistem mengatur waktu (time-based logic).

Dokumen ini adalah pedoman teknis implementasi, bukan teori.

---

## 2. ARSITEKTUR SISTEM YANG WAJIB DIGUNAKAN

Kalian WAJIB menggunakan arsitektur berikut:

```
Mobile / Vendor App (Ionic + Angular)
 ↓
Backend API (Laravel)
 ↓
Web Admin Dashboard (Laravel)
```

### 2.1 Penjelasan Arsitektur

#### A. Mobile / Vendor App

Digunakan oleh:
- Vendor

Fungsi:
- registrasi
- upload dokumen
- melihat tender
- mengikuti tender
- melakukan bidding
- melihat hasil tender

Mobile hanya bertugas sebagai client.
Semua logika bisnis tetap di backend.

#### B. Web Admin Dashboard

Digunakan oleh:
- Admin / Procurement

Fungsi:
- verifikasi vendor
- membuat tender
- mengatur timeline tender
- monitoring bidding
- menentukan pemenang
- menghasilkan hasil tender

Web adalah pusat kontrol procurement.

#### C. Backend Laravel

Laravel menjadi pusat seluruh proses:
- autentikasi
- vendor management
- tender management
- bidding management
- time control
- approval dan decision
- penyedia data untuk mobile dan web

Laravel adalah core system.

---

## 3. PEMBAGIAN PLATFORM

### 3.1 Mobile App

Digunakan untuk:
- Vendor

### 3.2 Web Dashboard

Digunakan untuk:
- Admin

Kalian tidak boleh mencampur peran vendor dan admin tanpa pemisahan yang jelas.

---

## 4. TECHNOLOGY STACK WAJIB

**Mobile**
- Ionic
- Angular

**Backend**
- Laravel (REST API)

**Web**
- Laravel Web / Blade / Frontend Web

**Database**
- MySQL atau PostgreSQL

---

## 5. STRUKTUR TEKNIS SISTEM

Kalian WAJIB membangun:

1. Vendor App (Mobile/Web)
2. Admin Dashboard (Web)
3. Backend Laravel API

Semua harus terintegrasi.

---

## 6. MOBILE / VENDOR APP YANG WAJIB DIBANGUN

### 6.1 Authentication

Aplikasi harus mampu:
- register vendor
- login
- logout
- mengelola profile

### 6.2 Vendor Profile & Document

Vendor harus bisa:
- mengisi data perusahaan
- upload dokumen
- melihat status verifikasi

### 6.3 Tender Listing

Vendor harus bisa:
- melihat daftar tender
- melihat detail tender
- melihat status tender

### 6.4 Join Tender

Vendor harus bisa:
- mendaftar sebagai peserta tender
- memastikan hanya vendor approved yang bisa join

### 6.5 Aanwijzing View

Vendor harus bisa:
- membaca informasi tambahan dari admin
- melihat update tender

### 6.6 Bidding Interface

Vendor harus bisa:
- memasukkan penawaran harga
- melihat status bidding (aktif / tidak aktif)
- mengetahui apakah masih dalam waktu bidding

Saat waktu habis:
- input harus ditolak

### 6.7 Tender Result

Vendor harus bisa:
- melihat hasil tender
- mengetahui menang atau kalah
- melihat detail hasil

---

## 7. WEB ADMIN DASHBOARD YANG WAJIB DIBANGUN

### 7.1 Admin Dashboard

Harus menampilkan:
- jumlah vendor
- jumlah vendor approved
- jumlah tender aktif
- jumlah tender selesai
- statistik bidding

### 7.2 Vendor Management

Admin harus bisa:
- melihat vendor
- melihat dokumen vendor
- approve vendor
- reject vendor

### 7.3 Tender Management

Admin harus bisa:
- membuat tender
- mengubah tender
- menentukan spesifikasi
- menentukan timeline
- mengubah status tender

### 7.4 Timeline Management (WAJIB)

Admin harus bisa mengatur:
- waktu mulai tender
- waktu aanwijzing
- waktu mulai bidding
- waktu selesai bidding

Timeline ini harus digunakan oleh sistem untuk kontrol otomatis.

### 7.5 Aanwijzing Management

Admin harus bisa:
- menambahkan informasi tender
- memberikan update
- menjelaskan detail kepada vendor

### 7.6 Bidding Monitoring

Admin harus bisa:
- melihat semua penawaran
- melihat vendor yang ikut
- melihat nilai penawaran
- memonitor aktivitas bidding

### 7.7 Winner Selection

Admin harus bisa:
- melihat seluruh penawaran
- memilih pemenang
- menyimpan keputusan

### 7.8 Result Management

Admin harus bisa:
- melihat hasil tender
- menghasilkan output tender (PO sederhana)
- melihat histori tender

---

## 8. KEMAMPUAN BACKEND LARAVEL YANG WAJIB ADA

### 8.1 Authentication & Role Management

Laravel harus mampu:
- login
- register vendor
- membedakan vendor dan admin

### 8.2 Vendor Management

Laravel harus mampu:
- menyimpan data vendor
- menyimpan dokumen vendor
- menyimpan status verifikasi
- memvalidasi vendor sebelum ikut tender

### 8.3 Tender Management

Laravel harus mampu:
- menyimpan data tender
- mengubah status tender
- menyediakan data tender ke vendor
- menyimpan timeline tender

### 8.4 Participant Management

Laravel harus mampu:
- mencatat vendor yang ikut tender
- membatasi hanya vendor approved

### 8.5 Bidding Management

Laravel harus mampu:
- menerima penawaran vendor
- menyimpan nilai penawaran
- menyimpan waktu penawaran
- memungkinkan update penawaran jika masih dalam waktu

### 8.6 Time-Based Logic (KRITIS)

Laravel WAJIB mampu:
- mengecek apakah bidding sudah dimulai
- mengecek apakah bidding sudah berakhir
- menolak input di luar waktu
- mengubah status tender otomatis atau semi-otomatis

Ini adalah inti dari sistem.

### 8.7 Winner Selection Logic

Laravel harus mampu:
- menyediakan semua data bidding
- menyimpan keputusan admin
- menyimpan vendor pemenang

### 8.8 Result Management

Laravel harus mampu:
- menghasilkan hasil tender
- menyediakan data hasil ke vendor
- menyimpan histori tender

### 8.9 Dashboard Data Provider

Laravel harus mampu:
- menyediakan data statistik vendor
- menyediakan data tender
- menyediakan data bidding
- menyediakan data monitoring

---

## 9. DATA YANG WAJIB DICATAT

Kalian bebas desain database, tetapi WAJIB menyimpan:
- data vendor
- data dokumen vendor
- data status vendor
- data tender
- data timeline tender
- data peserta tender
- data bidding
- data hasil tender
- data pemenang
- histori proses tender

Tanpa data ini, sistem tidak lengkap.

---

## 10. ALUR SISTEM YANG WAJIB TERJADI

### 10.1 Alur Vendor

1. vendor register
2. upload dokumen
3. menunggu verifikasi
4. vendor approved
5. vendor melihat tender
6. vendor join tender
7. vendor mengikuti bidding
8. vendor melihat hasil

### 10.2 Alur Admin

1. admin membuat tender
2. admin mengatur timeline
3. admin membuka tender
4. vendor join
5. admin memonitor bidding
6. bidding ditutup
7. admin memilih pemenang
8. hasil tender dipublish

### 10.3 Alur Bidding

1. waktu bidding dimulai
2. vendor submit harga
3. sistem mencatat
4. waktu habis
5. sistem menutup bidding

---

## 11. SEEDING DATA WAJIB

Kalian WAJIB menyediakan:
- beberapa vendor
- data vendor pending dan approved
- beberapa tender
- beberapa peserta tender
- data bidding dummy

Agar sistem bisa diuji dengan nyata.

---

## 12. STRUKTUR KODE WAJIB RAPI

**Laravel**

Pisahkan:
- controller
- service
- business logic
- time logic
- bidding logic

**Mobile**

Pisahkan:
- page
- service API
- model
- state

**Web**

Pisahkan:
- dashboard
- vendor management
- tender management
- bidding monitoring
- result

---

## 13. DEPLOYMENT WAJIB

**Backend**
Harus online

**Web**
Harus online

**Mobile**
APK/AAB

**Demo**

WAJIB end-to-end:
- vendor daftar
- vendor diverifikasi
- tender dibuat
- vendor ikut tender
- bidding berlangsung
- pemenang ditentukan

---

## 14. PENYEBAB GAGAL

Project dianggap gagal jika:
- tidak ada time-based bidding
- vendor tidak diverifikasi
- bidding tidak berjalan
- tidak ada winner selection
- tidak ada alur lengkap
- hanya CRUD

---

## 15. TARGET HASIL AKHIR

Kalian HARUS menghasilkan:
- aplikasi vendor
- dashboard admin
- backend terintegrasi
- sistem bidding real
- sistem tender end-to-end
- deployment online

---

## PENEGASAN

Kalian tidak membuat aplikasi listing proyek.

Kalian membangun:
**sistem tender dengan workflow nyata dan berbasis waktu**

Jika tidak ada:
- timeline,
- bidding,
- winner selection,

maka project dianggap gagal.