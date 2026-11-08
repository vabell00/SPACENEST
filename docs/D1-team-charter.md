# D1 · Team Charter

Tim sepakat membangun satu produk perangkat lunak untuk pengguna nyata dalam satu semester dengan aturan kerja di bawah ini. Dokumen disimpan di docs/D1-team-charter.md, versi 1.0.

## 1. Anggota dan Peran
* **Project Manager & Analis:** Vabellian Naoni (NPM: 41187007240006)
  * *Tanggung jawab utama:* Jadwal, rapat, log mingguan, register risiko, komunikasi dengan pihak fakultas, analisis kebutuhan.
  * *Memimpin dokumen:* D1, D4, D5, D9.
* **Lead Developer:** Andika Cahya Syahputra (NPM: 41187007240009)
  * *Tanggung jawab utama:* Arsitektur, basis data, logika bisnis, keamanan unggah berkas, deployment.
  * *Memimpin dokumen:* D6 (arsitektur dan ERD), D8 (dokumentasi teknis).
* **UI/UX Designer, QA & Analis Sistem:** Alif Fitrah Ramadhan (NPM: 41187007240008)
  * *Tanggung jawab utama:* Desain antarmuka mobile-friendly, test plan, pengujian, validasi data lokasi.
  * *Memimpin dokumen:* D2, D3, D6 (UI/UX), D7.

*Catatan:* Semua anggota tetap menulis kode dan menulis dokumen. Peran menentukan siapa yang memimpin, bukan siapa yang mengerjakan sendirian.

## 2. Rapat dan Komunikasi
* **Rapat tim:** Dua kali seminggu (Selasa dan Jumat, pukul 19.30–20.30, lewat Google Meet). Notulen ditulis di `log/mingguan.md` paling lambat 24 jam setelah rapat.
* **Rapat sprint:** Sprint planning di awal dan retrospektif di akhir setiap sprint (2 minggu), bertatap muka setelah kuliah PPL.
* **Komunikasi harian:** Grup WhatsApp tim. Keputusan yang memengaruhi scope, jadwal, atau desain wajib dicatat di Issue GitHub, bukan hanya di grup.
* **Waktu respons:** Pesan di grup dibalas maksimal 24 jam.

## 3. Aturan Kerja
* Setiap pekerjaan berawal dari Issue GitHub yang punya penanggung jawab.
* Tidak ada commit langsung ke `main`. Semua perubahan lewat pull request dan direview minimal satu anggota lain (reviewer dirotasi).
* Pesan commit memakai format jenis: deskripsi singkat, misalnya `feat: form laporan kerusakan`.
* Dokumen ditulis dalam Markdown di folder `docs/` dan direvisi lewat pull request seperti kode.

## 4. Definition of Done (Versi Awal)
Sebuah user story dianggap selesai jika:
* [ ] Semua acceptance criteria terpenuhi
* [ ] Kode sudah direview dan di-merge ke main
* [ ] Lolos test case yang terkait
* [ ] Tampil dan dapat dipakai dengan baik di layar ponsel (lebar minimal 360 px)
* [ ] Berjalan di server staging
* [ ] Dokumen terkait (SRS atau manual) sudah diperbarui

## 5. Penyelesaian Konflik
* Perbedaan pendapat teknis dibahas di Issue, lalu diputuskan di rapat dengan suara terbanyak (2 dari 3 anggota). Dengan tiga anggota, suara tidak mungkin seimbang.
* Konflik antaranggota dibicarakan langsung lebih dulu. Jika tidak selesai dalam satu minggu, dibawa ke dosen pengampu.

## 6. Konsekuensi Jika Anggota Tidak Berkontribusi
* **Tidak hadir rapat 2 kali berturut-turut tanpa kabar:** Teguran tertulis di log mingguan.
* **Issue lewat tenggat lebih dari 1 minggu tanpa kabar:** Issue dialihkan ke anggota lain, dicatat di log.
* **Tidak ada commit atau dokumen selama 1 sprint:** Dilaporkan ke dosen, memengaruhi nilai peer review.

## 7. Persetujuan
Disetujui oleh ketiga anggota pada pertemuan 1. Bukti persetujuan berupa commit pertama setiap anggota yang menambahkan namanya ke tabel peran di repositori.