<?php
require_once __DIR__ . '/config/database.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$id = $_GET['id'] ?? 0;
$stmt = $pdo->prepare("SELECT r.*, u.name as reporter_name, u.email FROM reports r JOIN users u ON r.user_id = u.id WHERE r.id = ?");
$stmt->execute([$id]);
$report = $stmt->fetch();

if (!$report) {
    header("Location: index.php");
    exit;
}

// Cek hak akses jika bukan admin atau pemilik
if ($_SESSION['role'] !== 'admin' && $report['user_id'] != $_SESSION['user_id']) {
    die("Akses ditolak.");
}

// Ambil Status Logs (US-10)
$logStmt = $pdo->prepare("SELECT * FROM status_logs WHERE report_id = ? ORDER BY created_at ASC");
$logStmt->execute([$id]);
$logs = $logStmt->fetchAll();

include __DIR__ . '/includes/header.php';
?>

<div class="max-w-4xl mx-auto space-y-6">
    <a href="dashboard.php#daftar" class="inline-flex items-center gap-2 rounded-full bg-white px-4 py-2 text-sm font-semibold text-stone-700 shadow-sm transition hover:bg-stone-100">
        <i class="fa-solid fa-arrow-left"></i> Kembali ke daftar laporan
    </a>
    <div class="glass-panel p-8 rounded-3xl shadow-xl">
        <div class="flex justify-between items-start border-b border-stone-200 pb-4 mb-6">
            <div>
                <span class="text-xs font-semibold text-orange-700 tracking-wide uppercase">Detail Laporan #<?= $report['id'] ?></span>
                <h1 class="text-2xl font-bold text-stone-900"><?= htmlspecialchars($report['facility_name']) ?></h1>
                <p class="text-xs text-stone-500 mt-0.5">Pelapor: <?= htmlspecialchars($report['reporter_name']) ?> (<?= $report['email'] ?>)</p>
            </div>
            <span class="px-3 py-1 rounded-full font-bold text-xs bg-amber-100 text-amber-800"><?= $report['status'] ?></span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-xs mb-6">
            <div>
                <span class="text-stone-400 block mb-1">Lokasi Facility</span>
                <p class="font-semibold text-stone-800"><?= htmlspecialchars($report['location']) ?></p>
            </div>
            <div>
                <span class="text-stone-400 block mb-1">Kategori & Urgensi</span>
                <p class="font-semibold text-stone-800"><?= $report['category'] ?> — Prioritas <?= $report['priority'] ?></p>
            </div>
            <div class="col-span-full">
                <span class="text-stone-400 block mb-1">Deskripsi Kerusakan</span>
                <p class="text-stone-700 bg-white/60 p-4 rounded-2xl border border-stone-200/50 leading-relaxed"><?= nl2br(htmlspecialchars($report['description'])) ?></p>
            </div>
            <?php if ($report['image']): ?>
                <div class="col-span-full">
                    <span class="text-stone-400 block mb-2">Dokumentasi Foto (US-09)</span>
                    <img src="uploads/<?= $report['image'] ?>" class="max-h-64 rounded-2xl border border-stone-300 shadow-sm object-cover">
                </div>
            <?php endif; ?>
        </div>

        <!-- Timeline Riwayat Status (US-10) -->
        <div class="border-t border-stone-200 pt-6">
            <h3 class="text-sm font-bold text-stone-900 mb-4">Riwayat Perubahan Status (Status Logs)</h3>
            <div class="relative border-l-2 border-[#2fbf8f]/60 ml-4 space-y-6">
                <?php foreach ($logs as $log): ?>
                    <div class="relative pl-6">
                        <div class="absolute -left-[9px] top-1 w-4 h-4 rounded-full bg-[#2fbf8f] border-2 border-white"></div>
                        <div class="font-bold text-xs text-stone-900"><?= $log['status'] ?></div>
                        <div class="text-[11px] text-stone-400 mb-1"><?= date('d M Y, H:i:s', strtotime($log['created_at'])) ?></div>
                        <?php if ($log['notes']): ?>
                            <div class="text-xs text-stone-600 italic bg-stone-100/60 p-2 rounded-lg border border-stone-200/40">"<?= htmlspecialchars($log['notes']) ?>"</div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>