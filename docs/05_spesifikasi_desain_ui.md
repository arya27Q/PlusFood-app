# Panduan Spesifikasi Desain UI (UI Wireframe & Design Specification)

Dokumen ini memetakan seluruh komponen visual, susunan elemen per layar (*screen hierarchy*), serta kebutuhan aset visual untuk mempermudah pengerjaan frontend Vue 3 maupun sinkronisasi dengan desain Figma.

---

## 1. Komponen & Layar POV Mahasiswa (Konsumen / Mobile-First)

### A. Layar Beranda / Discovery (`Pages/Mahasiswa/Beranda.vue`)
* **Top Header:**
  * Greeting ("Halo, [Nama Mahasiswa]! 👋")
  * Metrik Gamifikasi Ringkas: Badge Level Rescuer & Tabungan Emisi (misal: "🌱 2.4 kg CO2e dicegah")
* **Location & Radius Selector:**
  * Dropdown/Modal radius: "Kampus Utama - Radius 2 km"
* **Banner Flash Urgensi:**
  * Highlight warung yang jendela ambilnya tinggal `< 30 menit` lagi dengan tag diskon mencolok ("Diskon 70%").
* **Filter Bar Cepat (Horizontal Scroll):**
  * Tombol pill: Semua, Makanan Berat (Nasi), Roti/Pastry, Halal Only, Bebas Kacang, < Rp 15.000.
* **Katalog Kartu Produk (`FoodCard.vue`):**
  * Foto produk dengan badge diskon ("-70%") di pojok kiri atas.
  * Tag waktu hitung mundur (*Countdown pill*): "⏱ Sisa 45 menit".
  * Nama makanan & Nama Warung/Kantin.
  * Harga Asli (coret merah/abu) vs Harga Diskon (hijau bold).
  * Indikator sisa porsi: "Tersisa 2 porsi lagi".

### B. Layar Detail Produk (`Pages/Mahasiswa/DetailProduk.vue`)
* Gambar hero besar dengan overlay tombol bookmark & tombol back.
* Informasi Toko: Nama Mitra, Alamat, Jarak (misal: "800m dari lokasimu"), Jam Buka Surplus.
* Dietary & Allergen Badges: Ikon Halal, Bebas Gluten, Vegan, dll.
* Deskripsi Makanan & Kondisi: Penjelasan menu dan jaminan kelayakan konsumsi hari ini.
* **Sticky Bottom Action Bar:**
  * Pilihan Kuantitas (`-` `[ Jumlah ]` `+`) dibatasi oleh stok sisa.
  * Total Harga dinamis (`Harga x Kuantitas`).
  * Tombol CTA: "Reservasi Sekarang".

### C. Layar Checkout & Reservasi (`Pages/Mahasiswa/Checkout.vue`)
* **Step 1: Ringkasan Pesanan Single-Item:**
  * Nama produk, nama mitra, jumlah porsi, jam batas ambil.
* **Step 2: Opsi Wadah Ramah Lingkungan (*Eco-Packaging*):**
  * Radio button: "Bawa Wadah Sendiri (Gratis / +10 Green Points)" vs "Kotak Kertas (+Rp 2.000)".
* **Step 3: Data Pemesan (Otomatis terisi dari profil):**
  * Nama Mahasiswa, NIM, Nomor WhatsApp aktif.
* **Step 4: Rincian Pembayaran:**
  * Subtotal porsi + Biaya wadah (jika ada).
* Tombol CTA: "Konfirmasi & Kunci Reservasi".

### D. Layar Tiket & Riwayat Pesanan (`Pages/Mahasiswa/Riwayat.vue`)
* Tab: "Menunggu Pickup", "Selesai", "Dibatalkan".
* **Kartu Tiket Digital:**
  * QR Code unik (ditampilkan besar saat kartu diklik).
  * Kode Pesanan (misal: `PF-88219`).
  * Instruksi penjemputan: "Tunjukkan QR ini ke kasir sebelum pukul 20.30 WIB".
  * Tombol Google Maps: "Petunjuk Arah ke Warung".

---

## 2. Komponen & Layar POV Mitra UMKM (Merchant / Desktop Dashboard)

### A. Layar Dashboard Utama (`Pages/Mitra/Dashboard.vue`)
* **KPI Metric Cards (Grid 3 Kolom):**
  1. *Pendapatan Hari Ini*: Total Rupiah dari porsi surplus yang terjual.
  2. *Porsi Terselamatkan*: Jumlah porsi yang terambil vs total listing.
  3. *Tingkat Pemulihan Modal*: Persentase modal yang berhasil diselamatkan (*Break-Even Tracker*).
* **Tabel Pesanan Masuk (Real-Time Live Feed):**
  * Kolom: Order ID, Nama Mahasiswa, Menu, Jumlah Porsi, Batas Ambil, Status (Menunggu Pickup), Aksi (Tombol Validasi Manual / Scan QR).
* **Floating/Header Action:**
  * Tombol Utama: "📸 Buka Scanner QR" & "➕ Tambah Surplus Kilat".

### B. Layar Kelola Menu Surplus (`Pages/Mitra/KelolaMenu.vue`)
* **Form Kilat Input Surplus (< 1 Menit):**
  * Nama Menu (Autocomplete dari riwayat sebelumnya jika ada).
  * Harga Asli vs Harga Diskon (Sistem otomatis menampilkan persentase diskon & kalkulasi recovery).
  * Sisa Porsi (Input number).
  * Rentang Waktu Ambil (Waktu Mulai s/d Waktu Selesai).
  * Dietary Checkboxes (Halal, Pedas, Kacang, Telur, dll).
  * Upload Foto / Gunakan Foto Default Kategori.
* **Daftar Listing Aktif:**
  * Card / List makanan yang sedang tayang hari ini dengan toggle on/off instan.

### C. Modal Scanner QR (`Components/Mitra/ScanQrModal.vue`)
* Jendela kamera aktif dengan kotak bidik (*viewfinder overlay*).
* Opsi alternatif: Input manual kode reservasi 6 karakter jika kamera bermasalah.
* Layar Hasil Scan Instan:
  * Suara beep & visual hijau centang: "Pesanan Sukses Divalidasi!".
  * Rincian: Nama mahasiswa, jumlah porsi yang harus diserahkan.

---

## 3. Checklist Aset yang Dibutuhkan dari Desain Figma

Untuk memastikan hasil implementasi frontend (Vue + Tailwind) 100% presisi (*pixel-perfect*), berikut hal-hal yang dapat kamu bagikan dari Figma:

1. **Screenshot / Ekspor Gambar UI Figma:**
   * Layar Beranda Mahasiswa & Kartu Produk.
   * Layar Detail & Modal Reservasi / Checkout.
   * Layar Dashboard Mitra & Modal Scan QR.
2. **Design Tokens & Asset Ekspor:**
   * Kode Hex warna spesifik (jika ada warna sekunder/aksen khusus).
   * Logo PlusFood (SVG atau PNG transparan).
   * Ikon spesifik (misal: ikon eco-packaging, dietary badges, bottom nav icons).
3. **Link Figma (Opsional):**
   * Jika ada link file Figma publik / screenshot frame utamanya, itu sangat mempercepat penyesuaian CSS dan layout.
