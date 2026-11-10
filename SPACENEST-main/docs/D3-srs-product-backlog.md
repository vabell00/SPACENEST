# D3 · SRS (Software Requirements Specification) & Product Backlog

Dokumen ini merinci kebutuhan fungsional, kebutuhan non-fungsional, serta daftar Product Backlog awal untuk aplikasi pelaporan kerusakan fasilitas SPACENEST.

## 1. Deskripsi Kebutuhan Fungsional (Functional Requirements)
* **FR-01 (Autentikasi Pengguna):** Pengguna (Mahasiswa/Dosen) dapat melakukan registrasi dan login ke sistem, sementara Admin Fakultas memiliki akun hak akses khusus untuk verifikasi laporan.
* **FR-02 (Formulir Pelaporan Kerusakan):** Mahasiswa/Dosen dapat mengisi laporan kerusakan fasilitas dengan mencakup: pilih lokasi/gedung/ruangan, kategori kerusakan, deskripsi teks, dan unggah bukti foto (maksimal 5MB).
* **FR-03 (Daftar & Status Pelaporan):** Pengguna dapat melihat daftar laporan yang pernah dikirim beserta status penanganannya (Menunggu, Diproses, Selesai, Ditolak).
* **FR-04 (Manajemen Status oleh Admin):** Admin Sarpras dapat mengubah status laporan, memberikan catatan tindak lanjut, dan meneruskan informasi ke teknisi via WhatsApp.
* **FR-05 (Rekap & Ekspor Data):** Admin dapat melihat rekapitulasi data kerusakan fasilitas dan melakukan ekspor data laporan (format ringkas) untuk evaluasi pimpinan fakultas.

## 2. Kebutuhan Non-Fungsional (Non-Functional Requirements)
* **NFR-01 (Kompatibilitas & Responsivitas):** Antarmuka web harus responsif dan dapat diakses dengan baik menggunakan layar ponsel dengan lebar minimal 360 px (Tailwind CSS).
* **NFR-02 (Performa Akses):** Waktu muat halaman (*load time*) rata-rata di bawah 3 detik dalam kondisi jaringan standar.
* **NFR-03 (Kemudahan Penggunaan):** Formulir pelaporan dirancang agar dapat diselesaikan oleh pengguna dalam waktu kurang dari 2 menit.
* **NFR-04 (Keamanan & Validasi Berkas):** Sistem menerapkan pembatasan ukuran unggah foto maksimal 5 MB dengan validasi ekstensi berkas yang aman (`jpg`, `jpeg`, `png`).
* **NFR-05 (Keamanan Akses):** Pembatasan hak akses berbasis peran (*Role-Based Access Control* / RBAC) antara Mahasiswa dan Admin Fakultas.

## 3. Product Backlog (Versi Awal)

| ID | Fitur / User Story | Estimasi (Story Points) | Prioritas |
| :--- | :--- | :---: | :---: |
| **PB-01** | Sebagai pengguna, saya dapat masuk (*login*) dan keluar (*logout*) sistem sesuai peran. | 3 | Tinggi |
| **PB-02** | Sebagai mahasiswa, saya dapat membuat laporan kerusakan baru lengkap dengan foto dan titik lokasi. | 5 | Tinggi |
| **PB-03** | Sebagai mahasiswa, saya dapat melacak riwayat dan status laporan yang pernah saya buat. | 3 | Tinggi |
| **PB-04** | Sebagai admin sarpras, saya dapat melihat daftar laporan masuk, memverifikasi, dan mengubah statusnya. | 5 | Tinggi |
| **PB-05** | Sebagai admin sarpras, saya dapat mengekspor rekap data laporan kerusakan fasilitas. | 3 | Sedang |