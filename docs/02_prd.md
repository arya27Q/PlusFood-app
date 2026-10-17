# 02. Product Requirements Document (PRD) - PlusFood

* **Nama Produk:** PlusFood (SaveBite Marketplace)
* **Kategori:** Marketplace Makanan Surplus (*Food Surplus & Food Waste Reduction*)
* **Modul Terkait:** Modul 5 (Canteen Pre-Order & Express Checkout / QR Pickup)
* **Platform:** Responsive Web Application (Mobile-Optimized untuk Mahasiswa, Desktop/Tablet Dashboard untuk Mitra UMKM)
* **Tautan Repositori GitHub:** [https://github.com/arya27Q/PlusFood-app](https://github.com/arya27Q/PlusFood-app)
* **Tautan Figma Prototype:** `[Figma Design - Modul 5 Canteen Pre-Order & Checkout]`

---

## 1. Konteks Permasalahan (Problem Context)

1. **Sisi Mahasiswa (Konsumen):**
   * Mahasiswa sering melewatkan jam makan siang atau makan malam karena antrean kantin kampus yang sangat panjang saat jeda kelas yang singkat (hanya 15–20 menit).
   * Proses pembayaran tunai di kasir memakan waktu lama karena antrean kembalian uang pecahan kecil (*small change shortage*).
   * Mahasiswa perantauan/anak kos memiliki anggaran makan harian terbatas dan membutuhkan alternatif makanan layak konsumsi dengan harga terjangkau di akhir bulan.

2. **Sisi Mitra UMKM (Kantin / Warung Kampus):**
   * Setiap hari warung makan, kantin, dan kedai roti di sekitar kampus mengalami kerugian finansial akibat sisa makanan matang/surplus yang tidak terjual (rata-rata Rp40.000 – Rp150.000/hari).
   * Makanan layak konsumsi tersebut terancam dibuang (*food waste*) jika tidak diselamatkan sebelum jam tutup operasional.
   * Pemilik warung tidak memiliki saluran penjualan kilat digital yang mudah digunakan saat sibuk membereskan gerai di malam hari.

---

## 2. Target Persona & Log Wawancara Pengguna Nyata

### A. Rekapitulasi Log Wawancara Lapangan (Kolaborasi Tim)
Wawancara pengguna dilakukan secara kolaboratif oleh seluruh anggota tim dengan pembagian peran terstruktur (*Lead Interviewer & Co-Interviewer / Note-Taker*):

| Sesi | Target Persona | Narasumber & Lokasi | Waktu Sesi | Tim Pewawancara & Peran | Temuan Kunci Lapangan |
|:---:|:---|:---|:---:|:---|:---|
| **#1** | **Merchant UMKM** | Mas Danu (Owner Gerai Makanan di Kantin Greenwood UNESA Ketintang) | 14 Okt 2026 | • **Pewawancara Utama:** Arya Pratama<br>• **Notulis & Perekam:** Muhammad Mirza Fatih | Kerugian Rp40.000/outlet/minggu dari sisa stok; obral IG Story tidak efektif menjangkau mahasiswa. |
| **#2** | **Konsumen 1 (Budget Disiplin)** | Agus / Affan (Mahasiswa Kos Perantauan) | 15 Okt 2026 | • **Pewawancara Utama:** Arya Pratama<br>• **Dokumentator:** Muhammad Mirza Fatih | Uang makan harian dibatasi Rp20.000; membuang 15-30 menit keliling dan canggung datang saat warung mau tutup. |
| **#3** | **Konsumen 2 (Porsi Berat Nasi)** | Riko / Raka (Mahasiswa S1 UNESA) | 15 Okt 2026 | • **Pewawancara:** Muhammad Mirza Fatih<br>• **Pendamping Teknis:** Arya Pratama | Budget makan nasi Rp11.000–Rp15.000; frustrasi keliling warung yang ternyata sudah habis; terpaksa tidur lapar. |

---

### B. Profil Detail User Persona

### Persona 1: Mas Danu (Persona Penjual / Merchant UMKM)
* **Profil:** Pemuda berusia 24 tahun, pemilik UMKM makanan ringan & olahan harian. Mengelola 3 titik gerai: *Airlangga Food Point Kampus B*, *Kantin Srikandi RSUD Dr. Soetomo*, dan *Kantin Greenwood UNESA Ketintang*, serta aktif di bazar/Car Free Day.
* **Masalah & Frustrasi (*Pain Points*):**
  * Bisnis menanggung kerugian sekitar **Rp40.000 per outlet setiap minggunya** akibat bahan baku dan sisa makanan yang tidak habis terjual.
  * Proses pengecekan sisa stok fisik secara manual menjelang jam tutup toko memakan waktu dan merepotkan.
  * Obral diskon manual lewat Instagram Story dan TikTok kurang efektif karena pengikut terbatas (1.000 – 1.500 followers) sehingga informasi diskon terlambat sampai ke mahasiswa sekitar kampus.
* **Solusi Fitur di PlusFood:**
  * Fitur **Tambah Surplus Kilat (< 1 Menit)** tanpa tergantung algoritma/follower media sosial.
  * Dashboard **Break-Even Tracker** untuk memantau pemulihan kerugian Rp40.000/minggu secara langsung.
  * Validasi pengambilan pesanan kilat via **Scan QR Modal** untuk mencegah klaim ganda.

### Persona 2: Agus / Affan (Persona Konsumen 1 - Sangat Disiplin Budget)
* **Profil:** Mahasiswa perantauan/anak kos (usia 19–22 tahun). Sangat disiplin membatasi uang jajan hariannya maksimal **Rp20.000 per hari** sejak awal bulan agar uang saku tidak cepat menipis.
* **Masalah & Frustrasi (*Pain Points*):**
  * Sering kesulitan menemukan tempat makan yang sesuai budget pada malam hari, terpaksa membuang waktu 15–30 menit berkeliling mencari warung.
  * Merasa canggung dan tidak enak hati merepotkan pemilik warung jika datang saat warung sudah bersiap tutup atau makanan tinggal sedikit.
  * Jika gagal menemukan makanan murah saat berkeliling, terpaksa pulang dan hanya mengandalkan mi instan di kamar kos.
* **Solusi Fitur di PlusFood:**
  * **Interactive Price Slider Filter (Rp5.000 – Rp25.000)** untuk menemukan porsi hemat di bawah Rp15.000 secara instan.
  * **Sistem Reservasi & Tiket QR Digital:** Agus mendapatkan kepastian porsi dan tidak perlu merasa canggung lagi karena transaksi sudah sah terikat di sistem.

### Persona 3: Riko / Raka (Persona Konsumen 2 - Fokus Makan Porsi Berat)
* **Profil:** Mahasiswa perantauan di sekitar Universitas Negeri Surabaya (UNESA) dengan uang saku Rp500.000 per dua minggu. Mengalokasikan budget khusus makan malam porsi berat (seperti nasi) sekitar **Rp11.000 hingga Rp15.000** sekali makan.
* **Masalah & Frustrasi (*Pain Points*):**
  * Frustrasi jika ada pengeluaran tak terduga yang memotong jatah makan mingguan.
  * Menghabiskan waktu 10–30 menit keliling tanpa kepastian apakah warung favorit masih buka dan masih memiliki stok.
  * Jika kehabisan makanan yang cocok, ia terpaksa makan sosis frozen, mi instan, atau bahkan memilih langsung tidur menahan lapar.
* **Solusi Fitur di PlusFood:**
  * **Filter Kategori Cepat (Nasi, Mie, Soto)** untuk menemukan makanan berat berkarbohidrat dengan diskon 50%–70% (rentang harga Rp9.000 – Rp14.000).
  * **Live Stock Count & Inventory Reservation Lock:** Menampilkan sisa porsi transparan ("Sisa 3 porsi") dan langsung mengunci stok saat checkout sehingga Riko mendapat kepastian makan tanpa khawatir kehabisan.


---

## 3. Batasan Fitur (Scope of Work)

### A. In-Scope (Fitur Fase Prototipe MVP)
Fitur-fitur inti yang dikembangkan dan berfungsi pada fase rilis ini:

1. **POV Mahasiswa (Mobile-First):**
   * **Katalog Surplus Dinamis:** Eksplorasi daftar menu surplus dengan diskon 50%–70%, badge sisa waktu (*countdown*), dan sisa porsi.
   * **Collapsible Filter & Pencarian:** Filter rentang harga slider, pilihan kategori cepat (Nasi, Mie, Soto, Roti, dll), pilihan radius kampus (500m - 3km), serta preferensi diet (Halal, Vegetarian, Bebas Alergen).
   * **Detail Produk & Single-Item Flow:** Tampilan spesifikasi menu, jam operasional pickup, dan selector jumlah kuantitas porsi sesuai batas stok gerai.
   * **Checkout & QR Payment Simulation:** Form pemesan singkat (Nama, WhatsApp, Email), ringkasan harga hemat, pemilihan metode bayar (GoPay, OVO, Dana, QRIS, Tunai di Kasir), dan *countdown timer* pembayaran 5 menit.
   * **Digital Pickup Token (QR Token):** Penerbitan kode QR unik dan token 3-digit setelah konfirmasi pesanan untuk ditunjukkan di kasir toko.
   * **Riwayat Pesanan & Filter Status:** Tab navigasi pesanan (Semua, Menunggu Pickup, Selesai, Dibatalkan) dengan tombol aksi cepat *Tampilkan QR*.
   * **Mobile Bottom Navigation:** Navigasi 5 menu responsif di layar ponsel pintar.

2. **POV Mitra UMKM (Desktop & Tablet Dashboard + Mobile Access):**
   * **Dashboard Metrik Kasir:** Pantauan pendapatan harian (*Revenue Recovery*), porsi terselamatkan, dan indikator *Break-Even Tracker*.
   * **Collapsible Sidebar Navigation:** Navigasi modern yang bisa diperkecil (80px) atau diperluas (260px) tanpa merusak kerapian tabel data.
   * **Modal Tambah Surplus Kilat (< 1 Menit):** Form cepat pengisian nama menu, harga asli, harga diskon, kuota porsi, rentang jam pickup, tag dietary, dan foto makanan.
   * **Modal Pemindai QR (*Scan QR Modal*):** Simulasi pemindaian QR kamera dan input kode tiket manual untuk memvalidasi pengambilan makanan seketika.
   * **Tabel Pesanan Masuk (Real-time Simulation):** Daftar pesanan dengan informasi pemesan, status pembayaran, jam pengambilan, dan aksi verifikasi dengan dukungan scroll horizontal aman (*overflow-x-auto*).
   * **Profil Gerai & Status Buka/Tutup Toko:** Manajemen identitas warung, jadwal sesi pickup harian, dan ringkasan metrik dampak lingkungan (CO2 dicegah).

### B. Out-of-Scope (Fase Selanjutnya / Non-MVP)
Fitur-fitur yang sengaja **tidak** diimplementasikan pada fase prototipe saat ini untuk menjaga fokus penyelesaian tepat waktu:
1. **Live Payment Gateway Production:** Integrasi langsung dengan API bank/Midtrans/Xendit live dengan pemotongan saldo nyata (pada MVP menggunakan simulasi *instant clear* dan *e-wallet dummy*).
2. **Layanan Pengiriman Kurir / Ojek Online:** Aplikasi murni berfokus pada sistem *Self-Pickup* (ambil sendiri di gerai kantin); tidak menyediakan pelacakan armada kurir bermotor.
3. **In-App Real-time Chatting:** Komunikasi pemesan dan penjual dialihkan menggunakan nomor WhatsApp yang tertera pada tiket atau langsung tatap muka di kasir.
4. **Sistem Multi-Merchant Cart:** PlusFood menggunakan paradigma *Single-Item Flash Sale Reservation* (1 menu per transaksi) untuk mencegah rebutan stok dan kompleksitas pickup beda lokasi gerai.
5. **Autentikasi Biometrik & Integrasi SSO Kampus Terpusat:** Menggunakan autentikasi berbasis email/password standar dan local state auth.

---

## 4. User Stories

| No | Sebagai (*As a*) | Saya Ingin (*I want to*) | Agar (*So that*) | Kriteria Penerimaan (*Acceptance Criteria*) |
|:---|:---|:---|:---|:---|
| **US-01** | Mahasiswa | Melihat katalog makanan surplus diskon di sekitar kampus | Saya bisa mendapatkan makanan lezat dan murah di akhir bulan | Menampilkan foto, harga coret, harga diskon, sisa porsi, dan countdown timer |
| **US-02** | Mahasiswa | Memfilter menu berdasarkan rentang harga, jarak, dan dietary halal | Saya menemukan makanan yang sesuai dengan budget dan preferensi makan saya | Filter bereaksi instan tanpa reload halaman; menampilkan empty state jika data tidak cocok |
| **US-03** | Mahasiswa | Memesan makanan secara pre-order dan membayar lewat e-wallet | Saya tidak perlu mengantre lama di kasir saat jeda kelas yang hanya 15 menit | Menghasilkan kode QR tiket dan token pengambilan 3 digit setelah checkout |
| **US-04** | Mahasiswa | Membuka tiket QR dari menu Riwayat Pesanan di ponsel | Saya dapat langsung menunjukkannya ke staf kantin saat tiba di toko | Tiket dapat diakses 1-klik dari tab "Menunggu Pickup" |
| **US-05** | Mitra UMKM | Mengunggah menu sisa harian dalam waktu kurang dari 1 menit | Saya tidak kehilangan waktu berharga saat sedang membersihkan warung di malam hari | Modal popup "Tambah Surplus" hanya berisi field esensial dengan validasi otomatis |
| **US-06** | Mitra UMKM | Memindai QR code atau memasukkan kode tiket mahasiswa | Pesanan terverifikasi sah dan terhindar dari klaim ganda (*anti double-claim*) | Sistem memvalidasi tiket seketika, mengubah status jadi "Selesai", dan memperbarui grafik pendapatan |
| **US-07** | Mitra UMKM | Melihat total pendapatan pemulihan modal dan porsi terselamatkan | Saya mengetahui efektivitas pengurangan sampah makanan di toko saya | Dashboard menampilkan kartu metrik Rp, porsi, dan grafik pemulihan modal mingguan |

---

## 5. Kriteria Kelulusan & Metrik Keberhasilan (Success Metrics)

Keberhasilan implementasi prototipe PlusFood dievaluasi berdasarkan indikator berikut:

1. **Metrik Interaktivitas & Performa UI:**
   * **Waktu Alur Reservasi Mahasiswa:** Pengguna dapat menyelesaikan alur dari memilih makanan hingga mendapatkan tiket QR dalam waktu `< 60 detik`.
   * **Waktu Listing Menu Surplus Mitra:** Pengisian modal *Tambah Surplus Kilat* dapat diselesaikan dalam waktu `< 60 detik`.
   * **Kecepatan Validasi Tiket Kasir:** Pemindaian QR / input kode tiket manual memvalidasi status pesanan dalam waktu `< 3 detik`.
2. **Kepatuhan Responsif & Bebas Bug:**
   * **100% Responsif Multi-Device:** Tampilan berfungsi optimal pada layar mobile (360px – 390px) dan desktop (1280px – 1440px) tanpa *horizontal overflow* (`document.body.scrollWidth === window.innerWidth`).
   * **Build Frontend Bersih:** Kompilasi aset Vite (`npm run build`) berjalan dengan `0 errors` dan `0 warnings`.
3. **Kepatuhan Komponen Interaktif (Rubrik Penugasan):**
   * Memiliki lebih dari 2 komponen interaktif yang berfungsi:
     1. Menu hamburger & drawer off-canvas mobile.
     2. Sidebar desktop collapsible (buka-tutup transisi halus).
     3. Modal interaktif Scan QR & input token manual.
     4. Modal interaktif Tambah Menu Surplus Kilat.
     5. Filter interaktif multi-kriteria (slider harga, filter kategori pill, filter diet, radio radius).
     6. Collapsible filter accordion pada halaman Katalog mobile.
     7. Tab filter status riwayat pesanan (Semua, Menunggu Pickup, Selesai, Dibatalkan).

---

## 6. Aturan Bisnis & Validasi Sistem (*Business Rules & Validation*)

1. **Aturan Single-Item Quantity Flow:**
   * Satu transaksi pesanan terikat pada 1 ID Menu Surplus.
   * Kuantitas yang dapat dipilih pengguna: `1 <= kuantitas <= sisa_stok_tersedia`.
   * Saat checkout dikonfirmasi, stok langsung dipotong (*inventory reservation lock*) untuk mencegah perebutan porsi antar mahasiswa.
2. **Pencegahan Data Kosong (Empty State Guard):**
   * Jika filter lokasi atau dietary tidak menemukan makanan surplus, sistem menampilkan visual ramah *"Makanan Belum Tersedia"* dan tombol *"Reset Filter"*.
3. **Validasi Formulir Ketat (Form Guard):**
   * Tombol "Konfirmasi Reservasi" nonaktif (*disabled*) jika field biodata pemesan belum lengkap.
   * Tombol "Publikasikan Surplus" nonaktif jika harga diskon >= harga asli, atau jika porsi yang diinput <= 0.
4. **Validasi Tiket dan Batas Waktu Pickup:**
   * Kupon QR hanya dapat divalidasi satu kali (*single-use token*). Upaya pemindaian ulang pada tiket yang sama akan ditolak sistem.
   * Setiap reservasi memiliki batas waktu pengambilan; sistem menampilkan indikator peringatan jika waktu pengambilan telah terlewat.

