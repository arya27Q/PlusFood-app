# 04. Alur Kerja Pengguna (User Flow) & Panduan Sistem

Dokumen ini memetakan alur interaksi kedua pengguna (*Dual Perspectives*), siklus hidup pesanan (*Order Lifecycle*), serta validasi pengambilan makanan dengan QR Code.

---

## 1. Alur Pengguna: POV Mahasiswa (Konsumen)

```
[Buka Aplikasi]
      │
      ▼
[Eksplorasi / Katalog Makanan Surplus]
  - Filter Radius Lokasi (terdekat)
  - Filter Rentang Harga (< Rp15.000)
  - Filter Pantangan Alergi (Halal, Bebas Kacang, dll)
      │
      ▼
[Halaman Detail 1 Menu Makanan]
  - Membaca deskripsi & jam batas pickup
  - Menentukan Jumlah Porsi yang dipesan (1 hingga sisa stok toko)
      │
      ▼
[Halaman Checkout / Reservasi]
  - Verifikasi Biodata (Nama KTM, WhatsApp, Email Kampus)
  - Kalkulasi Subtotal (Harga Diskon x Kuantitas)
  - Klik "Lanjut Reservasi"
      │
      ▼
[Penerbitan Kupon Digital & QR Code]
  - Stok UMKM otomatis terpotong (Inventory Locked)
  - Tiket QR Code tersimpan di tab "Menunggu Pickup"
      │
      ▼
[Self-Pickup ke Toko Fisik]
  - Mahasiswa mendatangi warung sebelum batas waktu habis
  - Menunjukkan QR Code ke pihak UMKM
      │
      ▼
[Selesai & Menikmati Makanan]
```

---

## 2. Alur Pengguna: POV Mitra UMKM (Merchant)

```
[Buka Dashboard Mitra]
      │
      ▼
[Tambah Surplus Baru (< 1 Menit)]
  - Input Nama Menu, Harga Asli, Harga Diskon
  - Masukkan Sisa Porsi & Rentang Jam Pickup
  - Centang Dietary Tags & Unggah Foto
  - Klik "Publikasikan Listing"
      │
      ▼
[Monitoring Pesanan Masuk (Real-Time)]
  - Notifikasi pesanan reservasi baru masuk
  - Data pemesan: Nama Mahasiswa, Jumlah Porsi, Jam Pickup
      │
      ▼
[Kedatangan Mahasiswa & Validasi]
  - Klik tombol "Scan QR Code" di dashboard
  - Arahkan kamera ke QR Code mahasiswa (atau ketik ID Reservasi)
      │
      ▼
[Sistem Memvalidasi Tiket]
  ├── [VALID]: Status pesanan berubah menjadi 'Selesai'
  │            Rekap pendapatan bertambah otomatis.
  └── [INVALID / DOUBLE-CLAIM]: Peringatan merah tiket telah digunakan.
      │
      ▼
[Serahkan Makanan ke Mahasiswa]
```

---

## 3. Diagram Status Pesanan (Order State Machine)

```
           ┌──────────────┐
           │   PENDING    │ (Reservasi dibuat oleh Mahasiswa,
           │  RESERVATION │  Stok terkunci, QR Code terbit)
           └──────┬───────┘
                  │
        ┌─────────┴─────────┐
        │                   │
        ▼                   ▼
┌──────────────┐    ┌──────────────┐
│  COMPLETED   │    │  CANCELLED / │
│   (PICKED)   │    │   EXPIRED    │
└──────────────┘    └──────────────┘
(QR berhasil di-    (Batas jam pickup lewat
scan oleh UMKM)     atau dibatalkan sistem)
```

---

## 4. Keunggulan Alur Single-Product Quantity

1. **Anti Rebutan Stok (Zero Race-Condition):** Karena proses checkout hanya mengunci 1 item, validasi stok di server berlangsung instan dan minim konflik konkurensi database dibandingkan keranjang belanja multi-item.
2. **Kesesuaian dengan Operasional Lapangan:** Mahasiswa tidak perlu menunggu pesanan toko A dan toko B yang lokasinya berjauhan. Satu tiket QR = satu gerai = satu kali jalan ambil.
3. **Penyajian Data Sederhana bagi Mitra:** Pemilik warung makan cukup melihat: *"Ada pesanan masuk atas nama Budi, 2 porsi Nasi Ayam Bakar, batas jam 21.00"*, lalu menyiapkan 2 bungkus secara presisi.
