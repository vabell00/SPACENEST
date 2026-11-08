# D5 · Rencana Proyek (Scope, WBS, & Backlog Berestimasi)

Dokumen ini memuat batasan cakupan proyek, rincian pembagian kerja (Work Breakdown Structure), serta alokasi sprint pengembangan untuk aplikasi SPACENEST.

## 1. Batasan Proyek (Scope)
* **In-Scope (Dikerjakan):** Modul autentikasi pengguna, modul pelaporan kerusakan fasilitas (unggah foto & titik lokasi), manajemen status oleh admin sarpras, riwayat pelaporan, dan rekapitulasi data.
* **Out-of-Scope (Tidak Dikerjakan di V1):** Peminjaman ruang kelas/jadwal kuliah, integrasi langsung SIAKAD universitas, serta modul manajemen keuangan/pengadaan anggaran perbaikan fisik.

## 2. Work Breakdown Structure (WBS) Sederhana

### Fase 1: Inisiasi & Analisis (Minggu 1–2)
* Penyusunan Team Charter (D1) & Identifikasi Masalah (D2).
* Setup Repositori GitHub & dokumentasi awal.

### Fase 2: Perancangan Sistem & Desain (Minggu 3–4)
* Pembuatan SRS & Product Backlog (D3).
* Perancangan UI/UX Wireframe (Figma) oleh Alif Fitrah Ramadhan.
* Perancangan skema Database & Arsitektur (Laravel/MySQL) oleh Andika Cahya Syahputra.

### Fase 3: Implementasi / Sprint Pengembangan (Minggu 5–10)
* **Sprint 1:** Modul Autentikasi & Form Input Laporan Kerusakan.
* **Sprint 2:** Modul Dashboard Admin, Verifikasi Status, & Riwayat Laporan.

### Fase 4: Pengujian & Evaluasi (Minggu 11–12)
* Pengujian Sistem & User Acceptance Testing (UAT) bersama perwakilan mahasiswa dan admin.
* Finalisasi Laporan Proyek PPL (D1–D9).

## 3. Manajemen Pengkodean & Riwayat Proyek (Git & GitHub)

Untuk menjaga kerapian kode dan memastikan proses pembuatan aplikasi tercatat dengan jelas, proyek SPACENEST dikelola menggunakan sistem kontrol versi **Git** dan platform **GitHub**. Pengerjaan dan penyimpanan kode dibagi ke dalam beberapa tahap yang terstruktur:

* **Pembagian Tahap Pengerjaan (Commit Bertahap):**
  - **Dokumentasi & Log:** Menyimpan seluruh file perencanaan proyek (D1–D5) serta catatan log mingguan di awal.
  - **Database & Konfigurasi:** Menyiapkan struktur tabel database (`database.sql`) beserta file pengatur koneksinya.
  - **Modul Autentikasi:** Membangun fitur masuk (*login*), pendaftaran (*register*), dan keluar akun (*logout*).
  - **Fitur Utama (Pelaporan):** Membuat formulir pengaduan kerusakan sarpras beserta halaman detail laporan.
  - **Dashboard Admin:** Mengembangkan halaman khusus pengelola/admin sarpras untuk verifikasi data dan pengelolaan aset.

* **Penyimpanan di GitHub:**
  - Seluruh riwayat dan file kode dikirimkan secara berkala ke cabang utama (*branch* `main`) pada repositori GitHub (`vabell00/SPACENEST`) agar aman dan terstruktur rapi sesuai urutan pembuatannya.