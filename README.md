# 🍲 PlusFood - Campus Food Rescue & Express Pre-Order Platform

> **Platform Penyelamatan Makanan Surplus & Canteen Pre-Order Kampus Berbasis Dual-Perspective (Mahasiswa & Mitra UMKM)**  
> *Dikembangkan untuk memenuhi penugasan Web UI/UX Developer - Modul 5: Canteen Pre-Order & Checkout.*

[![Laravel](https://img.shields.io/badge/Laravel-11.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
[![Vue.js](https://img.shields.io/badge/Vue.js-3.x-4FC08D?style=for-the-badge&logo=vue.js&logoColor=white)](https://vuejs.org/)
[![Inertia.js](https://img.shields.io/badge/Inertia.js-Modern%20Monolith-9553E9?style=for-the-badge&logo=inertia&logoColor=white)](https://inertiajs.com/)
[![Tailwind CSS](https://img.shields.io/badge/TailwindCSS-v4-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white)](https://tailwindcss.com/)
[![GitHub Repo](https://img.shields.io/badge/GitHub-arya27Q%2FPlusFood--app-181717?style=for-the-badge&logo=github&logoColor=white)](https://github.com/arya27Q/PlusFood-app)
[![Figma Prototype](https://img.shields.io/badge/Figma-Surplus%20Design%20Modul%205-F24E1E?style=for-the-badge&logo=figma&logoColor=white)](https://www.figma.com/design/4ZD6btsfpaokBTCG17xz9k/surplus?node-id=0-1&t=51TlRTjdnSjmJlwc-0)

---

## 📋 Checklist Pemenuhan Berkas 02 & Modul 8: Data Dinamis & Logika Aplikasi
Berdasarkan rubrik penugasan resmi **MODUL 8 (100 Poin Penuh)** dan berkas dokumentasi, repositori ini telah memenuhi:
* [x] **Target 1: Data Dinamis & Fitur Search/Filter (25%):** Bebas hardcode berulang, looping komponen kartu (`v-for`), sumber data terstruktur (`mockFoods.js` & reactive state), fitur pencarian kata kunci dan filter multi-kategori/radius/harga yang langsung menyaring di layar.
* [x] **Target 2: Fungsionalitas Form, Validasi & Sanitasi (25%):** Formulir checkout & tambah surplus terhubung ke storage/state, sanitasi spasi `.trim()`, pengecekan regex email & nomor telepon, pesan error inline tepat di bawah kolom yang salah, serta proteksi tombol terkunci (*disabled*).
* [x] **Target 3: Kelengkapan UX States (20%):**
  - **Loading State:** Animasi kotak berdenyut (*Loading Skeleton Shimmer* `animate-pulse`) saat memuat data dan status tombol *"Mengirim..."* / spinner saat submit.
  - **Empty State:** Tampilan ramah *"Yah, produk tidak ditemukan 🔍"* dengan tombol *"Reset Filter ↻"* ketika pencarian nihil.
  - **Error State:** Penanganan try/catch dengan pesan kesalahan manusiawi (*"Gagal memuat data. Coba lagi ↻"*).
  - **Toast Notification:** Pop-up notifikasi halus (*"✅ Pesanan berhasil dikirim!"*) menggantikan `alert()` bawaan browser.
* [x] **Target 4: Kerapian Kode, Keamanan .env & Alur Git Branch (15%):** Dikerjakan pada branch `feat/dynamic-data`, seluruh credential/secret tersimpan di `.env` (tidak ikut ter-commit), dan riwayat commit terstruktur.
* [x] **Target 5: Dokumentasi README.md & Panduan Uji Mentor (15%):** Penjelasan sumber data, layanan formulir, log prompt AI, dan panduan halaman yang dapat diuji.

---

## 🎯 Panduan Pengujian Fitur Modul 8 untuk Mentor / Evaluator

Bagi mentor atau penguji yang ingin mencoba alur interaktif aplikasi, berikut halaman dan form utama yang siap diuji:

| Fitur / Halaman | URL Rute | Yang Dapat Dicoba Penguji | Kriteria Modul 8 yang Terpenuhi |
|:---|:---|:---|:---|
| **1. Pencarian & Filter Cepat (Beranda Mahasiswa)** | `/beranda` | - Ketik kata kunci (misal: "ayam", "soto", "roti") pada search bar.<br>- Klik filter cepat ("Nasi", "Roti", "Halal Only", "< Rp15.000").<br>- Ubah radius kampus via selector lokasi pill.<br>- Perhatikan animasi skeleton shimmer saat pertama kali memuat. | Data Dinamis, Looping `v-for`, Fast Filter, Loading Skeleton. |
| **2. Filter Multi-Kriteria & Empty State (Katalog Surplus)** | `/katalog` | - Geser slider harga maksimum (Rp5.000 - Rp25.000).<br>- Filter berdasarkan kategori pill makanan.<br>- Ketik kata kunci yang tidak ada (misal: "pizza spageti") untuk melihat **Empty State ramah** dan klik **Reset Filter ↻**.<br>- Coba ganti kampus ke kota lain (Malang/Jogja) untuk mencoba alur Form Waitlist. | Search Bar, Filter Multi-Kriteria, Empty State + Reset Button, Skeleton Shimmer. |
| **3. Formulir Checkout & Validasi Input** | `/makanan/nasi-campur-madura-pak-joko/checkout` | - Hapus isi nama / masukkan nomor WhatsApp tidak valid (huruf atau < 10 digit) untuk memicu **pesan error inline merah di bawah input**.<br>- Perhatikan tombol "Lanjut Bayar" otomatis **terkunci / disabled** saat form tidak valid.<br>- Isi data lengkap dan klik bayar untuk melihat **Loading Overlay Spinner**, **Toast Notifikasi Halus**, dan halaman sukses receipt. | Validasi Form, Sanitasi `.trim()`, Error Inline, Button Lock, Toast Pop-up. |
| **4. Tambah Surplus Kilat (< 1 Menit)** | `/mitra/menu` | - Isi formulir tambah menu surplus baru di sisi kiri.<br>- Cek kalkulasi otomatis harga diskon & persentase hemat.<br>- Submit menu baru dan saksikan menu langsung bertambah ke daftar aktif serta memicu **Toast Sukses Posting**. | Form Input Write, Two-way Data Binding, Toast Notification. |

---

## 🔗 Tautan Penting Proyek

* **Repositori GitHub:** [https://github.com/arya27Q/PlusFood-app](https://github.com/arya27Q/PlusFood-app)
* **Tautan Figma Prototype (Modul 5):** [https://www.figma.com/design/4ZD6btsfpaokBTCG17xz9k/surplus?node-id=0-1&t=51TlRTjdnSjmJlwc-0](https://www.figma.com/design/4ZD6btsfpaokBTCG17xz9k/surplus?node-id=0-1&t=51TlRTjdnSjmJlwc-0)
* **Folder Dokumentasi Lengkap:** [docs/](file:///d:/laragon/www/plusfood-app/docs/) (PRD, Arsitektur, User Flow, UI Spec, Logbook)

---

## 📌 Ringkasan Produk & Latar Belakang

Di lingkungan kampus, mahasiswa sering melewatkan waktu makan karena antrean kantin yang padat saat jeda pergantian kelas yang sangat sempit (15–20 menit) serta lambatnya kembalian uang tunai. Di sisi lain, mitra warung kantin UMKM sering mengalami kerugian akibat sisa makanan matang layak konsumsi yang tidak habis terjual di malam hari (mencapai Rp40.000 – Rp150.000 per hari).

**PlusFood** hadir sebagai solusi dua arah (*Two-Sided Marketplace*):
1. **POV Mahasiswa (Mobile-First):** Memungkinkan mahasiswa mencari makanan diskon 50%–70%, melakukan *pre-order single-item* cepat, membayar digital via simulasi QR e-wallet, dan mengambil pesanan di kasir kantin cukup dengan menunjukkan **Token Digital QR 3-digit** tanpa mengantre.
2. **POV Mitra UMKM (Desktop & Mobile Dashboard):** Membantu penjual memulihkan modal (*capital recovery*) dengan fitur posting sisa makanan (*Tambah Surplus Kilat < 1 menit*), pemindai QR untuk validasi pesanan anti klaim ganda, serta pemantauan metrik penjualan & dampak lingkungan (CO2 dicegah).

---

## 🚀 Panduan Menjalankan Aplikasi Secara Lokal

### Prasyarat Sistem
* **PHP:** Versi 8.2 atau lebih baru
* **Composer:** Versi 2.x
* **Node.js:** Versi 18.x atau 20.x LTS
* **NPM / PNPM**
* Web Server lokal (Laragon, XAMPP, atau PHP Native Server)

### Langkah-Langkah Instalasi

1. **Clone Repositori:**
   ```bash
   git clone https://github.com/arya27Q/PlusFood-app.git
   cd PlusFood-app
   ```

2. **Instal Dependensi Backend (PHP/Composer):**
   ```bash
   composer install
   ```

3. **Instal Dependensi Frontend (Node.js/NPM):**
   ```bash
   npm install
   ```

4. **Konfigurasi Environment (`.env`):**
   Salin file template `.env.example` menjadi `.env`:
   ```bash
   # Windows PowerShell
   copy .env.example .env
   # Linux/macOS
   cp .env.example .env
   ```
   *Catatan: Pastikan `DB_CONNECTION=sqlite` atau konfigurasikan database MySQL Anda.*

5. **Generate Application Key:**
   ```bash
   php artisan key:generate
   ```

6. **Jalankan Database Migration & Seeder (jika diperlukan):**
   ```bash
   php artisan migrate
   ```

7. **Jalankan Server Lokal:**
   Buka dua terminal terpisah:
   * **Terminal 1 (Laravel Backend Server):**
     ```bash
     php artisan serve
     ```
     *Aplikasi berjalan pada: `http://localhost:8000`*
   * **Terminal 2 (Vite Frontend Development):**
     ```bash
     npm run dev
     ```

8. **Akses Halaman Utama di Browser:**
   * **POV Mahasiswa (Beranda):** `http://localhost:8000/beranda`
   * **POV Mahasiswa (Katalog Surplus):** `http://localhost:8000/katalog`
   * **POV Mitra UMKM (Dashboard):** `http://localhost:8000/mitra/dashboard`
   * **POV Mitra UMKM (Kelola Menu):** `http://localhost:8000/mitra/menu`
   * **POV Mitra UMKM (Pesanan Masuk):** `http://localhost:8000/mitra/pesanan`
   * **POV Mitra UMKM (Profil Gerai):** `http://localhost:8000/mitra/profil`

---

## 📱 Komponen UI Interaktif yang Berfungsi (Memenuhi Rubrik Interaktivitas)

Aplikasi PlusFood mengimplementasikan lebih dari **7 komponen interaktif aktif** dengan state dinamis (bukan sekadar tampilan visual statis):

1. **Collapsible Sidebar Transisi Halus (Mitra Desktop):**
   * Sidebar dapat diciutkan (*collapsed* ke 80px) atau diperluas (260px) dengan tombol toggle transisi halus tanpa menyebabkan pemotongan teks atau kerusakan tabel data.
2. **Mobile Off-Canvas Drawer & Bottom Navigation Bar:**
   * Drawer navigasi mobile dengan auto-close saat menu disentuh, serta *bottom navigation bar* 5-ikon khusus di mobile (dengan tombol floating tengah *Scan QR*).
3. **Modal Interaktif Scan QR Kamera & Input Manual:**
   * Modal dialog untuk kasir memvalidasi QR tiket mahasiswa dengan simulasi *viewfinder* kamera aktif dan input kode tiket 3-digit manual.
4. **Modal Tambah Surplus Kilat (< 1 Menit):**
   * Form popup tambah menu sisa dengan kalkulasi diskon otomatis dan proteksi validasi formulir.
5. **Dynamic Multi-Criteria Filter (Katalog Mahasiswa):**
   * Filter interaktif berupa slider harga (Rp5.000 – Rp25.000), filter kategori pill (Semua, Nasi, Mie, Soto, Roti, Camilan, Minuman), pilihan radius kampus (500m – 3km), serta checkbox preferensi diet (Halal, Vegetarian, Bebas Kacang).
6. **Mobile Collapsible Filter Accordion:**
   * Panel filter di halaman mobile dapat dibuka-tutup (*accordion*) agar mahasiswa langsung melihat kartu makanan surplus di layar tanpa scrolling berlebih.
7. **Filter Tab Status Riwayat Transaksi:**
   * Penggantian data dinamis pada tab *Semua*, *Menunggu Pickup*, *Selesai*, dan *Dibatalkan* serta *Chart Switcher* (Porsi, Modal Rp, CO2) pada Dashboard Mitra.

---

## 👥 Pembagian Kontribusi Anggota Kelompok

| No | Nama Anggota | Akun GitHub | Peran / Tanggung Jawab Utama | Rincian Kontribusi Nyata |
|:---|:---|:---|:---|:---|
| **1** | **Arya Pratama** | [@arya27Q](https://github.com/arya27Q) | **Project Lead, Lead Fullstack Engineer & UI/UX Refiner** | - Perancangan arsitektur sistem end-to-end (Laravel 11, Inertia.js, Vue 3, & Tailwind CSS).<br>- Perancangan User Flow lengkap & penyempurnaan menyeluruh prototipe UI/UX Figma Modul 5.<br>- Eksekusi seluruh kodingan antarmuka dual-POV (Mahasiswa Mobile-first & Mitra Dashboard).<br>- Rekayasa komponen interaktif utama (Collapsible Sidebar, Bottom Nav, Scan QR Modal, Dynamic Filter).<br>- Pelaksanaan wawancara utama pengguna lapangan dan finalisasi seluruh dokumentasi (`docs/`). |
| **2** | **Muhammad Mirza Fatih** | [@MuhammadMirzaFatih](https://github.com/MuhammadMirzaFatih) | **UI/UX Drafter, Research Assistant & QA Tester** | - Pembuatan *drafting* awal wireframe dan eksplorasi visual dasar antarmuka Figma.<br>- Asistensi perekaman dan dokumentasi sesi wawancara pengguna lapangan.<br>- Pengujian antarmuka manual (*User Acceptance Testing / UAT*) pada browser & validasi form input.<br>- Kurasi awal aset gambar dan data dummy katalog makanan surplus. |

> **Catatan Penilaian Kolaborasi:** Pembagian peran mencerminkan proses iterasi tim, di mana drafting awal antarmuka dan pengujian dibantu oleh UI/UX Drafter & QA, sementara perancangan arsitektur, rekayasa kode penuh, penyempurnaan prototipe, dan kepemimpinan teknis diselesaikan oleh Project Lead.



---

## 🤖 Dokumentasi Log Prompt AI & Catatan Perbaikan Manual

Dalam proses pengembangan prototipe PlusFood, kami memanfaatkan kecerdasan buatan (*AI Assistant*) untuk mempercepat pembuatan scaffold kode dan perumusan dokumen, yang kemudian disempurnakan melalui perbaikan manual (*human-in-the-loop*).

### A. Ringkasan Log Prompt AI
1. **Prompt Perumusan PRD & Scope:**
   > *"Make Product Requirement Document for: Role PM Technical Lead for campus life web app. Target User: Maya (20), 2nd-year college student. Context: Canteen Pre-Order & Checkout (Module 5). Define User Story, In-Scope, Out-of-Scope, and Success Metrics."*
2. **Prompt Pembuatan Komponen Vue.js:**
   > *"Buatkan komponen Inertia Vue 3 untuk Katalog Makanan Surplus dengan filter interaktif slider harga, kategori pill, radius kampus, dan indikator countdown sisa waktu pickup dengan tema hangat PlusFood (#700926 maroon)."*
3. **Prompt Dashboard Mitra UMKM:**
   > *"Rancang MitraLayout dengan sidebar collapsible yang bisa dibuka-tutup halus, tabel pesanan masuk real-time dengan status pembayaran, dan modal pemindai QR tiket mahasiswa."*

### B. Catatan Perbaikan Manual (*Manual Refinement Notes*)
* **Perbaikan Font & Tipografi Global:**
  * AI awalnya menggunakan font bawaan sistem (*Inter / sans-serif generic*). Dilakukan perbaikan manual dengan mengintegrasikan font **Nunito** dari Google Fonts ke dalam `app.blade.php` dan `app.css` agar tampilan terasa lebih modern, ramah, dan khas aplikasi kuliner.
* **Perbaikan Sidebar Collapsed State & Table Overflow:**
  * Ketika sidebar desktop diciutkan, logo dan status aktif menu sempat terpotong (*clipped*), dan tabel pesanan bergeser membungkus teks ke bawah. Dilakukan perbaikan manual dengan menambahkan `min-w-[680px]` dan pembungkus `overflow-x-auto` serta `whitespace-nowrap` pada kolom tabel agar tabel tetap rapi dan dapat digeser horizontal dengan mulus.
* **Refactoring Responsivitas Mobile (Dual-POV):**
  * Penambahan navigasi bawah (*Mobile Bottom Navigation Bar*) khusus mitra UMKM lengkap dengan tombol tengah *Raised Scan QR* dan badge notifikasi order.
  * Pembuatan *Collapsible Accordion Filter* di halaman Katalog Mobile Mahasiswa agar panel filter tidak memakan 600px ruang layar atas.
  * Penghapusan *nested padding* ganda pada halaman Profil Mitra yang sempat membuat kartu profil mengecil di layar HP 360px.
* **Validasi Safe Area & Modal Guard:**
  * Penambahan `max-h-[92vh] overflow-y-auto` pada `ScanQrModal.vue` agar kamera dan tombol aksi tetap bisa diakses dengan nyaman pada layar ponsel berdimensi pendek.

---

## 📂 Struktur Dokumentasi Proyek (`docs/`)

Dokumen spesifikasi resmi dapat dibaca pada folder `docs/`:
* [**01. Latar Belakang & Ideasi**](docs/01_latar_belakang_dan_ideasi.md): Latar belakang pemborosan pangan kantin dan solusi *Triple Impact*.
* [**02. Product Requirements Document (PRD)**](docs/02_prd.md): Batasan In-Scope & Out-of-Scope, User Persona, User Story, dan Kriteria Kelulusan.
* [**03. Arsitektur Sistem**](docs/03_arsitektur_sistem.md): Alasan pemilihan arsitektur Laravel 11 + Inertia.js + Vue 3 Monolith Modern.
* [**04. User Flow & Panduan**](docs/04_user_flow_dan_panduan.md): Alur perjalanan mahasiswa dan mitra dari reservasi hingga validasi QR.
* [**05. Spesifikasi Desain UI**](docs/05_spesifikasi_desain_ui.md): Hirarki komponen dan panduan keselarasan desain Figma.
* [**06. Skema Database & Relasi**](docs/06_skema_database_dan_relasi.md): Spesifikasi ERD, kamus data lengkap, foreign key cascading, dan query bisnis utama.
* [**07. Panduan Git Workflow & Kontribusi**](docs/07_panduan_git_workflow_dan_kontribusi.md): SOP branching 2-branch, panduan push, PR, merge, dan log kontribusi tim.
* [**08. Model Operasional Admin & Kemitraan UMKM**](docs/08_model_operasional_admin_dan_kemitraan_umkm.md): Peran operator admin, validasi pasar kota ekspansi (waitlist), dasar hukum *click-wrap agreement*, SOP WhatsApp onboarding, dan panduan defense sidang.
* [**Desain Sistem Dual-POV**](docs/desain_sistem_plusfood_2_pov.md): Penjelasan pilar LBS, Single-Item reservation paradigm, dan skema database.

---

## 🚀 Panduan Deployment Laravel + Vue/Inertia ke Hostinger

Dokumen ini menjelaskan proses deploy project PlusFood (Laravel 11 + Vue.js/Inertia) ke Hostinger Web Hosting, termasuk konfigurasi database, Composer, Vite, `.htaccess`, SSH, debugging error 500/404, dan solusi masalah yang muncul saat deployment.

---

### 1. Struktur Project

Project yang digunakan:
- **Backend**: Laravel 11.x
- **Frontend**: Vue.js 3.x
- **Adapter Frontend**: Inertia.js
- **Build Tool**: Vite
- **Hosting**: Hostinger Web Hosting
- **Server**: PHP 8.3
- **Database**: MySQL

Berbeda dengan PHP native, Laravel membutuhkan beberapa komponen tambahan:

```text
Laravel
├── Composer
├── vendor/
├── .env
├── php artisan
├── routing
├── cache
└── Vite build
```

Vue juga tidak langsung dibaca oleh server production. File Vue harus diubah menjadi file JavaScript production menggunakan:

```bash
npm run build
```

---

### 2. Persiapan Project di Komputer Lokal

Masuk ke folder utama project (`PlusFood-app`), yaitu folder yang berisi:

```text
artisan
composer.json
package.json
resources/
routes/
```

Jalankan di terminal lokal:

```bash
composer install --no-dev --optimize-autoloader
npm install
npm run build
```

Setelah selesai, pastikan file berikut tersedia:

```text
vendor/autoload.php
public/build/manifest.json
public/build/assets/
```

> [!WARNING]
> **Jangan upload folder `node_modules/`!**  
> Folder `node_modules` hanya diperlukan di komputer lokal untuk proses build Vue. Server hanya memerlukan hasil build di `public/build/`.

---

### 3. File yang Perlu Disiapkan

File yang diperlukan untuk deployment:

```text
app/
bootstrap/
config/
database/
resources/
routes/
storage/
vendor/
.env
artisan
composer.json
composer.lock
public/
```

File frontend hasil build:

```text
public/build/
├── manifest.json
└── assets/
```

File yang **TIDAK** perlu di-upload:
```text
node_modules/
.git/
```

*Catatan: `.gitignore` boleh ikut ter-upload, tetapi tidak diperlukan oleh Laravel saat aplikasi berjalan.*

---

### 4. Membuat Database di Hostinger

Di hPanel Hostinger:
```text
Websites → Dashboard → Databases → Management
```

1. Buat database MySQL baru.
2. Catat data kredensial:
   - Database name
   - Database username
   - Database password
   - Database host (`localhost` atau `127.0.0.1`)
   - Database port (`3306`)

> [!NOTE]
> Biasanya nama database dan username memiliki awalan akun Hostinger, contoh:
> ```env
> DB_DATABASE=u356717581_namadb
> DB_USERNAME=u356717581_user
> DB_PASSWORD=password_database_baru
> ```
> Jangan hanya memakai nama pendek jika Hostinger menampilkan nama lengkap.

---

### 5. Import File SQL

File `.sql` tidak ditaruh permanen di `public_html`.

**Cara import via phpMyAdmin:**
```text
hPanel → Websites → Dashboard → Databases → Management → phpMyAdmin → Pilih Database → Import
```
1. Pilih file SQL hasil export dari database lokal, lalu klik **Go / Import**.
2. Setelah import berhasil, hapus file `.sql` jika sebelumnya sempat ter-upload ke File Manager.

> [!CAUTION]
> Jangan menjalankan `php artisan migrate:fresh` di server production karena dapat menghapus seluruh tabel dan data.

---

### 6. Membuat File `.env` Production

File `.env` berada di root project Laravel (misal: `public_html/.env`).

Contoh konfigurasi production:

```env
APP_NAME=PlusFood
APP_ENV=production
APP_KEY=base64:APP_KEY_DARI_PROJECT_LOKAL
APP_DEBUG=false
APP_URL=https://gold-curlew-865919.hostingersite.com

APP_LOCALE=en
APP_FALLBACK_LOCALE=en

LOG_CHANNEL=stack
LOG_STACK=single
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=u356717581_namadb
DB_USERNAME=u356717581_user
DB_PASSWORD=password_database_baru

SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=null

BROADCAST_CONNECTION=log
FILESYSTEM_DISK=local
QUEUE_CONNECTION=database
CACHE_STORE=database

MAIL_MAILER=log
```

> [!IMPORTANT]
> Pastikan di production:
> ```env
> APP_ENV=production
> APP_DEBUG=false
> ```
> Jangan gunakan `APP_ENV=local` atau `APP_URL=http://localhost:8000`. Password database, password SSH, `APP_KEY`, dan cookie jangan dibagikan ke repository publik.

---

### 7. Struktur Deployment yang Digunakan

Struktur yang digunakan di Hostinger:

```text
public_html/
├── index.php
├── .htaccess
├── .env
├── artisan
├── app/
├── bootstrap/
├── config/
├── database/
├── resources/
├── routes/
├── storage/
├── vendor/
├── public/
│   ├── build/
│   │   ├── manifest.json
│   │   └── assets/
│   ├── .htaccess
│   ├── index.php
│   └── favicon.ico
└── build/
```

> [!TIP]
> Pada project ini Laravel membaca asset Vite dari `public/build/` dan/atau `build/`. Pastikan folder `public/build/manifest.json` dan `build/manifest.json` tersedia.

---

### 8. Konfigurasi File `index.php`

Karena file source Laravel berada langsung di `public_html`, file `public_html/index.php` utama menggunakan path tanpa `../`:

```php
<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

if (file_exists($maintenance = __DIR__.'/storage/framework/maintenance.php')) {
    require $maintenance;
}

require __DIR__.'/vendor/autoload.php';

/** @var Application $app */
$app = require_once __DIR__.'/bootstrap/app.php';

$app->handleRequest(Request::capture());
```

Pastikan file berikut ada dan dapat diakses:
- `public_html/vendor/autoload.php`
- `public_html/bootstrap/app.php`
- `public_html/.env`

---

### 9. Konfigurasi File `.htaccess`

Agar URL Laravel seperti `/login`, `/register`, `/mitra`, dan `/dashboard` diteruskan ke Laravel, buat file `public_html/.htaccess`:

```apache
<IfModule mod_rewrite.c>
    RewriteEngine On

    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>
```

Tanpa rewrite ini, server akan mencari folder fisik bernama `login` atau `register` dan mengembalikan error **404 Not Found**.

---

### 10. Build Vue dan Vite

Setiap kali mengubah file Vue (misal di `resources/js/Pages/`):
1. Jalankan di komputer lokal:
   ```powershell
   npm run build
   ```
2. Upload folder `public/build/` ke server menjadi:
   - `public_html/public/build/manifest.json`
   - `public_html/build/manifest.json`
3. Setelah upload hasil build:
   ```bash
   php artisan optimize:clear
   rm -f public/hot
   ```
   *(File `public/hot` menandakan Vite dalam mode dev server lokal dan tidak boleh ada di production).*

---

### 11. Menggunakan SSH di Windows

SSH adalah terminal untuk menjalankan perintah langsung di server Hostinger.

#### Instalasi OpenSSH di Windows (PowerShell Administrator):
```powershell
Add-WindowsCapability -Online -Name OpenSSH.Client~~~~0.0.1.0
C:\Windows\System32\OpenSSH\ssh.exe -V
```

#### Login ke Server Hostinger:
```powershell
C:\Windows\System32\OpenSSH\ssh.exe u356717581@IP_SERVER -p 65002
```
*(Port dan IP server dilihat dari menu SSH Access di hPanel)*.

Setelah login berhasil:
```bash
cd ~/domains/gold-curlew-865919.hostingersite.com/public_html
```

---

### 12. Perintah Laravel yang Sering Digunakan di SSH

```bash
# 1. Masuk ke folder root project
cd ~/domains/gold-curlew-865919.hostingersite.com/public_html

# 2. Membersihkan cache
php artisan optimize:clear
php artisan route:clear

# 3. Melihat daftar route
php artisan route:list
php artisan route:list --path=mitra
php artisan route:list --path=dashboard
php artisan route:list --path=login

# 4. Package discovery
php artisan package:discover --ansi

# 5. Permission folder storage & cache
chmod -R 775 storage bootstrap/cache

# 6. Dump autoload tanpa proc_open
composer dump-autoload --optimize --no-scripts
```

> [!NOTE]
> Pada shared hosting, fungsi PHP `proc_open` dinonaktifkan sehingga `composer dump-autoload` perlu ditambahkan opsi `--no-scripts`.

---

### 13. Diagnosa Masalah & Solusi (Troubleshooting Guide)

#### A. Masalah `vendor/autoload.php not found`
- **Penyebab**: Path `index.php` salah atau folder `vendor/` belum lengkap ter-upload.
- **Solusi**: Di lokal jalankan `composer install --no-dev --optimize-autoloader`, kompres menjadi `vendor.zip`, upload ke `public_html/` lalu extract.

#### B. Masalah `Target class [view] does not exist`
- **Penyebab**: File `bootstrap/app.php` atau `config/app.php` diedit manual tidak sesuai format asli Laravel 11 (misal penambahan `withProviders()`), atau cache konfigurasi lama masih tersimpan.
- **Solusi**:
  1. Pulihkan file asli dari lokal untuk `bootstrap/app.php`, `config/app.php`, dan `bootstrap/providers.php`.
  2. Hapus file `config.php` di dalam `bootstrap/cache/`.
  3. Jalankan di SSH:
     ```bash
     php artisan optimize:clear
     composer dump-autoload --optimize --no-scripts
     php artisan package:discover --ansi
     ```

#### C. Masalah Layar Putih (Blank Screen)
- **Penyebab**: File build Vite (`manifest.json` & `assets/`) belum ter-upload, atau file `public/hot` masih ada.
- **Solusi**:
  1. Jalankan `npm run build` di lokal.
  2. Upload folder `public/build` ke `public_html/build` dan `public_html/public/build`.
  3. Hapus file `public/hot` jika ada:
     ```bash
     rm -f public/hot
     ```
  4. Lakukan hard refresh browser (`Ctrl + F5`) atau Incognito.

#### D. Masalah 404 pada `/login`, `/register`, atau `/mitra`
- **Penyebab**: File `.htaccess` di `public_html/` belum ada atau rewrite engine mati.
- **Solusi**: Buat file `public_html/.htaccess` dengan aturan rewrite menuju `index.php`.

#### E. Masalah Alur Registrasi Mahasiswa
- **Alur Standar**: `Register → Login → Loading → Beranda`
- Untuk mencegah registrasi langsung login otomatis tanpa verifikasi, pastikan di controller registrasi tidak memanggil `Auth::guard('web')->login($user)`.

---

### 14. Ringkasan Solusi Cepat Deployment

| Gejala Error | Penyebab Utama | Solusi Pasti |
|---|---|---|
| `vendor/autoload.php not found` | Path salah / vendor tidak lengkap | Sesuaikan path di `index.php`, upload ulang `vendor.zip` |
| `Target class [view] does not exist` | Modifikasi config/cache yang salah | Kembalikan `bootstrap/app.php` asli, `optimize:clear` |
| Halaman Putih (Blank Screen) | Build Vite belum ter-upload / file `hot` ada | `npm run build`, upload `public/build`, hapus `public/hot` |
| Route `/login` atau `/mitra` 404 | `.htaccess` belum rewrite | Buat `.htaccess` dengan aturan rewrite ke `index.php` |
| Composer error `proc_open is not available` | Fungsi hosting dinonaktifkan | Gunakan `composer dump-autoload --optimize --no-scripts` |
| Log error berisi path `D:\laragon` | Log lokal ikut ter-upload | Hapus `storage/logs/laravel.log` lama agar terbaca log hosting |

---

### 15. Checklist Urutan Deploy Selanjutnya

```text
1. Pastikan project berjalan normal di lokal
2. composer install --no-dev --optimize-autoloader
3. npm install && npm run build
4. Buat database MySQL & user di hPanel Hostinger
5. Import file .sql via phpMyAdmin Hostinger
6. Siapkan .env production di public_html/.env
7. Upload source code, vendor, dan folder build
8. Pastikan index.php dan .htaccess di public_html sudah benar
9. Buka SSH Hostinger:
   cd ~/domains/gold-curlew-865919.hostingersite.com/public_html
   php artisan optimize:clear
   php artisan package:discover --ansi
   php artisan route:list
10. Buka domain di browser dan uji seluruh alur aplikasi
```

---

## 📄 Lisensi & Hak Cipta
Dikembangkan dengan ❤️ oleh **Kelompok PlusFood** © 2026. Lisensi di bawah [MIT License](LICENSE).