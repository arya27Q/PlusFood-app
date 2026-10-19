# Dokumentasi PlusFood (Documentation Index)

Selamat datang di repositori dokumentasi resmi sistem **PlusFood**. Dokumentasi ini disusun secara terstruktur untuk memisahkan latar belakang produk, arsitektur teknis, spesifikasi kebutuhan produk (PRD), serta panduan implementasi.

---

## 📑 Daftar Isi Dokumentasi

1. [**01. Latar Belakang & Perjalanan Sistem (`01_latar_belakang_dan_ideasi.md`)**](file:///d:/laragon/www/plusfood-app/docs/01_latar_belakang_dan_ideasi.md)
   - Latar Belakang Masalah (UMKM Kuliner & Mahasiswa Perantauan).
   - Ide Solusi (*SaveBite / PlusFood Marketplace*).
   - Dampak & Tujuan Sistem (*Triple Impact*).

2. [**02. Product Requirements Document (`02_prd.md`)**](file:///d:/laragon/www/plusfood-app/docs/02_prd.md)
   - Informasi Produk & *Target Audience*.
   - *Unique Value Proposition* (UVP).
   - Fitur Utama Pengguna (POV Mahasiswa).
   - Fitur Utama Mitra (POV UMKM).
   - Alur Transaksi Single-Product / Kuota Surplus (*Non-Shopee Multi-Merchant Cart*).
   - Validasi Sistem, QR Validation & *Data Guarding*.

3. [**03. Arsitektur Sistem & Alasan Pemilihan (`03_arsitektur_sistem.md`)**](file:///d:/laragon/www/plusfood-app/docs/03_arsitektur_sistem.md)
   - Mengapa Arsitektur Ini Dipilih (Laravel 11 + Inertia.js + Vue 3).
   - Perbandingan: Monolith Klasik vs SPA Terpisah (API) vs Inertia Monolith Modern.
   - Dual-Persona UX Design (Mahasiswa Mobile-first vs Mitra Desktop Dashboard).
   - Single-Item Reservation Paradigm (*Flash Sale Inventory Lock*).
   - Struktur Direktori Frontend & Backend.

4. [**04. Alur & Panduan Pengguna (`04_user_flow_dan_panduan.md`)**](file:///d:/laragon/www/plusfood-app/docs/04_user_flow_dan_panduan.md)
   - User Journey Mahasiswa (Eksplorasi -> Pilih Jumlah -> Reservasi QR -> Self-Pickup).
   - User Journey UMKM (Input Surplus Kilat -> Pantau Order -> Scan QR Validasi).
   - State & Transition Diagram Pesanan.

5. [**05. Spesifikasi Desain UI & Kebutuhan Figma (`05_spesifikasi_desain_ui.md`)**](file:///d:/laragon/www/plusfood-app/docs/05_spesifikasi_desain_ui.md)
   - Hirarki komponen per layar (Mahasiswa Mobile vs Mitra Desktop).
   - Detail interaksi (Sticky action bar, Eco-packaging toggle, Modal QR Viewfinder).
   - Checklist kebutuhan aset & sinkronisasi Figma.

6. [**06. Skema Database & Relasi Entitas (`06_skema_database_dan_relasi.md`)**](file:///d:/laragon/www/plusfood-app/docs/06_skema_database_dan_relasi.md)
   - Analisis Normalisasi Formal (1NF, 2NF, 3NF) & Strategic Denormalization.
   - Matriks Pemetaan Antarmuka UI/UX (Profil Mahasiswa & Mitra) ke Database.
   - Entity Relationship Diagram (ERD) Mermaid Dual-Perspective (10 Entitas).
   - Kamus Data Detail (*Data Dictionary*), Matriks Foreign Key, & Daftar Migrasi Proyek.

7. [**07. Panduan Git Workflow & Manajemen Kontribusi Tim (`07_panduan_git_workflow_dan_kontribusi.md`)**](file:///d:/laragon/www/plusfood-app/docs/07_panduan_git_workflow_dan_kontribusi.md)
   - Strategi 2 Branch: Branch Utama (`main`) vs Branch Fitur (`feat/...`).
   - Penjelasan Mekanisme Push & Alasan Mengapa Main Tidak Otomatis Ter-update.
   - SOP Praktis: Branching, Commit, Push, Pull Request (PR), dan Merge.
   - Rekam Jejak Kontribusi Anggota Tim untuk Penilaian & Evaluasi Mentor.

8. [**Dokumen Desain Sistem Inti (`desain_sistem_plusfood_2_pov.md`)**](file:///d:/laragon/www/plusfood-app/docs/desain_sistem_plusfood_2_pov.md)
   - 3 Pilar teknologi: LBS, Time-sensitive trigger, dan Gamification engine.
   - Dual-perspective user flow lengkap.
   - Penanganan konkurensi (pessimistic locking) dan token desain.

