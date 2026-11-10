<?php
require_once __DIR__ . '/config/database.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

// Update Status Handler (US-06, US-07)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $report_id  = filter_var($_POST['report_id'] ?? null, FILTER_VALIDATE_INT);
    $new_status = $_POST['status'] ?? '';
    $notes      = trim($_POST['notes'] ?? '');
    $statuses   = ['Dikirim', 'Diverifikasi', 'Diproses', 'Selesai'];

    if (!$report_id || !in_array($new_status, $statuses, true)) {
        header("Location: admin_dashboard.php?error=invalid");
        exit;
    }

    try {
        $pdo->beginTransaction();
        $checkStmt = $pdo->prepare("SELECT id FROM reports WHERE id = ? FOR UPDATE");
        $checkStmt->execute([$report_id]);
        if (!$checkStmt->fetchColumn()) {
            $pdo->rollBack();
            header("Location: admin_dashboard.php?error=missing");
            exit;
        }

        $stmt = $pdo->prepare("UPDATE reports SET status = ? WHERE id = ?");
        $stmt->execute([$new_status, $report_id]);
        $logStmt = $pdo->prepare("INSERT INTO status_logs (report_id, status, notes) VALUES (?, ?, ?)");
        $logStmt->execute([$report_id, $new_status, $notes]);
        $pdo->commit();
    } catch (PDOException $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        throw $e;
    }

    header("Location: admin_dashboard.php?updated=1");
    exit;
}

// Query Laporan
$reports = $pdo->query("SELECT r.*, u.name as reporter_name FROM reports r JOIN users u ON r.user_id = u.id ORDER BY r.created_at DESC")->fetchAll();
$statusCounts = array_count_values(array_column($reports, 'status'));
$statusStyles = [
    'Dikirim' => 'bg-stone-100 text-stone-700',
    'Diverifikasi' => 'bg-sky-100 text-sky-800',
    'Diproses' => 'bg-indigo-100 text-indigo-800',
    'Selesai' => 'bg-emerald-100 text-emerald-800',
];

include __DIR__ . '/includes/header.php';
?>

<div class="mx-auto max-w-[1280px] space-y-6">
    <?php if (($_GET['updated'] ?? '') === '1'): ?>
        <div class="rounded-2xl bg-emerald-100 px-5 py-3 text-sm font-semibold text-emerald-800" role="status">Status laporan berhasil diperbarui.</div>
    <?php elseif (($_GET['error'] ?? '') === 'invalid'): ?>
        <div class="rounded-2xl bg-rose-100 px-5 py-3 text-sm font-semibold text-rose-800" role="alert">Status atau ID laporan tidak valid.</div>
    <?php elseif (($_GET['error'] ?? '') === 'missing'): ?>
        <div class="rounded-2xl bg-rose-100 px-5 py-3 text-sm font-semibold text-rose-800" role="alert">Laporan tidak ditemukan.</div>
    <?php endif; ?>
    <div class="glass-panel p-6 rounded-3xl flex justify-between items-center">
        <div>
            <p class="text-xs font-bold uppercase tracking-[.16em] text-emerald-700">Ruang pengelola</p>
            <h1 class="mt-1 text-2xl md:text-3xl font-extrabold text-stone-900">Panel Admin FTST</h1>
            <p class="mt-2 text-sm text-stone-600">Tinjau setiap laporan, perbarui progres, dan tambahkan catatan penanganan.</p>
        </div>
        <a href="dashboard.php" class="hidden sm:inline-flex items-center gap-2 rounded-full border border-stone-200 bg-white px-4 py-2.5 text-xs font-bold text-stone-700 transition hover:bg-stone-100">
            <i class="fa-solid fa-chart-pie"></i> Ringkasan
        </a>
    </div>

    <section class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3 2xl:grid-cols-5" aria-label="Ringkasan status laporan">
        <?php
        $statusCards = [
            ['Semua Laporan', count($reports), 'fa-folder-open', 'bg-stone-100 text-stone-700'],
            ['Dikirim', $statusCounts['Dikirim'] ?? 0, 'fa-paper-plane', 'bg-slate-100 text-slate-700'],
            ['Diverifikasi', $statusCounts['Diverifikasi'] ?? 0, 'fa-circle-check', 'bg-sky-100 text-sky-700'],
            ['Diproses', $statusCounts['Diproses'] ?? 0, 'fa-screwdriver-wrench', 'bg-indigo-100 text-indigo-700'],
            ['Selesai', $statusCounts['Selesai'] ?? 0, 'fa-circle-check', 'bg-emerald-100 text-emerald-700'],
        ];
        foreach ($statusCards as [$label, $count, $icon, $color]): ?>
            <article class="glass-panel flex items-center gap-4 rounded-2xl p-5">
                <span class="grid h-11 w-11 shrink-0 place-items-center rounded-full <?= $color ?>"><i class="fa-solid <?= $icon ?>"></i></span>
                <div><p class="text-xs font-semibold text-stone-500"><?= $label ?></p><p class="text-2xl font-extrabold"><?= $count ?></p></div>
            </article>
        <?php endforeach; ?>
    </div>

    <div class="glass-panel p-6 rounded-3xl">
        <div class="mb-5">
            <h2 class="text-lg font-bold text-stone-900">Semua Laporan</h2>
            <p class="text-xs text-stone-500">Perbarui status untuk mencatat perkembangan penanganan.</p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-stone-300/60 text-stone-500 font-semibold uppercase">
                        <th class="pb-3 px-2">ID & Pelapor</th>
                        <th class="pb-3 px-2">Fasilitas & Lokasi</th>
                        <th class="pb-3 px-2">Status Saat Ini</th>
                        <th class="pb-3 px-2">Perbarui Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-200/50">
                    <?php if (!$reports): ?>
                        <tr><td colspan="4" class="px-4 py-12 text-center">
                            <span class="mx-auto mb-3 grid h-12 w-12 place-items-center rounded-full bg-stone-100 text-stone-500"><i class="fa-solid fa-inbox"></i></span>
                            <p class="font-bold text-stone-800">Belum ada laporan masuk</p>
                            <p class="mt-1 text-stone-500">Laporan baru dari pengguna akan muncul di halaman ini.</p>
                        </td></tr>
                    <?php endif; ?>
                    <?php foreach ($reports as $r): ?>
                        <tr class="hover:bg-white/40 transition">
                            <td class="py-3.5 px-2">
                                <div class="font-bold text-stone-900">#<?= $r['id'] ?> - <?= htmlspecialchars($r['reporter_name']) ?></div>
                                <div class="text-[11px] text-stone-400"><?= date('d M Y', strtotime($r['created_at'])) ?></div>
                            </td>
                            <td class="py-3.5 px-2">
                                <div class="font-semibold text-stone-800"><?= htmlspecialchars($r['facility_name']) ?></div>
                                <div class="text-[11px] text-stone-500"><?= htmlspecialchars($r['location']) ?></div>
                                <a href="laporan_detail.php?id=<?= $r['id'] ?>" class="mt-1 inline-flex items-center gap-1 text-[11px] font-semibold text-emerald-800 hover:underline">Lihat detail <i class="fa-solid fa-arrow-up-right-from-square"></i></a>
                            </td>
                            <td class="py-3.5 px-2">
                                <span class="px-2.5 py-1 rounded-full font-bold text-[10px] <?= $statusStyles[$r['status']] ?? 'bg-stone-100 text-stone-700' ?>"><?= htmlspecialchars($r['status']) ?></span>
                            </td>
                            <td class="py-3.5 px-2">
                                <form method="POST" class="flex items-center space-x-2">
                                    <input type="hidden" name="report_id" value="<?= $r['id'] ?>">
                                    <select name="status" class="px-2 py-1 rounded border border-stone-300 text-xs bg-white">
                                        <option value="Dikirim" <?= $r['status']==='Dikirim'?'selected':'' ?>>Dikirim</option>
                                        <option value="Diverifikasi" <?= $r['status']==='Diverifikasi'?'selected':'' ?>>Diverifikasi</option>
                                        <option value="Diproses" <?= $r['status']==='Diproses'?'selected':'' ?>>Diproses</option>
                                        <option value="Selesai" <?= $r['status']==='Selesai'?'selected':'' ?>>Selesai</option>
                                    </select>
                                    <input type="text" name="notes" placeholder="Catatan..." class="px-2 py-1 rounded border border-stone-300 text-xs bg-white/70 w-32">
                                    <button type="submit" name="update_status" class="px-3 py-1 rounded bg-stone-900 text-white font-semibold text-[11px] hover:bg-stone-800 transition">Update</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>