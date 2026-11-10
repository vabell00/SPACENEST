# SPACENEST
Sistem Pengajuan dan Administrasi Catatan Eksklusif dengan Navigasi Efisiensi Sarana Tempat.

## Menjalankan aplikasi lokal (Windows/XAMPP)

1. Buka XAMPP Control Panel, lalu jalankan **MySQL**. Pastikan port `3306` tersedia.
2. Impor `database.sql` ke MariaDB/MySQL. Contoh melalui PowerShell:

   ```powershell
   Get-Content -Raw .\database.sql | & 'C:\xampp\mysql\bin\mysql.exe' -h 127.0.0.1 -u root
   ```

3. Jalankan server PHP dari folder proyek:

   ```powershell
   C:\xampp\php\php.exe -S 127.0.0.1:8000 -t .
   ```

4. Buka `http://127.0.0.1:8000`.

Konfigurasi bawaan memakai database `spacenest_db`, pengguna MySQL `root` tanpa kata sandi, dan port `3306`. Nilainya dapat diubah menggunakan `SPACENEST_DB_HOST`, `SPACENEST_DB_PORT`, `SPACENEST_DB_NAME`, `SPACENEST_DB_USER`, dan `SPACENEST_DB_PASS`.

Akun demo setelah mengimpor database:

- Pelapor: `mahasiswa@unisma.ac.id` / `password123`
- Admin: `admin.ftst@unisma.ac.id` / `password123`

Jika akun demo lama sudah ada tetapi kata sandinya tidak cocok, impor ulang `database.sql`.
Impor memperbarui kata sandi hanya jika masih berupa hash bawaan lama yang tidak valid;
kata sandi akun yang sudah diubah sendiri tidak ditimpa.
