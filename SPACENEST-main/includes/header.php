<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
date_default_timezone_set('Asia/Jakarta');

$page   = basename($_SERVER['SCRIPT_NAME']);
$logged  = isset($_SESSION['user_id']);
$isAdmin = $logged && ($_SESSION['role'] ?? '') === 'admin';
$uname   = $logged ? $_SESSION['name'] : '';

$initials = '';
foreach (array_slice(preg_split('/\s+/', trim($uname)), 0, 2) as $w) {
    if (preg_match('/^./u', $w, $m)) { $initials .= function_exists('mb_strtoupper') ? mb_strtoupper($m[0]) : strtoupper($m[0]); }
}

$rail = [
    ['index.php',          'fa-house',        'Dashboard',      $page === 'index.php'],
    ['laporan_buat.php',   'fa-plus',         'Buat laporan',   $page === 'laporan_buat.php'],
    ['index.php#daftar',   'fa-list-check',   'Daftar laporan', false],
];
if ($isAdmin) { $rail[] = ['admin_dashboard.php', 'fa-screwdriver-wrench', 'Panel admin', $page === 'admin_dashboard.php']; }
$rail[] = ['index.php#statistik', 'fa-chart-pie', 'Statistik', false];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SPACENEST — Monitoring Fasilitas FTST</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <style>
        :root { --ink:#1c1917; --mint:#2fbf8f; }
        html { scroll-behavior: smooth; }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif; color: var(--ink); min-height: 100vh;
            background: #ffffff;
            margin: 0;
        }
        h1, h2, .tight { letter-spacing: -0.04em; }

        /* Header fixed di atas */
        .site-header {
            position: fixed; top: 0; left: 0; right: 0; z-index: 50; background: #ffffff;
        }

        /* Sidebar fixed murni di luar flex flow */
        .site-sidebar {
            position: fixed; left: 1.5rem; top: 6rem; z-index: 40;
            display: flex; flex-direction: column; align-items: center; gap: 1rem;
            width: 5.5rem; padding: 1.5rem 0; background: rgba(245,245,245, .95);
            backdrop-filter: blur(8px);
            border-radius: 2rem; border: 1px solid rgba(0,0,0,.1);
        }

        /* Wrapper konten utama diberi jarak kiri yang aman dari sidebar */
        .main-content-wrapper {
            margin-top: 4.5rem;
            margin-left: 7rem; /* Jarak aman agar tidak tertutup sidebar */
            margin-right: 1.5rem;
            padding-bottom: 3rem;
        }

        .pill { background: rgba(245,245,245, .8); border: 1px solid rgba(0,0,0,.1); box-shadow: 0 4px 12px -4px rgba(0,0,0,.05); }
        .stage { background: transparent; min-height: 70vh; }

        /* Panel & kartu */
        .glass-panel { background: rgba(245,245,245, .8); border-radius: 2rem; box-shadow: 0 4px 20px -4px rgba(0,0,0,.05); }
        .stat-green  { background: linear-gradient(150deg, #34c496 0%, #159a70 100%); color: #fff; box-shadow: 0 10px 25px -10px rgba(16,150,110,.5); }

        /* Tombol rail */
        .rail-btn {
            width: 3rem; height: 3rem; border-radius: 9999px; display: grid; place-items: center; font-size: 0.95rem;
            background: #ffffff; border: 1px solid rgba(0,0,0,.1); box-shadow: 0 4px 10px -4px rgba(0,0,0,.1); transition: transform .15s;
        }
        .rail-btn:hover { transform: scale(1.06); }
        .rail-btn.on { width: 3.5rem; height: 3.5rem; border: 2.5px solid var(--ink); }
        :focus-visible { outline: 3px solid #10b981; outline-offset: 2px; }

        @media (max-width: 767px) {
            .site-header { position: relative; }
            .site-sidebar { position: relative; left: auto; top: auto; width: 100% !important; flex-direction: row !important; overflow-x: auto; padding: .75rem 1rem !important; border-radius: 1.5rem; margin-bottom: 1rem; }
            .main-content-wrapper { margin-top: 0; margin-left: 1rem; margin-right: 1rem; }
            .rail-btn, .rail-btn.on { width: 3rem; height: 3rem; flex: none; }
            .site-sidebar .spacer { display: none; }
        }
        @media (prefers-reduced-motion: reduce) { * { transition: none !important; scroll-behavior: auto !important; } }
    </style>
</head>
<body class="p-3 md:p-6">

    <!-- Bilah atas (Fixed) -->
    <header class="site-header flex items-center justify-between gap-2 px-4 md:px-8 py-3 border-b border-stone-100">
        <div class="flex items-center gap-3 min-w-0 shrink-0">
            <div class="w-11 h-11 shrink-0 rounded-full bg-[#1c1917] text-white grid place-items-center text-sm font-extrabold"><?= $logged ? htmlspecialchars($initials) : 'SN' ?></div>
            <div class="leading-tight min-w-0 hidden sm:block">
                <?php if ($logged): ?>
                    <div class="text-[10px] font-semibold uppercase tracking-wide text-stone-600 flex items-center gap-1.5">Welcome back <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span></div>
                    <div class="font-bold text-base tight truncate">Hi, <?= htmlspecialchars($uname) ?></div>
                <?php else: ?>
                    <div class="font-extrabold text-base tight">SPACENEST</div>
                    <div class="text-[11px] text-stone-600">Monitoring Fasilitas FTST</div>
                <?php endif; ?>
            </div>
        </div>

        <?php if ($logged): ?>
        <!-- Form pencarian -->
        <form action="index.php" method="get" class="pill rounded-full flex items-center gap-2 px-4 py-1.5 flex-1 max-w-md mx-2" role="search">
            <input id="q" name="q" value="<?= htmlspecialchars($_GET['q'] ?? '') ?>" type="search" autocomplete="off"
                   placeholder="Cari laporan, lab, atau fasilitas..." aria-label="Cari laporan"
                   class="flex-1 bg-transparent text-xs md:text-sm py-1.5 outline-none placeholder:text-stone-500 min-w-0">
            <button type="button" id="mic" aria-label="Cari dengan suara" class="w-8 h-8 rounded-full bg-[#1c1917] text-white grid place-items-center text-xs shrink-0"><i class="fa-solid fa-microphone"></i></button>
        </form>

        <nav class="pill rounded-full p-1 flex items-center gap-1 text-xs md:text-sm font-bold shrink-0" aria-label="Tampilan">
            <a href="index.php" class="px-3 py-2 rounded-full flex items-center gap-1.5 <?= $page !== 'admin_dashboard.php' ? 'bg-[#1c1917] text-white' : 'text-stone-800 hover:bg-stone-100' ?>">
                <i class="fa-solid fa-user-graduate"></i><span class="hidden xl:inline"><?= $isAdmin ? 'Pelapor (Mhs/Dosen)' : 'Pelapor' ?></span>
            </a>
            <?php if ($isAdmin): ?>
            <a href="admin_dashboard.php" class="px-3 py-2 rounded-full flex items-center gap-1.5 <?= $page === 'admin_dashboard.php' ? 'bg-[#1c1917] text-white' : 'text-stone-800 hover:bg-stone-100' ?>">
                <i class="fa-solid fa-shield-halved"></i><span class="hidden xl:inline">Admin FTST</span>
            </a>
            <?php endif; ?>
            <a href="logout.php" class="px-3 py-2 rounded-full text-rose-700 hover:bg-rose-50" aria-label="Keluar"><i class="fa-solid fa-right-from-bracket"></i></a>
        </nav>
        <?php else: ?>
        <nav class="pill rounded-full p-1.5 flex items-center gap-1 text-sm font-bold">
            <a href="login.php" class="px-5 py-2.5 rounded-full <?= $page === 'login.php' ? 'bg-[#1c1917] text-white' : 'hover:bg-stone-100' ?>">Masuk</a>
            <a href="register.php" class="px-5 py-2.5 rounded-full <?= $page === 'register.php' ? 'bg-[#1c1917] text-white' : 'hover:bg-stone-100' ?>">Daftar</a>
        </nav>
        <?php endif; ?>
    </header>

    <?php if ($logged): ?>
    <!-- Sidebar Fixed (Dipisah dari aliran dokumen utama) -->
    <aside class="site-sidebar border border-stone-200" aria-label="Navigasi utama">
        <?php foreach ($rail as [$href, $icon, $label, $on]): ?>
            <a href="<?= $href ?>" title="<?= $label ?>" aria-label="<?= $label ?>" <?= $on ? 'aria-current="page"' : '' ?> class="rail-btn <?= $on ? 'on' : '' ?>"><i class="fa-solid <?= $icon ?>"></i></a>
        <?php endforeach; ?>
        <div class="spacer flex-1"></div>
        <button type="button" onclick="location.reload()" title="Muat ulang" aria-label="Muat ulang" class="rail-btn text-rose-600"><i class="fa-solid fa-rotate-right"></i></button>
        <span class="hidden md:grid w-12 h-12 rounded-full bg-white border border-stone-200 place-items-center text-[11px] font-bold text-stone-700 shadow-sm">FTST</span>
    </aside>
    <?php endif; ?>

    <!-- Pembungkus Konten Utama -->
    <div class="main-content-wrapper">
        <main class="stage min-w-0 w-full">