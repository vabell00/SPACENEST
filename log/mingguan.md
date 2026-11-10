# Catatan Log Mingguan SPACENEST

---

## MINGGU 1: Transisi Repositori Solo ke Repositori Tim & Finalisasi Environment

* **Tanggal Execution:** 9 November 2026
* **Penanggung Jawab / PM:** Vabellian Naoni
* **Target Utama:** Mengubah proyek SPACENEST format ZIP menjadi repositori GitHub terstruktur (`vabell00/SPACENEST`), menguji skema database lokal, menetapkan SOP kolaborasi, dan memastikan kelayakan repositori sebelum mengundang anggota tim.

---

### 1. Rincian Kronologi & Aktivitas Teknis (What Was Done)

#### A. Restrukturisasi & Manajemen Repositori (Git Hygiene)
1. **Penerapan `.gitignore`:** 
   - Membuat file `.gitignore` di root proyek untuk menyaring file sampah bawaan OS (`Thumbs.db`, `.DS_Store`, `desktop.ini`), file konfigurasi lokal editor (`.vscode/`, `.idea/`), serta file log/cache (`*.log`).
   - Verifikasi kebersihan repositori agar tidak ada konfigurasi pribadi atau file temporary yang mengotori GitHub.
2. **Sinkronisasi & Perbaikan Dokumen Remote:**
   - Mengisi dan menyinkronkan file SOP kolaborasi `docs/panduan-kerja.md` (awalnya ter-push 0 bytes, berhasil dilakukan re-commit dan push hingga utuh 109 lines / 5.35 KB).
   - Memperbarui panduan instalasi lokal pada `README.md`.

#### B. Standar Operasional Prosedur (SOP) & Kolaborasi Tim
1. **Penetapan Dokumentasi Kolaborasi (`docs/panduan-kerja.md`):**
   - **Rapat & Diskusi:** Penetapan alur rapat fleksibel (via WhatsApp / Tatap Muka Kampus), format 3 pertanyaan kunci (What's Done, Blockers, Next Plan), dan kewajiban pengisian log mingguan.
   - **GitHub Issues:** Penetapan alur kerja berbasis *Issue-Driven Development*, pembuatan label tugas, penunjukan *Assignee*, dan terminasi otomatis Issue via pesan commit (`closes #issue_id`).
   - **Git Branching & Push SOP:** Penetapan standar commit message (`feat:`, `fix:`, `docs:`, `chore:`), serta prosedur `git pull` sebelum `git push` untuk menghindari conflict.
   - **Simulasi Konkret Workflow:** Membuat draf simulasi langkah demi langkah dari pengerjaan Issue oleh Lead Dev (Andika) hingga pengujian/dokumentasi oleh PM/QA.

#### C. Pengujian Database & Environment Lokal (XAMPP)
1. **Identifikasi Skema Database:**
   - Memeriksa file koneksi database pada folder `config/` (`config/koneksi.php`) untuk mengetahui variabel koneksi yang digunakan.
   - Mengidentifikasi nama database resmi proyek: **`spacenest_db`**.
2. **Import & Verification Testing:**
   - Menjalankan Service Apache & MySQL pada XAMPP Control Panel.
   - Membuat database `spacenest_db` via phpMyAdmin (`http://localhost/phpmyadmin`).
   - Mengimpor file `database.sql` ke dalam database `spacenest_db`.
   - **Hasil Uji:** Mengetes alur registrasi akun (`admin_register.php` / `register.php`) dan alur login (`login.php` / `index.php`). Aplikasi **100% sukses terhubung** dan data berhasil masuk/terbaca tanpa error.

---

### 2. Inventarisasi Status Dokumen Proyek

| Kode Dokumen | Nama File | Status | Fungsi Utama |
| :--- | :--- | :---: | :--- |
| **D1** | `docs/D1-team-charter.md` | **Siap** | Struktur peran tim (Vabellian: PM, Andika: Lead Dev, Alif: UI/UX/QA) |
| **D2** | `docs/D2-identifikasi-masalah.md` | **Siap** | Latar belakang masalah & landasan proyek SPACENEST |
| **D3** | `docs/D3-srs-product-backlog.md` | **Siap** | Spesifikasi kebutuhan software & backlog fitur |
| **D4** | `docs/D4-proposal-proyek.md` | **Siap** | Proposal formal perencanaan proyek |
| **D5** | `docs/D5-rencana-proyek.md` | **Siap** | Timeline, milestone, & alokasi resource |
| **SOP Tim** | `docs/panduan-kerja.md` | **Siap** | Panduan rapat, pengkodean, Git workflow & simulasi |
| **Root** | `.gitignore` | **Siap** | Filtering file temporary & OS |
| **Root** | `README.md` | **Siap** | Panduan setup cepat `spacenest_db` bagi anggota tim |
| **Log** | `log/mingguan.md` | **Aktif** | Catatan rekam jejak mingguan proyek |

---

### 3. Kendala, Analisis Error & Solusi (Troubleshooting Log)

1. **Kendala File Kosong (0 Bytes) di GitHub:**
   - *Masalah:* File `docs/panduan-kerja.md` ter-push ke remote repository dalam kondisi belum tersimpan (*0 bytes*).
   - *Penyebab:* Eksekusi `git add` & `git commit` dilakukan sebelum teks di-paste dan di-save (`Ctrl+S`) pada VS Code.
   - *Solusi:* Melakukan penyalinan ulang teks Markdown lengkap ke VS Code, simpan file, lalu jalankan `git add`, `git commit -m "docs: mengisi kembali teks panduan kerja tim yang kosong"`, dan `git push origin main`. Hasil terverifikasi 5.35 KB di GitHub.

2. **Ketidakpastian Nama Database Target:**
   - *Masalah:* Database belum diimpor ke XAMPP lokal dan nama database belum terkonfirmasi.
   - *Penyebab:* Proyek baru saja diekstrak dari format ZIP solo-developer.
   - *Solusi:* Melakukan inspeksi skrip koneksi pada folder `config/`, menemukan variabel `$db = "spacenest_db"`, lalu membuat database dengan nama yang persis di phpMyAdmin sebelum mengimpor `database.sql`.

---

### 4. Rencana Tindak Lanjut (Next Action Items)

- [ ] **Onboarding Anggota Tim:**
  - Mengundang `Andika Cahya Syahputra` dan `Alif Fitrah Ramadhan` sebagai Collaborator di repositori GitHub `vabell00/SPACENEST` (*Settings > Collaborators*).
  - Memastikan kedua anggota menerima undangan email/notifikasi GitHub.
- [ ] **Setup Laptop Anggota:**
  - Meminta Andika dan Alif melakukan `git clone https://github.com/vabell00/SPACENEST.git`.
  - Meminta mereka mengimpor `database.sql` ke `spacenest_db` di XAMPP masing-masing sesuai panduan `README.md`.
- [ ] **Manajemen Tugas Pertama (Sprint 1):**
  - Membuat **Issue #1** (Pengembangan Fitur/UI Utama oleh Andika) dan **Issue #2** (Perancangan Asset UI/Pengujian oleh Alif) pada tab *Issues* GitHub.

---

## MINGGU 2: Integrasi Tim, Perbaikan Struktur Root, & Sinkronisasi Environment

* **Tanggal Execution:** 10 November 2026
* **Penanggung Jawab / PM:** Vabellian Naoni
* **Target Utama:** Merapikan direktori proyek ke root directory, mengimplementasikan workflow Feature Branching & Pull Request (PR) untuk redesign UI autentikasi, menyinkronkan file database `spacenest_db`, serta memastikan seluruh lingkungan XAMPP lokal terhubung lancar di branch `main`.

---

### 1. Rincian Kronologi & Aktivitas Teknis (What Was Done)

#### A. Restrukturisasi Direktori Proyek (`fix` / `refactor`)
1. **Eliminasi Subfolder Ganda (`ed99373`):**
   - Memindahkan seluruh file utama (`index.php`, `login.php`, `register.php`, `dashboard.php`, `admin_dashboard.php`, `database.sql`, dll.) dari dalam subfolder ganda (`SPACENEST-main`) langsung ke *root directory* repositori.
   - Menyesuaikan struktur folder `config/`, `includes/`, `assets/`, `docs/`, dan `log/` agar dapat diakses tanpa hambatan oleh Web Server Apache XAMPP.

#### B. Redesign UI Autentikasi & Git Workflow (`style` / `feat`)
1. **Pembaruan Halaman Login & Register (`a5895f4`):**
   - Mengubah skema warna dan tata letak antarmuka `login.php` dan `register.php` agar lebih modern dan sesuai standar visual SPACENEST.
2. **Penerapan Feature Branching & Pull Request (PR) Workflow:**
   - **Feature Branching (`1-pembaruan-skema-warna-tampilan-halaman-login-redesign-pewarnaam`):** Mengisolasi pengerjaan fitur/redesign tampilan ke dalam cabang terpisah agar perubahan yang masih diuji coba tidak mengganggu kestabilan kode di branch utama (`main`).
   - **Pull Request #2 & #3 (`aafb6aa` & `780156b`):** Pengajuan izin penggabungan kode dari feature branch ke branch `main`. Melalui proses *code review* dan verifikasi bersama, PR disetujui dan di-merge agar fitur baru resmi masuk ke kode utama proyek.
   - **Remote & Local Sync (`4dd5292` & `63dc85e`):** Melakukan sinkronisasi riwayat commit antara GitHub remote dan XAMPP lokal agar lingkungan pengembangan kedua pengembang (`vabell00` dan `cahya01`) tetap identik.

#### C. Sinkronisasi Database & Kredensial Admin (`feat` / `docs`)
1. **Pembaruan Skema Database (`6ce5c25`):**
   - Memperbarui skrip `database.sql` dengan enkripsi password standar dan penyesuaian tabel untuk akun admin.
2. **Pembaruan Dokumentasi Testing (`README.md`):**
   - Mengisi panduan kredensial pengujian lokal terbaru di `README.md`:
     - **Pelapor:** `mahasiswa@unisma.ac.id` / `password123`
     - **Admin:** `admin.ftst@unisma.ac.id` / `password123`

---

### 2. Troubleshooting & Verifikasi Environment Lokal

1. **Uji Coba Sinkronisasi Database (`database.sql`):**
   - *Tantangan:* Gagal login pada environment lokal `vabell00` setelah penyesuaian skema warna login oleh `cahya01`.
   - *Penyebab:* File `database.sql` lokal belum di-impor ulang setelah ada pembaruan hash password di GitHub.
   - *Solusi:* Melakukan `git pull origin main`, mengimpor ulang `database.sql` ke database `spacenest_db` via phpMyAdmin, serta mengonfirmasi parameter koneksi pada `config/database.php`.
2. **Verifikasi Pengujian Form Login:**
   - Berhasil login ke dashboard menggunakan akun mahasiswa dan admin tanpa kendala (*100% Pass*).

---

### 3. Rencana Tindak Lanjut (Next Action Items)

- [ ] **Pengembangan Formulir Pelaporan Sarana (Sprint 1):**
  - Mengembangkan antarmuka dan penanganan backend untuk pemuatan laporan kerusakan fasilitas pada `laporan_buat.php`.
- [ ] **Pengembangan Dashboard Admin:**
  - Menyusun tabel status pengajuan dan aksi validasi laporan oleh pihak sarpras di `admin_dashboard.php`.
