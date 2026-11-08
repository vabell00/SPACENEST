<?php
require_once 'config/database.php';
session_start();

if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (!empty($email) && !empty($password)) {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['name']    = $user['name'];
            $_SESSION['role']    = $user['role'];
            
            header("Location: " . ($user['role'] === 'admin' ? "admin_dashboard.php" : "dashboard.php"));
            exit;
        } else {
            $error = "Email atau kata sandi tidak valid!";
        }
    } else {
        $error = "Silakan isi semua kolom!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SPACENEST FTST</title>
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
    <div class="w-full max-w-4xl bg-white/95 backdrop-blur-xl rounded-3xl shadow-2xl overflow-hidden border border-white/20 flex flex-col md:flex-row relative z-10">
        
        <!-- Sisi Kiri: Form Login -->
        <div class="w-full md:w-1/2 p-8 md:p-12 flex flex-col justify-center">
            
            <div class="mb-6">
                <span class="inline-block px-3 py-1 rounded-full bg-blue-100 text-blue-800 text-[11px] font-bold uppercase tracking-wider mb-2">SPACENEST FTST</span>
                <h2 class="text-2xl md:text-3xl font-extrabold text-blue-950 tracking-tight">Masuk Sistem</h2>
                <p class="text-xs text-slate-500 mt-1">Sistem Pelaporan dan Monitoring Fasilitas Kampus</p>
            </div>

            <?php if ($error): ?>
                <div class="p-3.5 mb-5 rounded-2xl bg-rose-50 border border-rose-200 text-rose-700 text-xs font-semibold flex items-center gap-2 animate-shake">
                    <i class="fa-solid fa-circle-exclamation text-sm shrink-0"></i>
                    <span><?= $error ?></span>
                </div>
            <?php endif; ?>

            <form method="POST" class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5 uppercase tracking-wider">Email Kampus</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-slate-400">
                            <i class="fa-solid fa-envelope"></i>
                        </span>
                        <input type="email" name="email" required placeholder="mahasiswa@unisma.ac.id" 
                               class="w-full pl-11 pr-4 py-3.5 rounded-2xl bg-slate-50 border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:bg-white text-sm font-medium text-slate-800 placeholder:text-slate-400 transition shadow-sm">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5 uppercase tracking-wider">Kata Sandi</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-slate-400">
                            <i class="fa-solid fa-lock"></i>
                        </span>
                        <input type="password" name="password" required placeholder="••••••••" 
                               class="w-full pl-11 pr-4 py-3.5 rounded-2xl bg-slate-50 border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:bg-white text-sm font-medium text-slate-800 placeholder:text-slate-400 transition shadow-sm">
                    </div>
                </div>

                <button type="submit" class="w-full py-3.5 rounded-2xl bg-gradient-to-r from-blue-600 via-blue-700 to-blue-900 hover:from-blue-700 hover:to-blue-950 text-white font-bold text-sm transition shadow-lg hover:shadow-blue-500/25 flex items-center justify-center gap-2 mt-2">
                    <span>Masuk Sekarang</span>
                    <i class="fa-solid fa-arrow-right text-xs"></i>
                </button>
            </form>

            <!-- Link Pendaftaran -->
            <div class="mt-6 text-center text-xs text-slate-500">
                <p>Belum punya akun? 
                    <a href="register.php" class="font-bold text-blue-600 hover:text-blue-800 hover:underline">Daftar di sini</a>
                </p>
            </div>
        </div>

        <!-- Sisi Kanan: Panel Dekoratif (Welcome Back & Kurva) -->
        <div class="hidden md:flex md:w-1/2 bg-gradient-to-br from-blue-600 via-blue-800 to-blue-950 text-white p-12 flex-col justify-center items-center text-center relative overflow-hidden rounded-l-[80px]">
            <!-- Aksen Garis Oranye Khas & Efek Cahaya -->
            <div class="absolute top-1/2 left-0 w-2.5 h-20 bg-orange-500 rounded-r-full -translate-y-1/2 shadow-lg shadow-orange-500/50"></div>
            <div class="absolute -right-20 -bottom-20 w-80 h-80 bg-orange-500/10 rounded-full blur-3xl pointer-events-none"></div>
            
            <div class="relative z-10 space-y-4">
                <div class="w-16 h-16 mx-auto bg-white/10 backdrop-blur-md rounded-2xl flex items-center justify-center border border-white/20 shadow-inner">
                    <i class="fa-solid fa-building-columns text-2xl text-blue-200"></i>
                </div>
                <h2 class="text-3xl font-extrabold tracking-tight">Welcome Back!</h2>
                <p class="text-blue-100 text-sm max-w-xs mx-auto leading-relaxed">
                    Kelola dan pantau fasilitas kampus dengan lebih mudah, cepat, dan transparan bersama SPACENEST FTST.
                </p>
            </div>
        </div>

    </div>

</body>
</html>