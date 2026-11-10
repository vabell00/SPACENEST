<?php
require_once __DIR__ . '/config/database.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id  = $_SESSION['user_id'];
$is_admin = ($_SESSION['role'] === 'admin');

// Query Laporan
if ($is_admin) {
    $stmt = $pdo->query("SELECT r.*, u.name AS reporter_name, u.prodi FROM reports r JOIN users u ON r.user_id = u.id ORDER BY r.created_at DESC");
} else {
    $stmt = $pdo->prepare("SELECT * FROM reports WHERE user_id = ? ORDER BY created_at DESC");
    $stmt->execute([$user_id]);
}
$reports = $stmt->fetchAll();

// Statistik
$by = fn($s) => count(array_filter($reports, fn($r) => $r['status'] === $s));
$statTotal   = count($reports);
$statVerif   = $by('Diverifikasi');
$statProses  = $by('Diproses');
$statSelesai = $by('Selesai');
$rate        = $statTotal ? round(($statSelesai / $statTotal) * 100) : 0;

$thisM = count(array_filter($reports, fn($r) => date('Y-m', strtotime($r['created_at'])) === date('Y-m')));
$lastM = count(array_filter($reports, fn($r) => date('Y-m', strtotime($r['created_at'])) === date('Y-m', strtotime('first day of last month'))));

// PERBAIKAN: Menambahkan operator minus (-) yang sebelumnya terpotong
$delta = $lastM > 0 ? sprintf('%+d%%', round((($thisM - $lastM) / $lastM) * 100)) : '+' . $thisM;

// Distribusi masalah: admin = per program studi, pelapor = per kategori
$dist = [];
foreach ($reports as $r) {
    $k = $is_admin ? ($r['prodi'] ?: 'Dosen / Staf') : $r['category'];
    $dist[$k] = ($dist[$k] ?? 0) + 1;
}
arsort($dist);
$distMax = $dist ? max($dist) : 1;

$statusChip = [
    'Dikirim'      => 'bg-stone-200 text-stone-700',
    'Diverifikasi' => 'bg-sky-100 text-sky-800',
    'Diproses'     => 'bg-indigo-100 text-indigo-800',
    'Selesai'      => 'bg-emerald-100 text-emerald-800',
];
$prioDot  = ['Rendah' => 'bg-emerald-500', 'Sedang' => 'bg-amber-500', 'Tinggi' => 'bg-rose-500'];
$barColor = ['#1c1917', '#2fbf8f', '#e07a10', '#3b82f6', '#a78bfa'];

include __DIR__ . '/includes/header.php';
?>

<div class="space-y-6 max-w-[1280px]">

    <?php if (($_GET['msg'] ?? '') === 'success'): ?>
        <div class="rounded-2xl bg-emerald-500/90 text-white text-sm font-semibold px-5 py-3" role="status">Laporan terkirim. Admin akan memverifikasinya.</div>
    <?php endif; ?>

    <!-- Judul -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div>
            <p class="text-xs font-bold uppercase tracking-[.16em] text-emerald-700">Dashboard <?= $is_admin ? 'pengelola' : 'pelapor' ?></p>
            <h1 class="mt-2 text-3xl md:text-4xl font-extrabold">Ringkasan Laporan</h1>
            <p class="mt-2 text-stone-600">Halo, <?= htmlspecialchars($_SESSION['name']) ?>. Pantau laporan fasilitas FTST dan perkembangan penanganannya di sini.</p>
        </div>
        <a href="<?= $is_admin ? 'admin_dashboard.php' : 'laporan_buat.php' ?>" class="self-start md:self-auto shrink-0 inline-flex items-center gap-3 rounded-full bg-[#1c1917] text-white font-bold px-7 py-4 shadow-xl hover:bg-black transition">
            <span class="w-5 h-5 rounded-full bg-[#2fbf8f] text-[#1c1917] grid place-items-center text-[10px]"><i class="fa-solid <?= $is_admin ? 'fa-screwdriver-wrench' : 'fa-plus' ?>"></i></span><?= $is_admin ? 'Kelola Laporan' : 'Buat Laporan Baru' ?>
        </a>
    </div>

    <!-- Kartu statistik -->
    <section id="statistik" class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5 scroll-mt-6">
        <div class="col-span-full">
            <h2 class="text-2xl font-bold">Statistik Laporan</h2>
            <p class="mt-1 text-sm text-stone-600">Ringkasan jumlah laporan dan progres penanganan.</p>
        </div>
        <?php
        $cards = [
            ['Total Laporan',       'fa-folder-open',        $statTotal,  $delta,   'bg-emerald-100 text-emerald-700', 'Bulan ini',            'text-[#1c1917]', false],
            ['Diverifikasi Admin', 'fa-circle-check',        $statVerif,  'Valid',  'bg-white/25 text-white',          'Siap ditindaklanjuti', 'text-white',     true],
            ['Sedang Diproses',    'fa-screwdriver-wrench', $statProses, 'Aktif',  'bg-amber-100 text-amber-700',     'Pengerjaan teknis',  'text-orange-600', false],
            ['Tingkat Penanganan', 'fa-chart-line',          $rate . '%', $statSelesai . ' Selesai', 'bg-black/5 text-stone-800', '',       'text-blue-600',   false],
        ];
        foreach ($cards as [$label, $icon, $val, $pill, $pillCls, $cap, $iconCls, $green]): ?>
            <div class="glass-panel <?= $green ? 'stat-green' : '' ?> p-7 min-h-[14rem] flex flex-col">
                <div class="flex items-start justify-between">
                    <span class="text-sm font-bold uppercase tracking-wide <?= $green ? '' : 'text-stone-700' ?>"><?= $label ?></span>
                    <span class="w-12 h-12 rounded-full grid place-items-center <?= $green ? 'bg-white/20' : 'bg-white shadow' ?> <?= $iconCls ?>"><?php if ($icon): ?><i class="fa-solid <?= $icon ?>"></i><?php endif; ?></span>
                </div>
                <div class="mt-auto">
                    <div class="text-5xl font-extrabold tight"><?= $val ?></div>
                    <div class="mt-3 flex items-center gap-2 text-sm font-semibold">
                        <span class="px-3 py-1 rounded-full <?= $pillCls ?>"><?= $pill ?></span>
                        <?php if ($cap): ?><span class="<?= $green ? '' : $iconCls ?>"><?= $cap ?></span><?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </section>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-5">

        <!-- Distribusi masalah -->
        <section class="glass-panel lg:col-span-5 p-7">
            <div class="flex flex-wrap items-start justify-between gap-3">
                <div>
                    <h2 class="text-xl font-bold"><?= $is_admin ? 'Distribusi Pelapor' : 'Kategori Laporan' ?></h2>
                    <p class="text-sm text-stone-600"><?= $is_admin ? 'Jumlah laporan menurut program studi pelapor.' : 'Jumlah laporan menurut kategori fasilitas.' ?></p>
                </div>
                <span class="px-4 py-2 rounded-full bg-white text-sm font-bold shadow-sm">FTST <?= date('Y') ?></span>
            </div>
            <?php if (!$dist): ?>
                <p class="mt-10 text-center text-sm text-stone-500">Belum ada data untuk ditampilkan.</p>
            <?php else: ?>
                <ul class="mt-7 space-y-5">
                    <?php $i = 0; foreach ($dist as $name => $n): ?>
                        <li>
                            <div class="flex justify-between text-sm font-semibold mb-1.5"><span><?= htmlspecialchars($name) ?></span><span><?= $n ?></span></div>
                            <div class="h-3.5 rounded-full bg-black/5" role="img" aria-label="<?= htmlspecialchars($name) ?>: <?= $n ?> laporan">
                                <div class="h-full rounded-full" style="width:<?= max(6, round(($n / $distMax) * 100)) ?>%;background:<?= $barColor[$i++ % count($barColor)] ?>"></div>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </section>

        <!-- Aktivitas laporan -->
        <section id="daftar" class="glass-panel lg:col-span-7 p-7 scroll-mt-6">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <h2 class="text-xl font-bold">Daftar Laporan</h2>
                    <p class="text-sm text-stone-600"><?= $is_admin ? 'Pilih laporan untuk membuka detail dan riwayatnya.' : 'Pilih laporan untuk melihat detail dan riwayat status.' ?></p>
                </div>
                <span class="shrink-0 rounded-full bg-stone-100 px-3 py-1 text-xs font-bold text-stone-700"><?= count($reports) ?> laporan</span>
                <?php if (count($reports) > 4): ?>
                    <button type="button" id="toggleAll" aria-expanded="false" class="text-emerald-800 font-bold text-sm whitespace-nowrap hover:underline">Lihat Semua <i class="fa-solid fa-arrow-right ml-1"></i></button>
                <?php endif; ?>
            </div>

            <?php if (empty($reports)): ?>
                <div class="mt-8 rounded-2xl bg-white/60 p-8 text-center text-sm text-stone-600">
                    <?php if ($is_admin): ?>
                        Belum ada laporan masuk. Laporan yang dikirim pengguna akan muncul di sini.
                    <?php else: ?>
                        Belum ada laporan. <a href="laporan_buat.php" class="font-bold underline">Buat laporan pertama</a> untuk mulai memantau fasilitas.
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <div id="list" class="mt-6 space-y-3 max-h-[26rem] overflow-y-auto pr-1">
                    <?php foreach ($reports as $n => $r): ?>
                        <a href="laporan_detail.php?id=<?= $r['id'] ?>" data-q="<?= htmlspecialchars(strtolower($r['facility_name'] . ' ' . $r['location'] . ' ' . $r['status'] . ' ' . $r['category'])) ?>"
                           class="item <?= $n >= 4 ? 'extra hidden' : '' ?> block rounded-2xl bg-white/75 hover:bg-white p-4 pl-5 transition">
                            <div class="flex items-center gap-2 text-xs">
                                <span class="font-mono text-stone-500">SPT-<?= str_pad($r['id'], 3, '0', STR_PAD_LEFT) ?></span>
                                <span class="px-3 py-1 rounded-full font-bold <?= $statusChip[$r['status']] ?>"><?= $r['status'] ?></span>
                                <span class="ml-auto flex items-center gap-1.5 text-stone-500"><span class="w-2 h-2 rounded-full <?= $prioDot[$r['priority']] ?>"></span>Prioritas <?= $r['priority'] ?></span>
                            </div>
                            <div class="mt-1.5 font-bold text-lg tight"><?= htmlspecialchars($r['facility_name']) ?></div>
                            <div class="flex items-center gap-2 text-stone-600 text-sm">
                                <i class="fa-solid fa-location-dot text-rose-600"></i><?= htmlspecialchars($r['location']) ?>
                                <?php if ($is_admin): ?><span class="ml-auto text-xs text-stone-500">Oleh <?= htmlspecialchars($r['reporter_name']) ?></span><?php endif; ?>
                            </div>
                        </a>
                    <?php endforeach; ?>
                    <p id="none" class="hidden text-center text-sm text-stone-500 py-6">Tidak ada laporan yang cocok dengan pencarian.</p>
                </div>
            <?php endif; ?>
        </section>
    </div>
</div>

<script>
(function () {
    var items = document.querySelectorAll('.item'), none = document.getElementById('none'),
        btn = document.getElementById('toggleAll'), q = document.getElementById('q'), all = false;
    function render() {
        var term = (q && q.value || '').trim().toLowerCase(), shown = 0;
        items.forEach(function (el) {
            var match = !term || el.dataset.q.indexOf(term) > -1;
            var hide = !match || (!term && !all && el.classList.contains('extra'));
            el.classList.toggle('hidden', hide); if (!hide) shown++;
        });
        if (none) none.classList.toggle('hidden', shown > 0 || !items.length);
    }
    if (btn) btn.addEventListener('click', function () {
        all = !all;
        btn.setAttribute('aria-expanded', String(all));
        btn.firstChild.nodeValue = all ? 'Ringkas ' : 'Lihat Semua ';
        render();
    });
    if (q) {
        q.addEventListener('input', render);
        q.form.addEventListener('submit', function (e) { e.preventDefault(); document.getElementById('daftar').scrollIntoView(); });
    }
    render();
})();
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>