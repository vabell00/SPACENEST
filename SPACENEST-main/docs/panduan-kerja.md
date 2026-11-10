# SOP & Panduan Kerja Tim SPACENEST
**Dokumen Operasional Kolaborasi Tim**  
**Lokasi File:** `docs/D10-panduan-kerja-tim.md` | **Versi:** 1.0  
**Tim:** Vabellian Naoni (PM), Andika Cahya Syahputra (Lead Dev), Alif Fitrah Ramadhan (UI/UX & QA)

---

## 1. Panduan Langkah Praktis

### A. Rapat & Koordinasi Tim
1. **Jadwal Fleksibel & Terarah:** Rapat tidak harus formal kaku. Koordinasi dapat dilakukan via WhatsApp Group atau tatap muka langsung di kampus setelah jam kuliah PPL.
2. **Format Pembahasan (Agenda 3 Pertanyaan):**
   - Apa yang sudah diselesaikan sejak rapat terakhir?
   - Apakah ada kendala teknis/error yang dialami?
   - Siapa memegang tugas (*task*) apa untuk periode berikutnya?
3. **Pencatatan Log Mingguan:** Setiap selesai rapat/diskusi penting, ditunjuk 1 anggota secara bergantian untuk mencatat hasilnya ke dalam file `log/mingguan.md`.

---

### B. Penggunaan GitHub Issues (Manajemen Tugas)
1. **Pembuatan Issue:** Setiap pekerjaan (fitur baru, perbaikan bug, atau dokumen) wajib dicatat sebagai **Issue** di repositori GitHub sebelum mulai dikerjakan.
2. **Penetapan Penanggung Jawab (*Assignee*):** Vabellian (selaku PM) atau pembuat Issue menunjuk 1 anggota penanggung jawab pada kolom *Assignees* di dashboard GitHub.
3. **Penutupan Issue Otomatis:** Issue akan tertutup otomatis saat anggota mengunggah kode dengan menyertakan kata kunci `closes #nomor_issue` pada pesan commit.

---

### C. Alur Pengkodean (*Coding Workflow*)
1. **Sinkronisasi Kode (`git pull`):** Wajib menjalankan `git pull origin main` sebelum mulai mengedit file di laptop masing-masing untuk menghindari bentrok (*merge conflict*).
2. **Pembagian Modul Kerja:**
   - **Andika (Lead Dev):** Database, koneksi PHP, dan logika *backend*.
   - **Alif (UI/UX & Frontend):** Tampilan antarmuka, layout HTML/CSS/Tailwind, dan respon seluler.
   - **Vabellian (PM & QA):** Pengujian fitur di `localhost`, validasi input, dan dokumentasi.
3. **Pengujian Lokal:** Pastikan kode berjalan tanpa error di `localhost` (XAMPP) sebelum di-push.
4. **Pengiriman Kode (`git push`):** Jalankan perintah `git add`, `git commit`, dan `git push origin main` setelah fitur selesai.

---

### D. Pembaruan Dokumen (`docs/`)
1. **Sinkronisasi Realita:** Isi dokumen di folder `docs/` harus mencerminkan kondisi aplikasi sebenarnya saat ini.
2. **Tanggung Jawab Pembaruan:** Pemegang dokumen utama (sesuai D1 Team Charter) bertanggung jawab memperbarui status centang checklist `[x]` atau deskripsi fitur bila ada perubahan.

---

## 2. Simulasi Konkret Penggunaan

### Skenario: Pengerjaan Fitur "Form Pelaporan Kerusakan"

```text
[Rapat/Diskusi] ──> [Buat Issue di GitHub] ──> [Git Pull] ──> [Ngoding di Localhost] ──> [Git Commit & Push] ──> [Issue Auto-Closed]
```

#### Langkah 1: Diskusi Singkat di Kampus / WhatsApp
* **Vabellian:** *"Guys, minggu ini kita selesaikan fitur Form Pelaporan Kerusakan. Alif bikin tampilan form-nya, Andika buat logika simpan ke database-nya, gua yang tes dan update dokumen D3."*

#### Langkah 2: Vabellian Membuat Issue di Dashboard GitHub
1. Vabellian membuka browser $\rightarrow$ repositori `vabell00/SPACENEST` $\rightarrow$ Tab **`Issues`**.
2. Klik tombol hijau **`New issue`**.
3. **Issue #1:**
   - **Title:** `Merapikan antarmuka form input laporan`
   - **Assignees:** Select `@alif`
   - Klik **`Submit new issue`**.
4. **Issue #2:**
   - **Title:** `Membuat logika simpan database laporan_buat.php`
   - **Assignees:** Select `@andika`
   - Klik **`Submit new issue`**.
5. Vabellian mencatat ringkasan pembagian tugas ini di `log/mingguan.md`.

#### Langkah 3: Alif Mengerjakan Tampilan (Frontend)
1. Alif membuka terminal VS Code di laptopnya:
   ```powershell
   git pull origin main
   ```
2. Alif mengedit file `laporan_buat.php` (menambah form input judul, lokasi, upload foto, dan gaya Tailwind).
3. Alif mengetes di browser: `http://localhost/spacenest/laporan_buat.php`.
4. Setelah rapi, Alif mengunggah kodenya sambil menghubungkan ke Issue #1:
   ```powershell
   git add laporan_buat.php
   git commit -m "feat: merapikan tampilan form input laporan (closes #1)"
   git push origin main
   ```
   *(Secara otomatis, Status Issue #1 di GitHub berubah menjadi **Closed**).*

#### Langkah 4: Andika Mengerjakan Logika Simpan (Backend)
1. Andika membuka terminal VS Code di laptopnya:
   ```powershell
   git pull origin main
   ```
   *(File `laporan_buat.php` buatan Alif otomatis masuk ke laptop Andika).*
2. Andika menambahkan fungsi PHP `mysqli_query` untuk menyimpan data form dan foto ke database MySQL.
3. Andika mengetes simpan data di `localhost`.
4. Setelah berhasil, Andika mengunggah kodenya:
   ```powershell
   git add .
   git commit -m "feat: integrasi simpan data laporan ke database (closes #2)"
   git push origin main
   ```
   *(Secara otomatis, Status Issue #2 di GitHub berubah menjadi **Closed**).*

#### Langkah 5: Vabellian Melakukan Pengujian & Pembaruan Dokumen
1. Vabellian menjalankan `git pull origin main` di laptopnya.
2. Vabellian mencoba mengisi form laporan dari browser dan memastikan data masuk ke database.
3. Vabellian membuka file `docs/D3-srs-product-backlog.md` dan mengubah centang fitur pelaporan menjadi selesai:
   - `[x] User dapat membuat laporan kerusakan baru`
4. Vabellian mengunggah pembaruan dokumen:
   ```powershell
   git add docs/D3-srs-product-backlog.md
   git commit -m "docs: pembaruan status pengerjaan fitur laporan pada D3"
   git push origin main
   ```