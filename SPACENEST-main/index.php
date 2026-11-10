<?php
// SPACENEST - Landing Page Publik (PHP murni, tanpa query database)
if (session_status() === PHP_SESSION_NONE) { session_start(); }
date_default_timezone_set('Asia/Jakarta');

$logged  = isset($_SESSION['user_id']);
$isAdmin = $logged && (($_SESSION['role'] ?? '') === 'admin');
$uname   = $logged ? ($_SESSION['name'] ?? 'Pengguna') : '';

$dashboardUrl = $isAdmin ? 'admin_dashboard.php' : 'dashboard.php';
$createUrl    = $logged ? ($isAdmin ? 'admin_dashboard.php' : 'laporan_buat.php') : 'register.php';
$createLabel  = $logged ? ($isAdmin ? 'Buka Dashboard' : 'Buka formulir laporan') : 'Daftar untuk mengirim';

// Status laporan: [label, waktu contoh, catatan admin]
$st = [
    ['Dikirim',      'Sen, 09.14', ''],
    ['Diverifikasi', 'Sen, 13.40', 'Data lengkap, diteruskan untuk ditangani.'],
    ['Diproses',     'Rab, 08.30', 'Perbaikan dijadwalkan, mohon area dikosongkan.'],
    ['Selesai',      'Jum, 15.20', 'Perbaikan tuntas.'],
];
// Contoh laporan: [judul, kategori, prioritas, lokasi]
$cases = [
    ['AC Lab Komputer tidak dingin',   'Hardware/Lab',     'Tinggi', 'Gedung FTST, Lt. 2'],
    ['Proyektor Ruang F.204 tidak menyala', 'Hardware/Lab',  'Sedang', 'Gedung FTST, Lt. 1'],
    ['WiFi lantai 3 sering putus',       'Jaringan/Internet', 'Sedang', 'Gedung FTST, Lt. 3'],
    ['Dua kursi kelas F.102 patah',      'Mebel/Ruangan',    'Rendah', 'Gedung FTST, Lt. 1'],
    ['Lampu koridor timur mati',         'Lobby Gedung F',          'Rendah', 'Gedung FTST, Lt. 2'],
    ['Spidol papan tulis habis',         'LAB AI',          'Rendah', 'Gedung FTST, Lt. 2'],
];
$chips = ['AC lab tidak dingin', 'Proyektor tidak menyala', 'WiFi lantai 3 lemot', 'Kursi kelas patah', 'Lampu koridor mati', 'Spidol papan tulis habis'];
$prioColor = ['Tinggi' => '#f43f5e', 'Sedang' => '#f59e0b', 'Rendah' => '#10b981'];

// Bar progres 4 segmen + label (n = indeks status aktif)
function bar(int $n, array $st): string {
    $h = '<div class="grid grid-cols-4 gap-2">';
    foreach ($st as $i => $_) {
        $s = $i < $n ? 'done' : ($i === $n ? 'active' : 'todo');
        $h .= '<span class="seg h-2.5 rounded-full" data-s="' . $s . '"></span>';
    }
    $h .= '</div><div class="mt-2 grid grid-cols-4 gap-2 text-center text-[11px] font-semibold text-stone-500">';
    foreach ($st as $x) { $h .= '<span>' . $x[0] . '</span>'; }
    return $h . '</div>';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SPACENEST</title>
    <meta name="description" content="Laporkan AC mati, proyektor error, atau WiFi lemot di FTST. Admin memverifikasi dan setiap perubahan status tercatat di riwayat laporan.">
    <script>document.documentElement.classList.add('js');</script>
    <!-- tambahkan di sini -->
<link rel="icon" type="image/svg+xml" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 44 44' fill='none'%3E%3Ccircle cx='22' cy='22' r='22' fill='%231c1917'/%3E%3Cpath d='M9.5 22a12.5 12.5 0 0 0 25 0' stroke='white' stroke-width='3' stroke-linecap='round'/%3E%3Ccircle cx='22' cy='17.5' r='4.6' fill='%232fbf8f'/%3E%3C/svg%3E">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <style>
        html { scroll-behavior: smooth; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; color: #1c1917; margin: 0; background-color: #fafaf9; }
        h1, h2, h3 { letter-spacing: -0.025em; }
        .num { font-variant-numeric: tabular-nums; }
        p, li, label, button, input { letter-spacing: 0; }

        .glass-panel { background: rgba(245,245,245,.8); border-radius: 2rem; backdrop-filter: blur(8px);
            border: 1px solid rgba(28,25,23,.09);
            box-shadow: 0 1px 0 rgba(255,255,255,.8) inset, 0 16px 36px -16px rgba(28,25,23,.18); }
        .btn-ink { background: #1c1917; color: #fff; transition: transform .2s, box-shadow .2s; }
        .btn-ink:hover { transform: translateY(-1px); box-shadow: 0 12px 24px -12px rgba(28,25,23,.7); }
        .btn-ink[disabled] { opacity: .45; pointer-events: none; }
        .btn-line { border: 1px solid rgba(28,25,23,.18); transition: background .2s; }
        .btn-line:hover { background: #fff; }
        #nav { transition: box-shadow .25s, backdrop-filter .25s; }
        #nav.is-s { box-shadow: 0 14px 30px -14px rgba(28,25,23,.35); backdrop-filter: blur(16px); }
/* tambahkan di sini */
.logo-mark .egg { transition: transform .35s cubic-bezier(.34,1.56,.64,1); }
a:hover .logo-mark .egg { transform: translateY(-3px); }
        /* Status */
        .chip { display: inline-flex; font-size: 11px; font-weight: 700; padding: .25rem .75rem; border-radius: 9999px; transition: background .25s, color .25s; }
        .c0 { background: #e7e5e4; color: #44403c; } .c1 { background: #e0f2fe; color: #075985; }
        .c2 { background: #e0e7ff; color: #3730a3; } .c3 { background: #d1fae5; color: #065f46; }
        .seg { background: #e7e5e4; transition: background .25s; }
        .seg[data-s=done] { background: #1c1917; } .seg[data-s=active] { background: #2fbf8f; }
        .dot { border: 1px solid #a8a29e; background: #fff; transition: background .25s, box-shadow .25s; }
        #tl li[data-s=done] .dot { background: #1c1917; border-color: #1c1917; }
        #tl li[data-s=active] .dot { background: #2fbf8f; border-color: #2fbf8f; box-shadow: 0 0 0 4px rgba(47,191,143,.25); }
        #tl li[data-s=todo] .lbl, #tl li[data-s=todo] .meta { color: #a8a29e; }
        .note { font-size: .75rem; font-style: italic; color: #57534e; max-height: 0; opacity: 0; overflow: hidden; transition: max-height .3s, opacity .3s; }
        #tl li[data-s=active] .note:not(:empty) { max-height: 4rem; opacity: 1; margin-top: .375rem; padding: .375rem .625rem;
            border-left: 2px solid #2fbf8f; background: rgba(255,255,255,.7); border-radius: 0 .5rem .5rem 0; }
        #t-fields { transition: opacity .18s; } #t-fields.out { opacity: 0; }

        /* Kontrol pilihan */
        .pill { border: 1px solid #d6d3d1; background: #fff; border-radius: 9999px; padding: .5rem 1rem; font-size: .8125rem; font-weight: 600;
                color: #44403c; transition: background .2s, color .2s, border-color .2s; }
        .pill:hover { border-color: #78716c; }
        .pill[aria-pressed=true] { background: #1c1917; border-color: #1c1917; color: #fff; }

        /* Tab alur kerja */
        .tab { text-align: left; width: 100%; padding: 1.25rem; border-radius: 1.25rem; border: 1px solid rgba(28,25,23,.12);
               background: rgba(255,255,255,.6); transition: background .25s, color .25s, border-color .25s; }
        .tab:hover { border-color: rgba(28,25,23,.35); }
        .tab .n { width: 2.5rem; height: 2.5rem; border-radius: 9999px; background: #1c1917; color: #fff; display: grid; place-items: center; font-weight: 800; font-size: .875rem; }
        .tab .d { color: #57534e; }
        .tab[aria-selected=true] { background: #1c1917; color: #fff; border-color: #1c1917; }
        .tab[aria-selected=true] .n { background: #2fbf8f; color: #1c1917; }
        .tab[aria-selected=true] .d { color: #d6d3d1; }
        .panel.enter { animation: fadein .28s ease-out; }

        @keyframes rise { from { opacity: 0; transform: translateY(12px); } to { opacity: 1; transform: none; } }
        @keyframes fadein { from { opacity: 0; transform: translateY(8px); } to { opacity: 1; transform: none; } }
        .js .rise { opacity: 0; animation: rise .6s ease-out forwards; animation-delay: calc(var(--i, 0) * 90ms); }

        :focus-visible { outline: 3px solid #2fbf8f; outline-offset: 2px; }
        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after { transition: none !important; animation: none !important; scroll-behavior: auto !important; }
            .js .rise { opacity: 1; }
        }
    </style>
</head>
<body class="min-h-screen overflow-x-hidden">

    <!-- ================= NAVBAR ================= -->
    <div class="max-w-6xl mx-auto px-5 md:px-8 sticky top-3 z-50 pt-3">
        <header id="nav" class="glass-panel !rounded-full flex items-center justify-between gap-3 pl-3 pr-2 py-2">
            <a href="index.php" class="flex items-center gap-3 min-w-0" aria-label="SPACENEST, beranda">
<svg class="logo-mark w-10 h-10 shrink-0" viewBox="0 0 44 44" fill="none" aria-hidden="true">
    <circle cx="22" cy="22" r="22" fill="#1c1917"/>
    <path d="M9.5 22a12.5 12.5 0 0 0 25 0" stroke="#fff" stroke-width="3" stroke-linecap="round"/>
    <path d="M15 22.5a7 7 0 0 0 14 0" stroke="#fff" stroke-opacity=".4" stroke-width="2.4" stroke-linecap="round"/>
    <circle class="egg" cx="22" cy="17.5" r="4.6" fill="#2fbf8f"/>
</svg>                <span class="leading-tight">
                    <span class="block font-extrabold text-base tracking-tight">SPACENEST</span>
                    <span class="hidden sm:block text-xs text-stone-500">Sistem Pengajuan dan Administrasi Catatan Eksklusif dengan Navigasi Efisiensi Sarana Tempat</span>
                </span>
            </a>
            <nav class="flex items-center gap-1.5 text-sm font-bold shrink-0" aria-label="Navigasi publik">
                <?php if ($logged): ?>
                    <span class="hidden md:inline text-xs font-medium text-stone-500 mr-2">Masuk sebagai <?= htmlspecialchars($uname) ?></span>
                    <a href="<?= $dashboardUrl ?>" class="btn-ink px-5 py-2.5 rounded-full">Buka Dashboard</a>
                <?php else: ?>
                    <a href="login.php" class="px-4 py-2.5 rounded-full text-stone-700 hover:bg-stone-900/5 transition">Masuk</a>
                    <a href="register.php" class="btn-ink px-4 sm:px-5 py-2.5 rounded-full">Daftar<span class="hidden sm:inline"> Akun</span></a>
                <?php endif; ?>
            </nav>
        </header>
    </div>

    <main>
        <!-- ================= HERO ================= -->
        <section class="max-w-6xl mx-auto px-5 md:px-8 pt-12 pb-20 md:pt-16 md:pb-28 grid lg:grid-cols-12 items-center gap-12">
            <div class="lg:col-span-7">
                <h1 class="rise text-[clamp(2.5rem,5vw,4.25rem)] font-extrabold leading-[1.05] text-balance" style="--i:0">
                    Setiap laporan punya jejak. Setiap perubahan tercatat.
                </h1>
                <p class="rise mt-6 max-w-xl text-[1.0625rem] leading-[1.7] text-stone-600" style="--i:1">
                    Kirim sekali dengan lokasi dan foto. Admin FTST memeriksa dan memperbarui statusnya,
                    dan setiap perubahan tercatat di riwayat laporan, jadi Anda tahu sudah sampai mana.
                </p>

                <div class="rise mt-8 flex flex-wrap gap-3" style="--i:2">
                    <?php if ($logged): ?>
                        <a href="<?= $dashboardUrl ?>" class="btn-ink px-7 py-4 rounded-2xl font-bold">Buka Dashboard</a>
                        <?php if (!$isAdmin): ?>
                            <a href="laporan_buat.php" class="btn-line px-7 py-4 rounded-2xl font-bold text-stone-800">Buat laporan baru</a>
                        <?php endif; ?>
                    <?php else: ?>
                        <a href="register.php" class="btn-ink px-7 py-4 rounded-2xl font-bold">Buat laporan pertama</a>
                        <a href="login.php" class="btn-line px-7 py-4 rounded-2xl font-bold text-stone-800">Sudah punya akun</a>
                    <?php endif; ?>
                </div>

                <div class="rise mt-12" style="--i:3">
                    <p class="text-sm text-stone-500">Klik salah satu untuk melihat contoh laporannya</p>
                    <div id="chips" class="mt-3 flex flex-wrap gap-2">
                        <?php foreach ($chips as $i => $c): ?>
                            <button type="button" class="pill" aria-pressed="<?= $i === 0 ? 'true' : 'false' ?>"><?= $c ?></button>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- Tiket contoh -->
            <div class="rise lg:col-span-5 w-full max-w-md mx-auto lg:mx-0 lg:ml-auto" style="--i:2">
                <div id="ticket" class="glass-panel !rounded-[1.75rem] p-6">
                    <div class="flex items-center justify-between gap-3">
                        <span class="rounded-md bg-[#1c1917] px-2.5 py-1 text-xs font-semibold text-white">Contoh laporan</span>
                        <span id="stamp" class="chip c2">Diproses</span>
                    </div>

                    <div id="t-fields">
                        <p class="num mt-5 text-xs font-bold text-stone-500">#142</p>
                        <h2 id="t-title" class="mt-1 min-h-[3.5rem] text-xl font-extrabold leading-tight"><?= $cases[0][0] ?></h2>
                        <dl class="mt-4 grid grid-cols-3 gap-3 text-xs">
                            <div><dt class="text-stone-500">Kategori</dt><dd id="t-cat" class="mt-0.5 font-bold"><?= $cases[0][1] ?></dd></div>
                            <div><dt class="text-stone-500">Prioritas</dt><dd class="mt-0.5 flex items-center gap-1.5 font-bold"><span id="t-dot" class="h-1.5 w-1.5 rounded-full" style="background:<?= $prioColor[$cases[0][2]] ?>"></span><span id="t-prio"><?= $cases[0][2] ?></span></dd></div>
                            <div><dt class="text-stone-500">Lokasi</dt><dd id="t-loc" class="mt-0.5 font-bold"><?= $cases[0][3] ?></dd></div>
                        </dl>
                    </div>

                    <div class="relative my-6 border-t-2 border-dashed border-stone-900/15" aria-hidden="true">
                        <span class="absolute -left-9 -top-3 h-6 w-6 rounded-full border border-stone-900/10 bg-stone-50"></span>
                        <span class="absolute -right-9 -top-3 h-6 w-6 rounded-full border border-stone-900/10 bg-stone-50"></span>
                    </div>

                    <p class="mb-3 text-sm font-bold">Riwayat status</p>
                    <ol id="tl" class="min-h-[13rem] space-y-3">
                        <?php foreach ($st as $i => $s): $state = $i < 2 ? 'done' : ($i === 2 ? 'active' : 'todo'); ?>
                        <li data-s="<?= $state ?>" class="flex gap-3">
                            <span class="dot mt-1.5 h-2.5 w-2.5 shrink-0 rounded-full"></span>
                            <div class="min-w-0">
                                <p class="text-sm"><b class="lbl"><?= $s[0] ?></b> <span class="meta num ml-1 text-stone-500"><?= $s[1] ?></span></p>
                                <p class="note"><?= $s[2] ?></p>
                            </div>
                        </li>
                        <?php endforeach; ?>
                    </ol>

                    <div class="mt-4"><?= bar(2, $st) ?></div>
                </div>

                <div id="segctl" class="mt-4 grid grid-cols-4 gap-2" role="group" aria-label="Ubah status contoh laporan">
                    <?php foreach ($st as $i => $s): ?>
                        <button type="button" class="pill !px-2 !py-2.5 text-center" aria-pressed="<?= $i === 2 ? 'true' : 'false' ?>"><?= $s[0] ?></button>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <!-- ================= ALUR KERJA ================= -->
        <section id="alur" class="max-w-6xl mx-auto px-5 md:px-8 py-20 md:py-28 border-t border-stone-900/10 scroll-mt-16">
            <div class="max-w-2xl">
                <h2 class="text-[clamp(2rem,3.5vw,3rem)] font-extrabold leading-[1.1] text-balance">Tiga langkah dari laporan sampai selesai</h2>
                <p class="mt-4 max-w-xl text-[1.0625rem] leading-[1.7] text-stone-600">Pilih langkahnya untuk melihat apa yang terjadi di sistem.</p>
            </div>

            <div class="mt-12 grid gap-8 lg:grid-cols-12">
                <div class="lg:col-span-5 space-y-3" role="tablist" aria-label="Langkah pelaporan" aria-orientation="vertical">
                    <?php
                    $tabs = [
                        ['Buat Laporan',      'Sebut fasilitas dan lokasinya, pilih kategori dan prioritas, lampirkan foto bila ada.'],
                        ['Verifikasi Admin',  'Admin FTST mengecek laporan, mengubah statusnya, dan boleh menambah catatan.'],
                        ['Proses & Selesai',  'Status naik ke Diproses lalu Selesai. Tiap perubahan masuk riwayat laporan.'],
                    ];
                    foreach ($tabs as $i => $t): ?>
                    <button type="button" role="tab" id="tab-<?= $i ?>" aria-controls="panel-<?= $i ?>" aria-selected="<?= $i === 0 ? 'true' : 'false' ?>" tabindex="<?= $i === 0 ? '0' : '-1' ?>" class="tab">
                        <span class="grid grid-cols-[2.5rem_1fr] items-center gap-x-4 gap-y-2">
                            <span class="n num"><?= $i + 1 ?></span>
                            <span class="text-lg font-bold"><?= $t[0] ?></span>
                            <span class="d col-start-2 text-sm leading-relaxed"><?= $t[1] ?></span>
                        </span>
                    </button>
                    <?php endforeach; ?>
                </div>

                <div class="lg:col-span-7">
                    <div class="glass-panel min-h-[25rem] p-6 md:p-8">
                        <!-- Panel 1: formulir -->
                        <div role="tabpanel" id="panel-0" aria-labelledby="tab-0" class="panel">
                            <p class="text-sm text-stone-500">Contoh formulir laporan</p>
                            <label for="f-name" class="mt-5 block text-xs font-semibold text-stone-500">Nama fasilitas</label>
                            <input id="f-name" type="text" value="Proyektor Ruang B.204" class="mt-1.5 w-full rounded-2xl border border-stone-300 bg-white px-4 py-3 text-sm">
                            <p class="mt-5 text-xs font-semibold text-stone-500">Kategori</p>
                            <div class="mt-2 flex flex-wrap gap-2" data-group>
                                <?php foreach (['Hardware/Lab', 'Jaringan/Internet', 'Mebel/Ruangan', 'Lainnya'] as $k => $c): ?>
                                    <button type="button" class="pill" aria-pressed="<?= $k === 0 ? 'true' : 'false' ?>"><?= $c ?></button>
                                <?php endforeach; ?>
                            </div>
                            <p class="mt-5 text-xs font-semibold text-stone-500">Prioritas</p>
                            <div class="mt-2 flex flex-wrap gap-2" data-group>
                                <?php foreach (['Rendah', 'Sedang', 'Tinggi'] as $k => $c): ?>
                                    <button type="button" class="pill" aria-pressed="<?= $k === 1 ? 'true' : 'false' ?>"><?= $c ?></button>
                                <?php endforeach; ?>
                            </div>
                            <div class="mt-5 flex items-center gap-3 rounded-2xl border border-dashed border-stone-400 bg-white/60 px-4 py-3 text-sm text-stone-500">
                                <i class="fa-regular fa-image"></i> Foto kerusakan (opsional)
                            </div>
                            <a href="<?= $createUrl ?>" class="btn-ink mt-6 inline-block rounded-2xl px-6 py-3.5 text-sm font-bold"><?= $createLabel ?></a>
                        </div>

                        <!-- Panel 2: admin -->
                        <div role="tabpanel" id="panel-1" aria-labelledby="tab-1" class="panel">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <p class="num text-xs font-bold text-stone-500">#142</p>
                                    <p class="mt-1 font-bold">Proyektor Ruang B.204 tidak menyala</p>
                                </div>
                                <span id="adm-chip" class="chip c0">Dikirim</span>
                            </div>
                            <label for="adm-note" class="mt-6 block text-xs font-semibold text-stone-500">Catatan untuk pelapor (opsional)</label>
                            <input id="adm-note" type="text" maxlength="120" placeholder="Contoh: foto sudah jelas, lanjut ditangani" class="mt-1.5 w-full rounded-2xl border border-stone-300 bg-white px-4 py-3 text-sm">
                            <div class="mt-4 flex flex-wrap gap-2">
                                <button id="adm-save" type="button" class="btn-ink rounded-2xl px-5 py-3 text-sm font-bold">Simpan status Diverifikasi</button>
                                <button id="adm-reset" type="button" class="btn-line rounded-2xl px-5 py-3 text-sm font-bold text-stone-700">Ulangi</button>
                            </div>
                            <p class="mt-6 border-t border-stone-900/10 pt-4 text-sm font-bold">Riwayat laporan</p>
                            <ol id="adm-log" class="mt-3 space-y-2 text-sm"></ol>
                        </div>

                        <!-- Panel 3: riwayat -->
                        <div role="tabpanel" id="panel-2" aria-labelledby="tab-2" class="panel">
                            <p class="text-sm text-stone-500">Riwayat laporan #142</p>
                            <ol class="mt-4 space-y-4">
                                <?php foreach ($st as $i => $s): ?>
                                <li class="flex gap-3">
                                    <span class="chip c<?= $i ?> h-fit shrink-0"><?= $s[0] ?></span>
                                    <div class="text-sm">
                                        <p class="num text-stone-500"><?= $s[1] ?></p>
                                        <?php if ($s[2] !== ''): ?><p class="mt-0.5"><?= $s[2] ?></p><?php endif; ?>
                                    </div>
                                </li>
                                <?php endforeach; ?>
                            </ol>
                            <div class="mt-6 border-t border-stone-900/10 pt-5"><?= bar(3, $st) ?></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- CTA penutup -->
            <div class="mt-16 flex flex-col items-start justify-between gap-6 rounded-[2rem] bg-[#1c1917] p-8 text-white md:flex-row md:items-center md:p-12">
                <div>
                    <h2 class="text-3xl font-extrabold leading-tight">Ada fasilitas yang rusak?</h2>
                    <p class="mt-2 max-w-md leading-relaxed text-stone-300">Laporkan sekarang dan pantau statusnya sampai selesai.</p>
                </div>
                <a href="<?= $logged ? $dashboardUrl : 'register.php' ?>" class="shrink-0 rounded-2xl bg-white px-7 py-4 font-bold text-[#1c1917] transition hover:-translate-y-px hover:shadow-lg">
                    <?= $logged ? 'Buka Dashboard' : 'Buat laporan pertama' ?>
                </a>
            </div>
        </section>
    </main>

    <!-- ================= FOOTER ================= -->
    <footer class="border-t border-stone-900/10">
        <div class="max-w-6xl mx-auto px-5 md:px-8 py-8 flex flex-col gap-2 text-sm text-stone-500 sm:flex-row sm:items-center sm:justify-between">
            <p>&copy; <?= date('Y') ?> Kelompok 2 - SPACENEST</p>
            <p>PPL Informatika UM Indonesia</p>
        </div>
    </footer>

<script>
(function () {
    var $ = function (s) { return document.querySelector(s); };
    var $$ = function (s) { return Array.prototype.slice.call(document.querySelectorAll(s)); };
    var rm = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    var names = ['Dikirim', 'Diverifikasi', 'Diproses', 'Selesai'];
    var cases = <?= json_encode($cases, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) ?>;
    var pc = <?= json_encode($prioColor) ?>;
    var times = <?= json_encode(array_column($st, 1)) ?>;

    // Navbar: bayangan saat scroll
    var nav = $('#nav');
    function onScroll() { nav.classList.toggle('is-s', window.scrollY > 8); }
    window.addEventListener('scroll', onScroll, { passive: true }); onScroll();

    // Pilihan tunggal (pil kategori dan prioritas)
    $$('[data-group]').forEach(function (g) {
        g.addEventListener('click', function (e) {
            var b = e.target.closest('button'); if (!b) return;
            Array.prototype.forEach.call(g.querySelectorAll('button'), function (x) { x.setAttribute('aria-pressed', x === b); });
        });
    });

    // ===== Tiket contoh =====
    var T = $('#ticket'), stamp = $('#stamp');
    var lis = $$('#tl li'), segs = $$('#ticket .seg'), sb = $$('#segctl button');
    var cur = 2, timer = null, manual = false;

    function setS(n) {
        cur = n;
        lis.forEach(function (li, i) { li.dataset.s = i < n ? 'done' : (i === n ? 'active' : 'todo'); });
        segs.forEach(function (s, i) { s.dataset.s = i < n ? 'done' : (i === n ? 'active' : 'todo'); });
        stamp.textContent = names[n]; stamp.className = 'chip c' + n;
        sb.forEach(function (b, i) { b.setAttribute('aria-pressed', i === n); });
    }
    function stop() { clearInterval(timer); timer = null; }
    function play() {
        stop(); if (rm || manual || document.hidden) return;
        timer = setInterval(function () { setS((cur + 1) % 4); }, 3200);
    }
    T.addEventListener('mouseenter', stop); T.addEventListener('mouseleave', play);
    T.addEventListener('focusin', stop);    T.addEventListener('focusout', play);
    document.addEventListener('visibilitychange', function () { document.hidden ? stop() : play(); });
    sb.forEach(function (b, i) { b.addEventListener('click', function () { manual = true; stop(); setS(i); }); });
    play();

    // Chip contoh laporan
    var chips = $$('#chips button'), box = $('#t-fields');
    chips.forEach(function (b, i) {
        b.addEventListener('click', function () {
            chips.forEach(function (x) { x.setAttribute('aria-pressed', x === b); });
            box.classList.add('out');
            setTimeout(function () {
                var d = cases[i];
                $('#t-title').textContent = d[0]; $('#t-cat').textContent = d[1];
                $('#t-prio').textContent = d[2];  $('#t-loc').textContent = d[3];
                $('#t-dot').style.background = pc[d[2]];
                box.classList.remove('out');
            }, rm ? 0 : 180);
        });
    });

    // ===== Tab alur kerja =====
    var tabs = $$('[role=tab]'), panels = $$('[role=tabpanel]');
    var admChip = $('#adm-chip'), admNote = $('#adm-note'), admSave = $('#adm-save'), admLog = $('#adm-log');

    function logItem(label, time, note) {
        var li = document.createElement('li'); li.className = 'flex flex-wrap gap-x-2';
        var b = document.createElement('b'); b.textContent = label;
        var t = document.createElement('span'); t.className = 'num text-stone-500'; t.textContent = time;
        li.appendChild(b); li.appendChild(t);
        if (note) { var n = document.createElement('p'); n.className = 'w-full text-xs italic text-stone-600'; n.textContent = note; li.appendChild(n); }
        admLog.appendChild(li);
    }
    function resetAdm() {
        admChip.textContent = 'Dikirim'; admChip.className = 'chip c0';
        admNote.value = ''; admSave.disabled = false; admLog.innerHTML = '';
        logItem('Dikirim', times[0], '');
    }
    $('#adm-save').addEventListener('click', function () {
        admChip.textContent = 'Diverifikasi'; admChip.className = 'chip c1';
        admSave.disabled = true;
        logItem('Diverifikasi', times[1], admNote.value.trim() || 'Data lengkap, diteruskan untuk ditangani.');
    });
    $('#adm-reset').addEventListener('click', resetAdm);

    function sel(i, focus) {
        tabs.forEach(function (t, k) { t.setAttribute('aria-selected', k === i); t.tabIndex = k === i ? 0 : -1; });
        panels.forEach(function (p, k) {
            p.hidden = k !== i;
            if (k === i) { p.classList.remove('enter'); void p.offsetWidth; p.classList.add('enter'); }
        });
        if (i === 1) resetAdm();
        if (focus) tabs[i].focus();
    }
    tabs.forEach(function (t, i) {
        t.addEventListener('click', function () { sel(i, false); });
        t.addEventListener('keydown', function (e) {
            var n = tabs.length, k = null;
            if (e.key === 'ArrowDown' || e.key === 'ArrowRight') k = (i + 1) % n;
            else if (e.key === 'ArrowUp' || e.key === 'ArrowLeft') k = (i - 1 + n) % n;
            else if (e.key === 'Home') k = 0;
            else if (e.key === 'End') k = n - 1;
            if (k !== null) { e.preventDefault(); sel(k, true); }
        });
    });
    sel(0, false);
})();
</script>
</body>
</html>