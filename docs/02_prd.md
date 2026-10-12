# 02. Product Requirements Document (PRD) - PlusFood

---

## 1. Informasi Produk
* **Nama Produk:** PlusFood (SaveBite Marketplace)
* **Kategori:** Marketplace Makanan Surplus (*Food Surplus & Food Waste Reduction*)
* **Platform:** Responsive Web Application (Mobile-Optimized untuk Mahasiswa, Desktop/Tablet Dashboard untuk Mitra UMKM)
* **Target Pengguna:**
  * **Primary User (Konsumen):** Mahasiswa perantauan / anak kos dengan anggaran makan terbatas di area sekitar kampus.
  * **Secondary User (Mitra UMKM):** Pemilik warung makan, kantin kampus, kedai roti, dan UMKM kuliner harian yang memiliki surplus makanan layak konsumsi.

---

## 2. Unique Value Proposition (UVP)
> *"Platform marketplace makanan surplus diskon 60%–80% di sekitar kampus yang memungkinkan mahasiswa melakukan reservasi cepat satu jenis menu secara real-time dan self-pickup langsung ke gerai, sekaligus membantu UMKM memulihkan modal dari potensi food waste."*

---

## 3. Fitur Utama Pengguna (POV 1: Mahasiswa / Konsumen)

### A. Eksplorasi & Katalog Flash Sale Diskon Ekstrem
* Menampilkan kartu produk surplus (*Food Card*) dari warung terdekat secara informatif.
* Menampilkan harga asli dicoret dan harga diskon (diskon 60%-80%).
* Menampilkan sisa porsi yang tersedia serta batas waktu pengambilan (*pickup countdown*).
* Desain layout dinamis (*Split Layout*) untuk filter dan pencarian tanpa reload halaman (*auto-refresh*).

### B. Filter Pencarian Cepat & Spesifik
* **Radius Lokasi:** Filter jarak warung terdekat dari kampus/kos.
* **Rentang Harga:** Slider atau pilihan cepat (contoh: di bawah Rp15.000).
* **Dietary Filter / Pantangan Alergi:** Pilihan filter Halal, Vegetarian, Bebas Kacang, Bebas Gluten, Tanpa Laktosa.

### C. Halaman Detail Produk & Kuota Porsi
* Informasi lengkap menu, deskripsi, peringatan alergen, foto asli, nama toko, alamat toko, dan jam batas pickup.
* Pilihan jumlah porsi (kuantitas) yang ingin dipesan (dibatasi oleh sisa porsi toko saat itu).

### D. Model Checkout Single-Item & Reservasi Kupon Digital
> **PENTING (Perbedaan dengan E-Commerce Konvensional):**
> * PlusFood **TIDAK menggunakan keranjang multi-toko/multi-produk seperti Shopee/Tokopedia**.
> * Karena sistem ini berbasis *flash sale surplus* yang cepat habis dan memiliki jendela waktu *self-pickup* yang ketat di tiap gerai fisik, **transaksi dilakukan per 1 jenis makanan/produk**.
> * Mahasiswa bebas menentukan kuantitas (jumlah porsi) dari 1 produk tersebut sesuai stok yang disediakan oleh UMKM.
* Form data pemesan: Nama (sesuai KTM), Nomor WhatsApp (untuk notifikasi/koordinasi), Email Kampus.
* Rincian subtotal langsung dihitung berdasarkan: `Harga Diskon x Jumlah Porsi`.
* Setelah reservasi dikonfirmasi, sistem menerbitkan **Tiket Digital bertanda QR Code unik** untuk mencegah klaim ganda (*anti double-claim*).

### E. Manajemen Riwayat Pesanan
* Tab navigasi: **Semua**, **Menunggu Pickup** (dengan tombol cepat "Tampilkan QR"), **Selesai**, dan **Dibatalkan**.

---

## 4. Fitur Utama Mitra (POV 2: UMKM / Merchant)

### A. Dashboard Pemantauan (Merchant Dashboard)
* Kartu metrik ringkasan:
  * Total Pesanan Hari Ini
  * Sisa Stok Porsi Aktif
  * Total Pendapatan (*Revenue Recovery*)
* Tabel pesanan masuk real-time: Order ID, Nama Pemesan, Menu, Jumlah Porsi, Batas Jam Pickup, dan Status.

### B. Input Cepat Jual Rugi (Tambah Surplus Kilat < 1 Menit)
* Didesain ringkas agar pemilik warung yang sedang sibuk menutup toko dapat memposting sisa makanan dalam hitungan detik.
* Input fields:
  * Nama Menu
  * Harga Asli & Harga Diskon
  * Jumlah Sisa Porsi (Stok)
  * Jam Pengambilan (Waktu Mulai & Waktu Selesai)
  * Kategori & Dietary Tags (Halal, Veg, Bebas Alergen)
  * Upload Foto Produk

### C. Modal Pemindai QR (*Scan QR Modal*)
* Fitur modal pemindai QR kamera atau input kode reservasi manual untuk memvalidasi tiket mahasiswa saat tiba di toko.
* Sistem memvalidasi keaslian tiket:
  * Jika valid: Status pesanan otomatis berganti menjadi **Selesai (Completed)**, kuota tercatat lunas.
  * Jika sudah pernah di-scan: Peringatan *"Kupon sudah digunakan / Kadaluarsa"*.

### D. Notifikasi Sukses & Rekap Transaksi
* Pop-up notifikasi instan saat makanan sukses diserahkan ke mahasiswa.
* Laporan riwayat transaksi berkala yang dapat difilter berdasarkan bulan dan status.

---

## 5. Aturan Bisnis & Validasi Sistem (*Business Rules & Validation*)

1. **Aturan Single-Item Quantity Flow:**
   * Satu transaksi pesanan terikat pada 1 ID Menu Surplus.
   * Kuantitas yang dapat dipilih pengguna: `1 <= kuantitas <= sisa_stok_tersedia`.
   * Saat checkout dikonfirmasi, stok langsung dipotong (*inventory reservation lock*) untuk mencegah rebutan antar mahasiswa.
2. **Pencegahan Data Kosong (Empty State Guard):**
   * Jika filter lokasi atau dietary tidak menemukan makanan surplus, sistem menampilkan visual ramah *"Makanan Belum Tersedia"* dan tombol *"Reset Filter"*.
3. **Validasi Formulir Ketat (Form Guard):**
   * Tombol "Lanjut Reservasi / Checkout" nonaktif (*disabled*) jika field biodata pemesan belum lengkap.
   * Tombol "Publikasikan Surplus" nonaktif jika harga diskon >= harga asli, atau jika porsi yang diinput <= 0.
4. **Validasi Waktu Pickup:**
   * Jika waktu melewati batas jam pickup dan pesanan belum diambil, sistem memiliki aturan pembatalan otomatis (*auto-cancel / expired*).
