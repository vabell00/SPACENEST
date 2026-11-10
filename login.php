<?php
require_once __DIR__ . '/config/database.php';
session_start();

if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$mode = ($_GET['mode'] ?? 'login') === 'register' ? 'register' : 'login';
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? 'login';
    $mode = $action === 'register' ? 'register' : 'login';

    if ($mode === 'register') {
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['register_email'] ?? '');
        $password = trim($_POST['register_password'] ?? '');
        $prodi = trim($_POST['prodi'] ?? '');

        if ($name !== '' && $email !== '' && $password !== '' && $prodi !== '') {
            $checkStmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
            $checkStmt->execute([$email]);

            if ($checkStmt->fetch()) {
                $error = 'Email sudah terdaftar! Silakan gunakan email lain atau masuk.';
            } else {
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role, prodi) VALUES (?, ?, ?, 'pelapor', ?)");
                $stmt->execute([$name, $email, $hashedPassword, $prodi]);
                $success = 'Pendaftaran berhasil! Silakan masuk dengan akun Anda.';
            }
        } else {
            $error = 'Semua kolom wajib diisi!';
        }
    } else {
        $email = trim($_POST['email'] ?? '');
        $password = trim($_POST['password'] ?? '');

        if ($email !== '' && $password !== '') {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['name'] = $user['name'];
                $_SESSION['role'] = $user['role'];

                header("Location: " . ($user['role'] === 'admin' ? "admin_dashboard.php" : "dashboard.php"));
                exit;
            }

            $error = 'Email atau kata sandi tidak valid!';
        } else {
            $error = 'Silakan isi semua kolom!';
        }
    }
}

$isRegister = $mode === 'register';
$escapedError = htmlspecialchars($error, ENT_QUOTES, 'UTF-8');
$escapedSuccess = htmlspecialchars($success, ENT_QUOTES, 'UTF-8');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $isRegister ? 'Daftar' : 'Login' ?> - SPACENEST FTST</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 44 44' fill='none'%3E%3Ccircle cx='22' cy='22' r='22' fill='%231c1917'/%3E%3Cpath d='M9.5 22a12.5 12.5 0 0 0 25 0' stroke='white' stroke-width='3' stroke-linecap='round'/%3E%3Ccircle cx='22' cy='17.5' r='4.6' fill='%232fbf8f'/%3E%3C/svg%3E">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: #1c1917;
            background: #fafaf9;
        }

        .auth-card {
            min-height: 690px;
            isolation: isolate;
        }

        .auth-forms {
            position: absolute;
            inset: 0 auto 0 0;
            display: flex;
            width: 100%;
        }

        .auth-form {
            display: flex;
            width: 50%;
            flex: 0 0 50%;
            flex-direction: column;
            justify-content: center;
            padding: 2rem;
        }

        .auth-register {
            padding-top: 1.5rem;
            padding-bottom: 1.5rem;
        }

        .auth-overlay {
            position: absolute;
            z-index: 2;
            top: 0;
            right: 0;
            display: flex;
            width: 50%;
            height: 100%;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            border-radius: 5rem 0 0 5rem;
            background: #1c1917;
            color: white;
            transition: transform .75s cubic-bezier(.77, 0, .18, 1), border-radius .75s;
        }

        .auth-overlay-content {
            position: absolute;
            width: 100%;
            padding: 2rem;
            text-align: center;
            transition: opacity .3s ease, transform .5s ease;
        }

        .auth-overlay-register {
            transform: translateX(24px);
            opacity: 0;
            pointer-events: none;
        }

        .auth-card.is-register .auth-overlay {
            transform: translateX(-100%);
            border-radius: 0 5rem 5rem 0;
        }

        .auth-card.is-register .auth-overlay-login {
            transform: translateX(-24px);
            opacity: 0;
            pointer-events: none;
        }

        .auth-card.is-register .auth-overlay-register {
            transform: translateX(0);
            opacity: 1;
            pointer-events: auto;
        }

        @media (min-width: 768px) {
            .auth-form {
                padding-right: 4rem;
                padding-left: 4rem;
            }

            .auth-register {
                padding-right: 4.5rem;
                padding-left: 4.5rem;
            }
        }

        @media (max-width: 767px) {
            .auth-card {
                min-height: 650px;
            }

            .auth-form {
                padding: 1.5rem;
            }

            .auth-forms {
                width: 200%;
                transition: transform .75s cubic-bezier(.77, 0, .18, 1);
            }

            .auth-register {
                padding-top: 1.25rem;
                padding-bottom: 1.25rem;
            }

            .auth-card.is-register .auth-forms {
                transform: translateX(-50%);
            }

            .auth-overlay {
                display: none;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after {
                scroll-behavior: auto !important;
                transition-duration: .01ms !important;
                animation-duration: .01ms !important;
                animation-iteration-count: 1 !important;
            }
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4 relative overflow-x-hidden">
    <div class="absolute -top-40 -left-40 w-96 h-96 bg-[#2fbf8f]/10 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute -bottom-40 -right-40 w-96 h-96 bg-stone-400/10 rounded-full blur-3xl pointer-events-none"></div>

    <main id="auth-card" class="auth-card w-full max-w-5xl bg-white/90 backdrop-blur-xl rounded-3xl shadow-[0_24px_70px_-28px_rgba(28,25,23,.3)] overflow-hidden border border-stone-200 relative z-10 <?= $isRegister ? 'is-register' : '' ?>" aria-label="Autentikasi SPACENEST">
        <div class="auth-forms">
            <section id="login-panel" class="auth-form" aria-hidden="<?= $isRegister ? 'true' : 'false' ?>" <?= $isRegister ? 'inert' : '' ?>>
                <div class="mb-6">
                    <a href="index.php" class="inline-flex items-center gap-3 mb-5 rounded-full focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#2fbf8f]" aria-label="SPACENEST, beranda">
                        <svg class="h-11 w-11 shrink-0" viewBox="0 0 44 44" fill="none" aria-hidden="true">
                            <circle cx="22" cy="22" r="22" fill="#1c1917"/>
                            <path d="M9.5 22a12.5 12.5 0 0 0 25 0" stroke="#fff" stroke-width="3" stroke-linecap="round"/>
                            <path d="M15 22.5a7 7 0 0 0 14 0" stroke="#fff" stroke-opacity=".4" stroke-width="2.4" stroke-linecap="round"/>
                            <circle cx="22" cy="17.5" r="4.6" fill="#2fbf8f"/>
                        </svg>
                        <span class="text-sm font-extrabold tracking-tight text-[#1c1917]">SPACENEST <span class="text-stone-500">FTST</span></span>
                    </a>
                    <h1 class="text-2xl md:text-3xl font-extrabold text-[#1c1917] tracking-tight">Masuk Sistem</h1>
                    <p class="text-xs text-stone-500 mt-1">Sistem Pelaporan dan Monitoring Fasilitas Kampus</p>
                </div>

                <?php if ($error && !$isRegister): ?>
                    <div class="p-3.5 mb-5 rounded-2xl bg-rose-50 border border-rose-200 text-rose-700 text-xs font-semibold flex items-center gap-2" role="alert">
                        <i class="fa-solid fa-circle-exclamation text-sm shrink-0"></i>
                        <span><?= $escapedError ?></span>
                    </div>
                <?php endif; ?>

                <form method="POST" action="login.php" class="space-y-4">
                    <input type="hidden" name="action" value="login">
                    <div>
                        <label for="login-email" class="block text-xs font-bold text-stone-700 mb-1.5 uppercase tracking-wider">Email Kampus</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-stone-400"><i class="fa-solid fa-envelope"></i></span>
                            <input id="login-email" type="email" name="email" required autocomplete="email" placeholder="mahasiswa@unisma.ac.id" class="w-full pl-11 pr-4 py-3.5 rounded-2xl bg-stone-50 border border-stone-200 focus:outline-none focus:ring-2 focus:ring-[#2fbf8f] focus:bg-white text-sm font-medium text-stone-800 placeholder:text-stone-400 transition shadow-sm">
                        </div>
                    </div>
                    <div>
                        <label for="login-password" class="block text-xs font-bold text-stone-700 mb-1.5 uppercase tracking-wider">Kata Sandi</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-stone-400"><i class="fa-solid fa-lock"></i></span>
                            <input id="login-password" type="password" name="password" required autocomplete="current-password" placeholder="••••••••" class="w-full pl-11 pr-4 py-3.5 rounded-2xl bg-stone-50 border border-stone-200 focus:outline-none focus:ring-2 focus:ring-[#2fbf8f] focus:bg-white text-sm font-medium text-stone-800 placeholder:text-stone-400 transition shadow-sm">
                        </div>
                    </div>
                    <button type="submit" class="w-full py-3.5 rounded-2xl bg-[#1c1917] hover:bg-stone-800 text-white font-bold text-sm transition shadow-lg hover:shadow-stone-900/20 flex items-center justify-center gap-2 mt-2 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#2fbf8f]">
                        <span>Masuk Sekarang</span><i class="fa-solid fa-arrow-right text-xs"></i>
                    </button>
                </form>

                <div class="mt-6 text-center text-xs text-stone-500 md:hidden">
                    Belum punya akun?
                    <button type="button" data-switch-mode="register" class="font-bold text-stone-900 hover:text-[#159a70] hover:underline">Daftar di sini</button>
                </div>
            </section>

            <section id="register-panel" class="auth-form auth-register" aria-hidden="<?= $isRegister ? 'false' : 'true' ?>" <?= $isRegister ? '' : 'inert' ?>>
                <div class="mb-5">
                    <a href="index.php" class="inline-flex items-center gap-3 mb-4 rounded-full focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#2fbf8f]" aria-label="SPACENEST, beranda">
                        <svg class="h-10 w-10 shrink-0" viewBox="0 0 44 44" fill="none" aria-hidden="true">
                            <circle cx="22" cy="22" r="22" fill="#1c1917"/>
                            <path d="M9.5 22a12.5 12.5 0 0 0 25 0" stroke="#fff" stroke-width="3" stroke-linecap="round"/>
                            <path d="M15 22.5a7 7 0 0 0 14 0" stroke="#fff" stroke-opacity=".4" stroke-width="2.4" stroke-linecap="round"/>
                            <circle cx="22" cy="17.5" r="4.6" fill="#2fbf8f"/>
                        </svg>
                        <span class="text-sm font-extrabold tracking-tight text-[#1c1917]">SPACENEST <span class="text-stone-500">FTST</span></span>
                    </a>
                    <h2 class="text-2xl font-extrabold text-[#1c1917] tracking-tight">Buat Akun Baru</h2>
                    <p class="text-xs text-stone-500 mt-0.5">Daftarkan akun pelapor SPACENEST FTST.</p>
                </div>

                <?php if ($error && $isRegister): ?>
                    <div class="p-3 mb-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-xs font-semibold flex items-center gap-2" role="alert">
                        <i class="fa-solid fa-circle-exclamation text-sm shrink-0"></i>
                        <span><?= $escapedError ?></span>
                    </div>
                <?php endif; ?>
                <?php if ($success): ?>
                    <div class="p-3 mb-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs font-semibold flex items-center gap-2" role="status">
                        <i class="fa-solid fa-circle-check text-sm shrink-0"></i>
                        <span><?= $escapedSuccess ?> <button type="button" data-switch-mode="login" class="underline font-bold ml-1 text-stone-900">Masuk di sini</button></span>
                    </div>
                <?php endif; ?>

                <form method="POST" action="login.php?mode=register" class="space-y-3">
                    <input type="hidden" name="action" value="register">
                    <div>
                        <label for="register-name" class="block text-[11px] font-bold text-stone-700 mb-1 uppercase tracking-wider">Nama Lengkap *</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-stone-400"><i class="fa-solid fa-user text-xs"></i></span>
                            <input id="register-name" type="text" name="name" required autocomplete="name" placeholder="Nama lengkap" class="w-full pl-10 pr-3.5 py-2.5 rounded-xl bg-stone-50 border border-stone-200 focus:outline-none focus:ring-2 focus:ring-[#2fbf8f] focus:bg-white text-xs font-medium text-stone-800 placeholder:text-stone-400 transition">
                        </div>
                    </div>
                    <div>
                        <label for="register-email" class="block text-[11px] font-bold text-stone-700 mb-1 uppercase tracking-wider">Email Kampus / Pribadi *</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-stone-400"><i class="fa-solid fa-envelope text-xs"></i></span>
                            <input id="register-email" type="email" name="register_email" required autocomplete="email" placeholder="mahasiswa@unisma.ac.id" class="w-full pl-10 pr-3.5 py-2.5 rounded-xl bg-stone-50 border border-stone-200 focus:outline-none focus:ring-2 focus:ring-[#2fbf8f] focus:bg-white text-xs font-medium text-stone-800 placeholder:text-stone-400 transition">
                        </div>
                    </div>
                    <div>
                        <label for="register-prodi" class="block text-[11px] font-bold text-stone-700 mb-1 uppercase tracking-wider">Program Studi / Unit *</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-stone-400"><i class="fa-solid fa-graduation-cap text-xs"></i></span>
                            <select id="register-prodi" name="prodi" required class="w-full pl-10 pr-3.5 py-2.5 rounded-xl bg-stone-50 border border-stone-200 focus:outline-none focus:ring-2 focus:ring-[#2fbf8f] focus:bg-white text-xs font-medium text-stone-800 transition">
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
                        <label for="register-password" class="block text-[11px] font-bold text-stone-700 mb-1 uppercase tracking-wider">Kata Sandi *</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-stone-400"><i class="fa-solid fa-lock text-xs"></i></span>
                            <input id="register-password" type="password" name="register_password" required autocomplete="new-password" placeholder="••••••••" class="w-full pl-10 pr-3.5 py-2.5 rounded-xl bg-stone-50 border border-stone-200 focus:outline-none focus:ring-2 focus:ring-[#2fbf8f] focus:bg-white text-xs font-medium text-stone-800 placeholder:text-stone-400 transition">
                        </div>
                    </div>
                    <button type="submit" class="w-full py-3 rounded-xl bg-[#1c1917] hover:bg-stone-800 text-white font-bold text-xs transition shadow-lg hover:shadow-stone-900/20 flex items-center justify-center gap-2 mt-1 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#2fbf8f]">
                        <span>Daftar Sekarang</span><i class="fa-solid fa-arrow-right text-[10px]"></i>
                    </button>
                </form>

                <div class="mt-4 text-center text-xs text-stone-500 md:hidden">
                    Sudah punya akun?
                    <button type="button" data-switch-mode="login" class="font-bold text-stone-900 hover:text-[#159a70] hover:underline">Masuk di sini</button>
                </div>
            </section>
        </div>

        <aside class="auth-overlay" aria-live="polite">
            <div class="absolute top-1/2 left-0 w-2.5 h-20 bg-[#2fbf8f] rounded-r-full -translate-y-1/2 shadow-lg shadow-[#2fbf8f]/30"></div>
            <div class="absolute -right-20 -bottom-20 w-80 h-80 bg-[#2fbf8f]/10 rounded-full blur-3xl pointer-events-none"></div>

            <div class="auth-overlay-content auth-overlay-login" aria-hidden="<?= $isRegister ? 'true' : 'false' ?>">
                <div class="w-16 h-16 mx-auto mb-5 bg-white/10 rounded-2xl grid place-items-center border border-white/20">
                    <i class="fa-solid fa-building-columns text-2xl text-[#2fbf8f]"></i>
                </div>
                <h2 class="text-3xl font-extrabold tracking-tight">Selamat Datang Kembali</h2>
                <p class="mt-4 text-stone-300 text-sm max-w-xs mx-auto leading-relaxed">Pantau laporan dan fasilitas kampus dengan mudah dan transparan bersama SPACENEST FTST.</p>
                <button type="button" data-switch-mode="register" class="mt-8 px-7 py-3 rounded-full border border-white/40 hover:bg-white hover:text-[#1c1917] font-bold text-sm transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#2fbf8f]">Buat Akun</button>
            </div>

            <div class="auth-overlay-content auth-overlay-register" aria-hidden="<?= $isRegister ? 'false' : 'true' ?>">
                <div class="w-16 h-16 mx-auto mb-5 bg-white/10 rounded-2xl grid place-items-center border border-white/20">
                    <i class="fa-solid fa-user-plus text-2xl text-[#2fbf8f]"></i>
                </div>
                <h2 class="text-3xl font-extrabold tracking-tight">Sudah Punya Akun?</h2>
                <p class="mt-4 text-stone-300 text-sm max-w-xs mx-auto leading-relaxed">Masuk kembali untuk melanjutkan pemantauan laporan fasilitas kampus Anda.</p>
                <button type="button" data-switch-mode="login" class="mt-8 px-7 py-3 rounded-full border border-white/40 hover:bg-white hover:text-[#1c1917] font-bold text-sm transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#2fbf8f]">Masuk</button>
            </div>
        </aside>
    </main>

    <script>
        const authCard = document.getElementById('auth-card');
        const loginPanel = document.getElementById('login-panel');
        const registerPanel = document.getElementById('register-panel');

        function setAuthMode(mode, updateHistory = true) {
            const isRegister = mode === 'register';
            authCard.classList.toggle('is-register', isRegister);
            loginPanel.setAttribute('aria-hidden', String(isRegister));
            registerPanel.setAttribute('aria-hidden', String(!isRegister));
            loginPanel.toggleAttribute('inert', isRegister);
            registerPanel.toggleAttribute('inert', !isRegister);
            document.querySelector('.auth-overlay-login').setAttribute('aria-hidden', String(isRegister));
            document.querySelector('.auth-overlay-register').setAttribute('aria-hidden', String(!isRegister));
            document.title = `${isRegister ? 'Daftar' : 'Login'} - SPACENEST FTST`;

            if (updateHistory) {
                const url = new URL(window.location.href);
                if (isRegister) {
                    url.searchParams.set('mode', 'register');
                } else {
                    url.searchParams.delete('mode');
                }
                window.history.pushState({}, '', url);
            }
        }

        document.querySelectorAll('[data-switch-mode]').forEach((button) => {
            button.addEventListener('click', () => setAuthMode(button.dataset.switchMode));
        });

        window.addEventListener('popstate', () => {
            setAuthMode(new URLSearchParams(window.location.search).get('mode') === 'register' ? 'register' : 'login', false);
        });
    </script>
</body>
</html>
