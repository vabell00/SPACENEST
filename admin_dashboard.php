<?php
require_once 'config/database.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

// Update Status Handler (US-06, US-07)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $report_id  = $_POST['report_id'];
    $new_status = $_POST['status'];
    $notes      = trim($_POST['notes']);

    // Update status laporan
    $stmt = $pdo->prepare("UPDATE reports SET status = ? WHERE id = ?");
    $stmt->execute([$new_status, $report_id]);

    // Tambahkan riwayat status log (US-10)
    $logStmt = $pdo->prepare("INSERT INTO status_logs (report_id, status, notes) VALUES (?, ?, ?)");
    $logStmt->execute([$report_id, $new_status, $notes]);

    header("Location: admin_dashboard.php?updated=1");
    exit;
}

// Query Laporan
$reports = $pdo->query("SELECT r.*, u.name as reporter_name FROM reports r JOIN users u ON r.user_id = u.id ORDER BY r.created_at DESC")->fetchAll();

include 'includes/header.php';
?>

<div class="space-y-6">
    <div class="glass-panel p-6 rounded-3xl flex justify-between items-center">
        <div>
            <h1 class="text-xl font-bold text-stone-900">Panel Manajemen Admin FTST</h1>
            <p class="text-xs text-stone-500">Verifikasi dan perbarui status laporan fasilitas kampus.</p>
        </div>
    </div>

    <div class="glass-panel p-6 rounded-3xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-stone-300/60 text-stone-500 font-semibold uppercase">
                        <th class="pb-3 px-2">ID & Pelapor</th>
                        <th class="pb-3 px-2">Fasilitas & Lokasi</th>
                        <th class="pb-3 px-2">Status Saat Ini</th>
                        <th class="pb-3 px-2">Perbarui Status (US-07)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-200/50">
                    <?php foreach ($reports as $r): ?>
                        <tr class="hover:bg-white/40 transition">
                            <td class="py-3.5 px-2">
                                <div class="font-bold text-stone-900">#<?= $r['id'] ?> - <?= htmlspecialchars($r['reporter_name']) ?></div>
                                <div class="text-[11px] text-stone-400"><?= date('d M Y', strtotime($r['created_at'])) ?></div>
                            </td>
                            <td class="py-3.5 px-2">
                                <div class="font-semibold text-stone-800"><?= htmlspecialchars($r['facility_name']) ?></div>
                                <div class="text-[11px] text-stone-500"><?= htmlspecialchars($r['location']) ?></div>
                            </td>
                            <td class="py-3.5 px-2">
                                <span class="px-2.5 py-1 rounded-full font-bold text-[10px] bg-amber-100 text-amber-800"><?= $r['status'] ?></span>
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

<?php include 'includes/footer.php'; ?>