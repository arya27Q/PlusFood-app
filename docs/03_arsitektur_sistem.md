# 03. Arsitektur Sistem PlusFood & Alasan Pemilihan

Dokumen ini menjelaskan arsitektur teknis aplikasi **PlusFood**, alasan mendalam di balik pemilihan teknologi **Laravel 11 + Inertia.js + Vue 3**, serta bagaimana struktur kode dirancang untuk mendukung **Dual-Perspective (Mahasiswa & Mitra UMKM)**.

---

## 1. Stack Teknologi Utama

* **Backend & Database Engine:** Laravel 11 (PHP 8.2+), Eloquent ORM, MySQL / SQLite.
* **Glue / Adapter Layer:** Inertia.js (Inertia Laravel Adapter & Inertia Vue 3 Adapter).
* **Frontend View & Reactive Logic:** Vue 3 (Composition API `<script setup>`), Tailwind CSS.
* **Build Tool:** Vite.

---

## 2. Mengapa Memilih Arsitektur Ini? (Justifikasi & Rationale)

Pemilihan kombinasi **Laravel + Inertia.js + Vue 3** didasarkan pada kebutuhan spesifik model bisnis PlusFood:

### A. Kecepatan Pengembangan Tanpa Kompleksitas REST API Terpisah
* **Tradisional SPA (React/Vue terpisah + REST API):** Membutuhkan pembuatan endpoint API terpisah, manajemen autentikasi berbasis Bearer token (Sanctum/JWT), penanganan *CORS*, duplikasi validasi input di front & backend, dan state store yang berat (Pinia/Redux).
* **Inertia.js Monolith Modern:** Menggabungkan kekuatan backend Laravel (keamanan session cookie, ORM, controller) dengan reaktivitas modern Vue 3. Inertia bertindak sebagai jembatan yang mengirimkan data dari Controller Laravel langsung sebagai *props* ke komponen Vue **tanpa perlu menulis API endpoint manual**.

### B. Dual-Persona UX yang Berbeda Karakter
PlusFood memiliki dua kebutuhan antarmuka yang sangat bertolak belakang:
1. **POV Mahasiswa:** Berorientasi pada perangkat bergerak (*mobile-first*), membutuhkan navigasi cepat, bottom navigation bar, kartu swipe, animasi filter instan, dan proses checkout kilat.
2. **POV Mitra UMKM:** Berorientasi pada produktivitas (*desktop/tablet friendly*), membutuhkan tata letak dashboard komprehensif, sidebar, data grid, formulir input cepat dengan validasi real-time, dan scanner kamera QR.

> *Dengan arsitektur ini, kita dapat memisahkan Layout dan Pages secara elegan menggunakan `MahasiswaLayout.vue` dan `MitraLayout.vue` di bawah satu aplikasi yang sama tanpa harus membuat 2 aplikasi terpisah.*

### C. Reaktivitas Tinggi untuk Model "Single-Item Flash Sale"
* Makanan sisa (*food surplus*) memiliki sifat **stok sangat terbatas** dan **waktu hitung mundur (countdown)** yang cepat.
* Menggunakan Vue 3 Composition API (`useCart`, `useFilter`, `useModal`) memungkinkan perubahan kuantitas pesanan, kalkulasi subtotal instan, pemfilteran alergen seketika (*client-side reactivity*), dan transisi pop-up QR Code yang mulus tanpa refresh halaman browser (*Single Page App feeling*).

### D. Keamanan Transaksi & Anti Double-Claim
* Menjual sisa makanan dengan harga sangat murah berisiko tinggi terhadap perebutan kupon atau eksploitasi kuota.
* Laravel menangani *database transaction*, *locking* kuota stok saat reservasi terjadi, otentikasi berbasis *session guard*, dan hashing token QR Code dengan sangat andal di sisi server (*Server-Side Reliability*).

---

## 3. Perbandingan Pola Arsitektur

| Kriteria | Blade Tradisional (MPA) | REST API + SPA Terpisah | Laravel 11 + Inertia.js + Vue 3 (Pilihan PlusFood) |
| :--- | :--- | :--- | :--- |
| **Pengalaman Pengguna (UX)** | Lambat, layar berkedip karena reload halaman | Sangat cepat & interaktif | Sangat cepat & interaktif (SPA) |
| **Pengembangan Filter & Interaktivitas** | Rumit (butuh jQuery / Alpine parsial) | Mudah menggunakan Vue | Sangat mudah & bersih menggunakan Vue 3 |
| **Manajemen State & API Overhead** | Rendah | Tinggi (butuh API Controller, Resource, DTO terpisah) | Minimal (Data controller langsung jadi props Vue) |
| **Single-Page Feel untuk Mobile** | Kurang optimal | Bagus | Sangat optimal |
| **Kesiapan Dual POV (Mhs & Mitra)** | Tumpang tindih view | Harus mengelola routing API terpisah | Terkelola rapi lewat struktur Pages & Layouts |

---

## 4. Struktur Folder Frontend (`resources/js`)

Arsitektur direktori diatur secara modular memisahkan komponen visual, logika state (*composables*), tata letak (*layouts*), dan halaman spesifik pengguna (*pages*):

```text
resources/js/
├── 📄 app.js                   <-- (Inti/Entry Point) Setup Vue 3 dan Inertia
├── 📄 bootstrap.js             <-- (Konfigurasi) Setup Axios & header CSRF
│
├── 📁 Components/              <-- 🎨 KOMPONEN UI (Potongan visual reusable)
│   ├── 📁 UI/                  <-- Elemen umum: PrimaryButton.vue, TextInput.vue, Badge.vue, Modal.vue
│   ├── 📁 Mahasiswa/           <-- Khusus mhs: FoodCard.vue, QuantityPicker.vue, ReservationModal.vue
│   └── 📁 Mitra/               <-- Khusus mitra: DataGrid.vue, SidebarMenu.vue, ScanQrModal.vue
│
├── 📁 Composables/             <-- 🧠 LOGIKA STATE (Dipisah dari UI)
│   ├── 📄 useCart.js           <-- Logika pilihan jumlah porsi & subtotal produk tunggal
│   ├── 📄 useFilter.js         <-- Logika reactive filter (jarak, harga, dietary alergi)
│   └── 📄 useModal.js          <-- Logika reaktif pop-up (Scan QR, Konfirmasi Checkout)
│
├── 📁 Layouts/                 <-- 🖼️ KERANGKA HALAMAN
│   ├── 📄 GuestLayout.vue      <-- Polos (untuk Splash Screen & Auth Login/Register)
│   ├── 📄 MahasiswaLayout.vue  <-- Navbar Navigasi Bawah ala aplikasi Mobile
│   └── 📄 MitraLayout.vue      <-- Sidebar Kiri & Topbar untuk Admin UMKM
│
├── 📁 Pages/                   <-- 🌍 HALAMAN UTAMA (Dirender oleh Route Laravel)
│   ├── 📄 SplashScreen.vue     <-- Halaman intro awal
│   ├── 📁 Auth/                <-- Login.vue, Register.vue
│   │
│   ├── 📁 Mahasiswa/           <-- (POV 1: Mahasiswa)
│   │   ├── 📄 Beranda.vue      <-- List flash sale terdekat & banner rekomendasi
│   │   ├── 📄 Katalog.vue      <-- Seluruh makanan surplus + filter interaktif
│   │   ├── 📄 DetailProduk.vue <-- Detail 1 menu, pilih porsi, jam pickup
│   │   ├── 📄 Checkout.vue     <-- Form reservasi single-item & review pemesan
│   │   └── 📄 Riwayat.vue      <-- Tab pesanan & tombol lihat tiket QR
│   │
│   └── 📁 Mitra/               <-- (POV 2: Mitra UMKM)
│       ├── 📄 Dashboard.vue    <-- Ringkasan statistik & pesanan aktif hari ini
│       ├── 📄 KelolaMenu.vue   <-- Form kilat tambah surplus & daftar listing aktif
│       └── 📄 Pesanan.vue      <-- Manajemen reservasi masuk & modal scan QR
│
└── 📁 Utils/                   <-- 🛠️ FUNGSI BANTUAN (Pure Helper Functions)
    ├── 📄 formatCurrency.js    <-- Format mata uang (contoh: 15000 -> "Rp 15.000")
    └── 📄 formatDate.js        <-- Format jam operasional (contoh: "18.00 - 21.30 WIB")
```

---

## 5. Keputusan Arsitektur Transaksi: Single-Product vs Multi-Cart

Salah satu keputusan arsitektur terpenting pada PlusFood adalah **meniadakan sistem multi-cart ala Shopee**:

1. **Karakteristik Fisik Self-Pickup:** Mahasiswa mengambil makanan langsung ke outlet fisik mitra. Jika menggunakan multi-cart dari berbagai warung berbeda, mahasiswa akan kebingungan membagi rute penjemputan dalam batas waktu yang sempit (*pickup window*).
2. **Karakteristik Flash Sale Surplus:** Stok makanan sisa umumnya hanya tersisa 1 hingga 5 porsi per toko dan berebut waktu dengan jam tutup toko. Alur checkout 1 produk memungkinkan proses selesai dalam 2-3 klik, mengurangi *abandoned cart* dan perselisihan inventaris.
3. **Fleksibilitas Kuantitas:** Mahasiswa tetap dapat membeli lebih dari 1 porsi (misal 3 porsi untuk teman kosnya) selama porsi sisa dari menu yang sama masih mencukupi.
