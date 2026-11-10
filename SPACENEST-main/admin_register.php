<?php
require_once __DIR__ . '/config/database.php';
session_start();

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = trim($_POST['name']);
    $email    = trim($_POST['email']);
    $password = trim($_POST['password']);
    $role     = 'admin'; // Diatur otomatis sebagai admin FTST
    $prodi    = NULL;    // Admin fakultas tidak terikat prodi spesifik

    if (!empty($name) && !empty($email) && !empty($password)) {
        // Cek apakah email sudah terdaftar
        $checkStmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $checkStmt->execute([$email]);
        
        if ($checkStmt->rowCount() > 0) {
            $error = "Email sudah terdaftar! Silakan gunakan email lain.";
        } else {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // Simpan ke database dengan role 'admin'
            $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role, prodi) VALUES (?, ?, ?, ?, ?)");
            if ($stmt->execute([$name, $email, $hashedPassword, $role, $prodi])) {
                $success = "Pendaftaran Admin FTST berhasil! Silakan masuk.";
            } else {
                $error = "Terjadi kesalahan saat mendaftar akun admin.";
            }
        }
    } else {
        $error = "Semua kolom wajib diisi!";
    }
}

include __DIR__ . '/includes/header.php';
?>

<div class="max-w-md mx-auto mt-6 mb-10">
    <div class="glass-panel p-8 rounded-3xl shadow-xl border border-white/60">
        <div class="flex items-center gap-2 mb-2">
            <span class="px-3 py-1 bg-amber-600 text-white text-[10px] font-bold rounded-full uppercase tracking-wider">Registrasi Khusus</span>
            <span class="text-xs text-stone-500 font-semibold">Pihak Fakultas / Admin</span>
        </div>
        <h2 class="text-2xl font-bold text-stone-900 mb-1">Daftar Akun Admin FTST</h2>
        <p class="text-xs text-stone-500 mb-6">Buat akun pengelola untuk verifikasi dan pemantauan fasilitas.</p>

        <?php if ($error): ?>
            <div class="p-3 mb-4 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-700 text-xs font-medium">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="p-3 mb-4 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-700 text-xs font-medium">
                <?= htmlspecialchars($success) ?> 
                <a href="login.php" class="underline font-bold ml-1">Masuk sekarang</a>
            </div>
        <?php endif; ?>

        <form method="POST" class="space-y-4">
            <div>
                <label class="block text-xs font-semibold text-stone-700 mb-1">Nama Lengkap & Gelar *</label>
                <input type="text" name="name" required placeholder="Dr. Ir. Admin Fakultas, M.T." 
                       class="w-full px-4 py-2.5 rounded-xl bg-white/70 border border-stone-300 focus:ring-2 focus:ring-amber-500 text-xs">
            </div>

            <div>
                <label class="block text-xs font-semibold text-stone-700 mb-1">Email Resmi Fakultas *</label>
                <input type="email" name="email" required placeholder="admin.ftst@unisma.ac.id" 
                       class="w-full px-4 py-2.5 rounded-xl bg-white/70 border border-stone-300 focus:ring-2 focus:ring-amber-500 text-xs">
            </div>

            <div>
                <label class="block text-xs font-semibold text-stone-700 mb-1">Kata Sandi *</label>
                <input type="password" name="password" required placeholder="••••••••" 
                       class="w-full px-4 py-2.5 rounded-xl bg-white/70 border border-stone-300 focus:ring-2 focus:ring-amber-500 text-xs">
            </div>

            <button type="submit" class="w-full py-3 rounded-xl bg-amber-600 hover:bg-amber-700 text-white font-semibold text-xs transition shadow-md">
                Daftar Sebagai Admin FTST
            </button>
        </form>

        <div class="mt-6 flex justify-between items-center text-xs text-stone-500">
            <a href="register.php" class="hover:underline">← Daftar sebagai Pelapor</a>
            <a href="login.php" class="text-amber-700 font-semibold hover:underline">Sudah punya akun? Masuk</a>
        </div>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>