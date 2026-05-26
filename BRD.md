# BUSINESS REQUIREMENT DOCUMENT (BRD)
## FINAL PROJECT — E-PROCUREMENT (TENDER & BIDDING SYSTEM)

---

## 1. TUJUAN PROJECT

Kalian WAJIB membangun sebuah sistem E-Procurement berbasis Mobile + Web + Backend yang mampu mengelola proses pengadaan barang/jasa melalui mekanisme tender dan bidding.

Sistem ini HARUS mampu:
- mengelola vendor,
- mengelola event tender,
- mengelola proses registrasi dan verifikasi vendor,
- mengelola proses aanwijzing (penjelasan tender),
- mengelola proses bidding dengan batas waktu,
- menentukan pemenang tender,
- menghasilkan dokumen hasil pengadaan (PO sederhana),
- menyediakan dashboard monitoring untuk admin.

Project ini bukan sekadar aplikasi listing proyek.
Project ini adalah simulasi sistem pengadaan perusahaan / instansi yang kompleks dan berbasis proses.

---

## 2. MASALAH YANG HARUS KALIAN SELESAIKAN

Dalam proses procurement manual, biasanya terjadi:
- vendor tidak terverifikasi dengan baik,
- proses tender tidak transparan,
- bidding tidak terstruktur,
- tidak ada batas waktu yang jelas,
- sulit menentukan pemenang secara objektif,
- dokumen tidak terdokumentasi dengan baik,
- admin kesulitan memantau seluruh proses tender.

Kalian HARUS menyelesaikan masalah tersebut dengan membangun sistem yang:
- memiliki alur tender yang jelas,
- memiliki proses verifikasi vendor,
- memiliki sistem bidding berbasis waktu,
- memiliki pencatatan data yang transparan,
- memiliki dashboard monitoring,
- mampu menghasilkan hasil keputusan tender.

---

## 3. TUJUAN BISNIS SISTEM

### 3.1 Untuk Vendor

Vendor harus bisa:
- mendaftar sebagai vendor,
- mengupload dokumen perusahaan,
- mengikuti event tender,
- memahami detail tender,
- mengikuti proses aanwijzing,
- mengajukan penawaran harga (bidding),
- melihat status tender,
- melihat hasil tender (menang/kalah).

### 3.2 Untuk Admin / Procurement Team

Admin harus bisa:
- membuat event tender,
- mengelola vendor,
- memverifikasi dokumen vendor,
- mengelola proses tender,
- mengatur waktu bidding,
- memantau penawaran vendor,
- menentukan pemenang tender,
- menghasilkan dokumen hasil tender.

### 3.3 Untuk Sistem

Sistem harus mampu:
- mengelola alur tender end-to-end,
- mengatur status setiap tahap,
- memastikan proses berjalan sesuai timeline,
- menyimpan seluruh histori tender,
- menyediakan transparansi proses.

---

## 4. TARGET USER

Kalian WAJIB mengimplementasikan 2 role utama:

### 4.1 Vendor (Mobile / Web)

Vendor adalah peserta tender.

Mereka menggunakan:
- Mobile atau Web

Tujuan:
- registrasi
- upload dokumen
- mengikuti tender
- melakukan bidding

### 4.2 Admin / Procurement (Web)

Admin adalah pengelola sistem.

Mereka menggunakan:
- Web Dashboard

Tujuan:
- membuat tender
- verifikasi vendor
- mengelola bidding
- menentukan pemenang

---

## 5. RUANG LINGKUP PROJECT

Kalian WAJIB membangun sistem yang terdiri dari:

### 5.1 Mobile / Vendor App

Digunakan oleh:
- Vendor

Fungsi utama:
- registrasi vendor
- upload dokumen
- melihat tender
- mengikuti tender
- melakukan bidding
- melihat hasil tender

### 5.2 Web Application

Digunakan oleh:
- Admin

Fungsi utama:
- membuat tender
- verifikasi vendor
- monitoring bidding
- menentukan pemenang
- mengelola dokumen

### 5.3 Backend API

Digunakan sebagai:
- pusat data
- pengelola workflow tender
- penghubung mobile dan web

---

## 6. FITUR BISNIS UTAMA (HIGH LEVEL)

### 6.1 Vendor Journey

Vendor HARUS bisa:

1. registrasi sebagai vendor
2. login ke sistem
3. upload dokumen perusahaan
4. menunggu verifikasi admin
5. melihat daftar tender
6. melihat detail tender
7. mengikuti tender
8. mengikuti proses aanwijzing
9. melakukan bidding (input harga)
10. melihat status tender
11. melihat hasil tender

### 6.2 Admin Journey

Admin HARUS bisa:

1. login ke web
2. mengelola vendor
3. memverifikasi dokumen vendor
4. membuat event tender
5. menentukan spesifikasi tender
6. mengatur waktu tender
7. membuka proses bidding
8. memantau penawaran vendor
9. menutup bidding
10. menentukan pemenang
11. menghasilkan hasil tender (PO sederhana)

---

## 7. FITUR KUNCI YANG WAJIB ADA

### 7.1 Vendor Registration & Verification (WAJIB)

Sistem HARUS:
- menyediakan registrasi vendor,
- memungkinkan upload dokumen,
- memiliki status:
  - pending
  - approved
  - rejected,
- hanya vendor yang approved yang bisa ikut tender.

### 7.2 Tender Management

Sistem HARUS:
- memungkinkan admin membuat tender,
- menyimpan:
  - nama tender
  - deskripsi
  - spesifikasi
  - waktu mulai
  - waktu selesai
- memiliki status tender:
  - draft
  - open
  - bidding
  - closed
  - finished

### 7.3 Aanwijzing (Penjelasan Tender)

Sistem HARUS:
- menyediakan sesi penjelasan tender,
- memungkinkan vendor membaca informasi tambahan,
- memungkinkan admin memberikan update.

### 7.4 Bidding System (WAJIB)

Sistem HARUS:
- membuka bidding dalam waktu tertentu,
- memungkinkan vendor menginput harga,
- mencatat semua penawaran,
- membatasi bidding hanya dalam periode tertentu,
- tidak menerima bidding di luar waktu.

### 7.5 Time-Based Logic (WAJIB)

Sistem HARUS:
- memiliki waktu mulai dan selesai bidding,
- otomatis menutup bidding jika waktu habis,
- menolak input setelah waktu selesai.

### 7.6 Winner Selection

Sistem HARUS:
- memungkinkan admin memilih pemenang,
- dapat berdasarkan:
  - harga terendah (minimal),
  - atau pertimbangan admin (opsional),
- menyimpan hasil keputusan.

### 7.7 Purchase Order (PO) / Result Output

Sistem HARUS:
- menghasilkan hasil tender,
- menampilkan pemenang,
- menampilkan detail penawaran,
- menyediakan dokumen sederhana hasil tender.

---

## 8. NILAI UTAMA YANG HARUS DITUNJUKKAN SISTEM

Sistem kalian HARUS menunjukkan bahwa:
- proses tender berjalan end-to-end,
- vendor benar-benar bisa ikut tender,
- bidding benar-benar berjalan,
- ada batas waktu,
- pemenang benar-benar dipilih,
- sistem bisa dipakai seperti procurement system nyata.

Jika sistem hanya:
- listing tender,
- atau input harga tanpa proses,

maka project dianggap tidak memenuhi standar.

---

## 9. BATASAN PROJECT

### 9.1 Wajib Ada
- registrasi vendor
- verifikasi vendor
- tender system
- bidding system berbasis waktu
- monitoring bidding
- penentuan pemenang
- output hasil tender
- sistem terintegrasi
- demo online

### 9.2 Tidak Boleh
- tidak ada verifikasi vendor,
- tidak ada batas waktu bidding,
- bidding hanya satu kali tanpa proses,
- tidak ada winner selection,
- hanya CRUD tender,
- tidak deploy.

---

## 10. OUTPUT YANG HARUS DIHASILKAN

### 10.1 Vendor App
- bisa digunakan vendor untuk ikut tender

### 10.2 Web Dashboard
- admin panel procurement

### 10.3 Backend API
- pusat logika bisnis

### 10.4 Sistem Terintegrasi
- vendor ↔ backend ↔ admin

### 10.5 Demo Sistem
- end-to-end:
  - vendor daftar
  - vendor diverifikasi
  - vendor ikut tender
  - bidding berlangsung
  - pemenang ditentukan

---

## 11. STANDAR PROJECT

Project dianggap PROFESIONAL jika:
- vendor bisa ikut tender,
- bidding berjalan dengan benar,
- waktu bidding berjalan,
- pemenang ditentukan,
- sistem transparan,
- UI rapi,
- sistem bisa digunakan seperti procurement system nyata.

---

## PENEGASAN

Kalian tidak sedang membuat aplikasi listing proyek.

Kalian sedang membangun:
**sistem tender dan pengadaan seperti di perusahaan / instansi**

Jika sistem kalian hanya:
- daftar tender,
- atau input harga tanpa alur,

maka project dianggap tidak memenuhi standar E-Procurement.