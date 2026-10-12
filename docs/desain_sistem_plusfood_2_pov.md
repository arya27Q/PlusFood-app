# Dokumen Desain Sistem (System Design) - PlusFood

**PlusFood (Food Rescue Kampus)** adalah platform web *marketplace* makanan surplus (Two-Sided Marketplace) yang menghubungkan UMKM kuliner di sekitar kawasan kampus dengan mahasiswa perantauan. Sistem ini dirancang untuk mengubah *food waste* menjadi *revenue recovery* bagi UMKM, sekaligus menyediakan jaring pengaman logistik (makanan murah) bagi mahasiswa.

---

## 1. Fondasi Arsitektur & Teknologi Inti

Sistem PlusFood digerakkan oleh tiga teknologi/algoritma inti untuk memastikan transaksi berjalan di jendela waktu yang sempit:

1. **Location-Based Service (LBS) & Spatial Filtering:**
   Sistem menyaring pangkalan data ketersediaan makanan berdasarkan radius terdekat (misal: `< 3 km` atau `15 menit jalan kaki`) dari kampus atau kos mahasiswa.
2. **Time-Sensitive Trigger (Countdown Timer):**
   Setiap *listing* produk terikat pada parameter *Jendela Ambil* (contoh: 18.00 - 21.00 WIB). Sistem akan otomatis menurunkan *listing* yang melewati batas waktu, dan memicu *timer* urgensi di layar mahasiswa.
3. **Impact Tracker & Gamification Engine:**
   Algoritma yang menghitung dampak nyata di kedua sisi secara *real-time*:
   * **Sisi Mahasiswa:** Poin gamifikasi (*Green Points*), total uang yang dihemat (Rp), dan jejak karbon organik yang dicegah (kg CO2e).
   * **Sisi UMKM:** Persentase pemulihan modal (kerugian vs keuntungan dari stok sisa) dan potensi omzet tersisa.

---

## 2. Arsitektur Antarmuka & User Flow: POV Mahasiswa (Konsumen)

Fokus utama desain untuk mahasiswa adalah **Kecepatan Eksplorasi, Kepercayaan (Trust), dan Kebanggaan Lingkungan**.

### A. Onboarding & Discovery
* **Landing/Splash Screen:** Menampilkan *social proof* metrik penyelamatan (contoh: "1.240 porsi diselamatkan pekan ini") dan *value proposition* utama (Diskon hingga 60%, dompet aman).
* **Dashboard Personal:** Menampilkan ringkasan level gamifikasi (misal: "Green Rescuer - Level 3"), rekomendasi makanan berbasis radius ("Dekat Kos Kamu"), dan *Flash Pickup* (warung yang segera tutup).
* **Katalog Split-Layout:** Menggunakan arsitektur 25% panel *Filter* dinamis (Rentang Harga, Jarak, Jendela Jam Ambil, Jaminan Halal) dan 75% grid kartu produk agar mahasiswa dapat memfilter tanpa *page-reload*.

### B. Checkout & Reservasi
* **Multi-step Checkout:** Memecah *cognitive load* menjadi 3 langkah: Ringkasan -> Data Diri & Opsi Wadah -> Pembayaran.
* **Eco-Packaging Options:** Fitur pendorong *Zero Waste*. Jika mahasiswa memilih "Bawa wadah sendiri", biaya tambahan dihilangkan (Gratis), sedangkan "Kotak Kertas" dikenakan biaya tambahan.
* **Smart Alert:** Konfirmasi instan berupa pop-up yang menghasilkan **Kode Tiket QR Code** (misal: SB-4X29A) dan instruksi batas jam ambil.

### C. Penyelesaian (Post-Purchase)
* **Arsip Riwayat Komprehensif:** Dibagi menjadi *tab* logis (Menunggu Pickup, Selesai, Dibatalkan) dengan *header* yang menampilkan ringkasan dampak (Total Porsi, Hemat Rp, Emisi CO2e) selama 1 semester.
* **Sistem Ulasan Berhadiah:** Form ulasan dengan *quick-tags* ("Porsi pas", "Masih hangat") yang memberikan insentif *Green Points* setelah dikirimkan.

---

## 3. Arsitektur Antarmuka & User Flow: POV Mitra UMKM (Merchant)

Fokus utama desain untuk mitra UMKM adalah **Pemulihan Modal Cepat (Revenue Recovery) dan Efisiensi Operasional Kasir**.

### A. Dashboard & Data Visualisasi
* **Hero Banner Metrik Finansial:** Menampilkan "Pendapatan Hari Ini" secara mencolok untuk memberi validasi psikologis bahwa modal harian aman.
* **Grafik Batas Rugi:** Grafik batang pendapatan 7 hari ke belakang yang dilengkapi garis potong *Break-Even* ("vs kerugian Rp 40.000"). Membantu pemilik memvisualisasikan profitabilitas *food rescue*.
* **Status Sesi Real-time:** Menampilkan bar progres stok makanan yang sedang di-diskon beserta sisa waktu *live* (misal: ⏱ 42m).

### B. Manajemen Inventaris Kilat (Listing Surplus)
* **Formulir Terpusat:** Input *listing* dalam satu halaman (Nama, Harga Diskon, Porsi, Jam Ambil, Kebijakan Gerai).
* **Kalkulator Penyelamatan Modal (Smart Assistant):** Fitur interaktif di samping form yang mengkalkulasi selisih antara "Modal per porsi" dengan "Harga Diskon", menunjukkan persentase kerugian modal yang berhasil ditutupi (misal: "100% modal terselamatkan").
* **Inventory List Dinamis:** Tabel daftar makanan aktif dengan status yang berubah secara otomatis (Aktif -> Hampir Habis -> Habis).

### C. Pelaporan & Pembukuan Kasir
* **Riwayat Transaksi:** Tabel komprehensif seluruh transaksi dengan sistem *pagination*.
* **Ekspor Data:** Tombol "Cetak Rekap Kasir" (harian) dan "Ekspor CSV" (bulanan/periodik) untuk mempermudah audit pembukuan eksternal UMKM.

---

## 4. Jembatan Sistem: Validasi Logistik (QR Code Scanner)

Fitur ini adalah titik temu (*Touchpoint*) krusial antara aplikasi mahasiswa dan aplikasi UMKM di dunia nyata untuk mencegah *double-claim*:

1. **Penyiapan Mahasiswa:** Mahasiswa membuka *tab* "Menunggu Pickup" yang menampilkan QR Code dengan *countdown timer* batas ambil.
2. **Pemindaian Kasir:** UMKM menekan tombol *Scan QR Pickup* di dasbor mereka, memunculkan *Modal Pop-up* (kamera pemindai) tanpa berpindah halaman.
3. **Sinkronisasi State:** Saat QR divalidasi, *database* otomatis mengubah status entitas `Orders` menjadi `Selesai`.
4. **Sukses:** Layar UMKM menampilkan pop-up konfirmasi (Nominal diterima & gramasi diselamatkan), dan HP mahasiswa memunculkan notifikasi "Pickup Berhasil".

---

## 5. Entitas Basis Data Utama (ERD Core)

Rancangan tabel basis data relasional untuk mendukung keseluruhan arsitektur di atas:

* **`users` (Mahasiswa):** Menyimpan `id`, `name`, `nim`, `whatsapp`, `email_kampus`, `dietary_preferences` (json), `total_green_points`, `total_money_saved`, `total_co2e_saved`.
* **`merchants` (UMKM):** Menyimpan `id`, `user_id`, `nama_toko`, `latitude`, `longitude`, `alamat_lengkap`, `jam_buka_surplus`, `jam_tutup_surplus`, `kontak_wa`, `is_active`.
* **`surplus_products`:** Menyimpan `id`, `merchant_id`, `nama_makanan`, `kategori`, `foto_url`, `harga_asli`, `harga_diskon`, `stok_sisa`, `mulai_pickup` (time), `batas_pickup` (time), `dietary_tags` (json: halal, vegetarian, nut_free, dll), `status` (aktif, habis, expired).
* **`orders`:** Menyimpan `id`, `order_code` (misal: PF-20261012-001), `user_id`, `product_id`, `merchant_id`, `jumlah_porsi`, `opsi_wadah` (bawa_sendiri / kotak_kertas), `biaya_wadah`, `subtotal`, `total_bayar`, `qr_ticket_code`, `status` (menunggu_pickup, selesai, dibatalkan, kadaluarsa), `verified_at`, `pickup_deadline`.
* **`order_reviews`:** Menyimpan `id`, `order_id`, `rating`, `review_tags` (json), `catatan`, `points_earned`.

---

## 6. Penanganan Konkurensi & Integritas Stok (Concurrency & Race Condition)

Karena sifat *food rescue* adalah *flash sale* dengan sisa stok sangat minim (1-5 porsi), sistem wajib mencegah *overselling* (misal 2 mahasiswa checkout porsi terakhir di detik yang sama):

1. **Pessimistic Locking pada Reservasi:**
   ```php
   // Transaksi atomic Laravel saat mahasiswa menekan Reservasi
   DB::transaction(function () use ($productId, $quantity, $user) {
       $product = SurplusProduct::where('id', $productId)->lockForUpdate()->first();
       
       if ($product->stok_sisa < $quantity) {
           throw new OutOfStockException("Maaf, porsi tersisa sudah habis atau tidak mencukupi.");
       }
       
       $product->decrement('stok_sisa', $quantity);
       if ($product->stok_sisa === 0) {
           $product->update(['status' => 'habis']);
       }
       
       return Order::create([...]);
   });
   ```
2. **Auto-Cancellation & Stock Rollback:**
   Scheduler background (`php artisan orders:expire-unclaimed`) berjalan berkala untuk membatalkan pesanan yang melewati batas waktu tanpa pengambilan fisik, mengembalikan stok jika warung masih dalam jam operasional.

---

## 7. Desain Token & Visual Identity (Design Tokens)

* **Palet Warna:**
  * **Brand Primary:** `#10B981` (Emerald Green - mewakili sustainability & food rescue).
  * **Brand Urgent / Flash:** `#F59E0B` (Amber / Sunset Orange - mewakili countdown & flash sale sore).
  * **Dark Base:** `#0F172A` (Slate 900 - teks kontras tinggi).
  * **Surface Light:** `#F8FAFC` (Slate 50) & `#FFFFFF`.
* **Tipografi:** Sans-serif modern dan legible (`Inter` atau `Plus Jakarta Sans`).
* **Design Pattern:**
  * **Mobile Konsumen:** Bottom Sheet modal, Sticky Bottom Reservation Bar, Tab Pill Badge.
  * **Desktop Mitra:** Stats Grid KPI Card, Data Table responsive, Modal Viewfinder Scanner.