<?php
require_once 'config/database.php';
session_start();

// Jika sudah login, arahkan ke index
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = trim($_POST['name']);
    $email    = trim($_POST['email']);
    $password = trim($_POST['password']);
    $prodi    = trim($_POST['prodi']);
    $role     = 'pelapor'; // Default pendaftaran publik sebagai pelapor (mahasiswa/dosen)

    if (!empty($name) && !empty($email) && !empty($password) && !empty($prodi)) {
        // Cek apakah email sudah terdaftar
        $checkStmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $checkStmt->execute([$email]);
        
        if ($checkStmt->rowCount() > 0) {
            $error = "Email sudah terdaftar! Silakan gunakan email lain atau masuk.";
        } else {
            // Hash password
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // Insert user baru ke database
            $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role, prodi) VALUES (?, ?, ?, ?, ?)");
            if ($stmt->execute([$name, $email, $hashedPassword, $role, $prodi])) {
                $success = "Pendaftaran berhasil! Silakan masuk dengan akun Anda.";
            } else {
                $error = "Terjadi kesalahan saat mendaftar. Silakan coba lagi.";
            }
        }
    } else {
        $error = "Semua kolom wajib diisi!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar - SPACENEST FTST</title>
    <!-- Tailwind CSS & FontAwesome CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-blue-900 via-blue-950 to-slate-900 min-h-screen flex items-center justify-center p-4 relative overflow-hidden">

    <!-- Background Ornament / Glow Effects -->
    <div class="absolute -top-40 -left-40 w-96 h-96 bg-blue-500/20 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute -bottom-40 -right-40 w-96 h-96 bg-orange-500/15 rounded-full blur-3xl pointer-events-none"></div>

    <!-- Main Container -->
    <div class="w-full max-w-4xl bg-white/95 backdrop-blur-xl rounded-3xl shadow-2xl overflow-hidden border border-white/20 flex flex-col md:flex-row relative z-10 my-6">
        
        <!-- Sisi Kiri: Form Registrasi -->
        <div class="w-full md:w-1/2 p-8 md:p-10 flex flex-col justify-center">
            
            <!-- Logo & Header Form -->
            <div class="mb-5">
                <h2 class="text-2xl font-extrabold text-blue-950 tracking-tight">Buat Akun Baru</h2>
                <p class="text-xs text-slate-500 mt-0.5">Daftarkan akun pelapor SPACENEST FTST.</p>
            </div>

            <?php if ($error): ?>
                <div class="p-3 mb-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-xs font-semibold flex items-center gap-2">
                    <i class="fa-solid fa-circle-exclamation text-sm shrink-0"></i>
                    <span><?= htmlspecialchars($error) ?></span>
                </div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="p-3 mb-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs font-semibold flex items-center gap-2">
                    <i class="fa-solid fa-circle-check text-sm shrink-0"></i>
                    <span><?= htmlspecialchars($success) ?> <a href="login.php" class="underline font-bold ml-1 text-blue-600">Masuk di sini</a></span>
                </div>
            <?php endif; ?>

            <form method="POST" class="space-y-3.5">
                <div>
                    <label class="block text-[11px] font-bold text-slate-700 mb-1 uppercase tracking-wider">Nama Lengkap *</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400">
                            <i class="fa-solid fa-user text-xs"></i>
                        </span>
                        <input type="text" name="name" required placeholder="Alif Fitrah Ramadhan" 
                               class="w-full pl-10 pr-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:bg-white text-xs font-medium text-slate-800 placeholder:text-slate-400 transition">
                    </div>
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-slate-700 mb-1 uppercase tracking-wider">Email Kampus / Pribadi *</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400">
                            <i class="fa-solid fa-envelope text-xs"></i>
                        </span>
                        <input type="email" name="email" required placeholder="mahasiswa@unisma.ac.id" 
                               class="w-full pl-10 pr-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:bg-white text-xs font-medium text-slate-800 placeholder:text-slate-400 transition">
                    </div>
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-slate-700 mb-1 uppercase tracking-wider">Program Studi / Unit *</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400">
                            <i class="fa-solid fa-graduation-cap text-xs"></i>
                        </span>
                        <select name="prodi" required class="w-full pl-10 pr-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:bg-white text-xs font-medium text-slate-800 transition">
                            <option value="" disabled selected>Pilih Program Studi</option>
                            <option value="Teknik Informatika">Teknik Informatika (S1)</option>
                            <option value="Teknik Elektro">Teknik Elektro (S1)</option>
                            <option value="Teknik Mesin">Teknik Mesin (S1)</option>
                            <option value="Teknik Sipil">Teknik Sipil (S1)</option>
                            <option value="Dosen / Staf FTST">Dosen / Staf Fakultas</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-slate-700 mb-1 uppercase tracking-wider">Kata Sandi *</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400">
                            <i class="fa-solid fa-lock text-xs"></i>
                        </span>
                        <input type="password" name="password" required placeholder="••••••••" 
                               class="w-full pl-10 pr-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:bg-white text-xs font-medium text-slate-800 placeholder:text-slate-400 transition">
                    </div>
                </div>

                <button type="submit" class="w-full py-3 rounded-xl bg-gradient-to-r from-blue-600 via-blue-700 to-blue-900 hover:from-blue-700 hover:to-blue-950 text-white font-bold text-xs transition shadow-lg hover:shadow-blue-500/25 flex items-center justify-center gap-2 mt-1">
                    <span>Daftar Sekarang</span>
                    <i class="fa-solid fa-arrow-right text-[10px]"></i>
                </button>
            </form>

            <div class="mt-5 text-center text-xs text-slate-500">
                Sudah punya akun? <a href="login.php" class="font-bold text-blue-600 hover:text-blue-800 hover:underline">Masuk di sini</a>
            </div>
        </div>

        <!-- Sisi Kanan: Panel Dekoratif -->
        <div class="hidden md:flex md:w-1/2 bg-gradient-to-br from-blue-600 via-blue-800 to-blue-950 text-white p-10 flex-col justify-center items-center text-center relative overflow-hidden rounded-l-[80px]">
            <!-- Aksen Garis Oranye Khas & Efek Cahaya -->
            <div class="absolute top-1/2 left-0 w-2.5 h-20 bg-orange-500 rounded-r-full -translate-y-1/2 shadow-lg shadow-orange-500/50"></div>
            <div class="absolute -right-20 -bottom-20 w-80 h-80 bg-orange-500/10 rounded-full blur-3xl pointer-events-none"></div>
            
            <div class="relative z-10 space-y-4">
                <div class="w-16 h-16 mx-auto bg-white/10 backdrop-blur-md rounded-2xl flex items-center justify-center border border-white/20 shadow-inner">
                    <i class="fa-solid fa-user-plus text-2xl text-blue-200"></i>
                </div>
                <h2 class="text-3xl font-extrabold tracking-tight">Bergabung Bersama Kami!</h2>
                <p class="text-blue-100 text-sm max-w-xs mx-auto leading-relaxed">
                    Daftarkan akun Anda untuk mulai melaporkan dan memantau fasilitas kampus dengan transparan di SPACENEST FTST.
                </p>
            </div>
        </div>

    </div>

</body>
</html>