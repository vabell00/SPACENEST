<?php
require_once __DIR__ . '/config/database.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $facility_name = trim($_POST['facility_name'] ?? '');
    $category      = $_POST['category'] ?? '';
    $priority      = $_POST['priority'] ?? '';
    $location      = trim($_POST['location'] ?? '');
    $description   = trim($_POST['description'] ?? '');
    $image_name    = null;
    $categories    = ['Hardware/Lab', 'Jaringan/Internet', 'Mebel/Ruangan', 'Lainnya'];
    $priorities    = ['Rendah', 'Sedang', 'Tinggi'];
    $valid_input   = $facility_name !== '' && $location !== '' && $description !== ''
        && in_array($category, $categories, true) && in_array($priority, $priorities, true);

    if (!$valid_input) {
        $error = 'Lengkapi semua kolom dan pilih kategori serta prioritas yang valid.';
    }

    $upload = $_FILES['image'] ?? null;
    if ($valid_input && $upload && $upload['error'] !== UPLOAD_ERR_NO_FILE) {
        if ($upload['error'] !== UPLOAD_ERR_OK) {
            $error = 'Foto gagal diunggah. Pastikan ukuran file tidak melebihi batas server.';
        } else {
            $ext = strtolower(pathinfo($upload['name'], PATHINFO_EXTENSION));
            $upload_dir = __DIR__ . '/uploads';
            $valid_image = in_array($ext, ['jpg', 'jpeg', 'png', 'webp'], true)
                && $upload['size'] <= 5 * 1024 * 1024
                && getimagesize($upload['tmp_name']) !== false;

            if (!$valid_image) {
                $error = 'Foto harus JPG, PNG, atau WEBP yang valid dan maksimal 5 MB.';
            } else {
                if (!is_dir($upload_dir) && !mkdir($upload_dir, 0755, true) && !is_dir($upload_dir)) {
                    throw new RuntimeException('Folder upload tidak dapat dibuat.');
                }
                $image_name = bin2hex(random_bytes(16)) . '.' . $ext;
                if (!move_uploaded_file($upload['tmp_name'], $upload_dir . '/' . $image_name)) {
                    throw new RuntimeException('Foto laporan gagal disimpan.');
                }
            }
        }
    }

    if (!$error) {
        try {
            $pdo->beginTransaction();
            $stmt = $pdo->prepare("INSERT INTO reports (user_id, facility_name, category, priority, location, description, image, status) VALUES (?, ?, ?, ?, ?, ?, ?, 'Dikirim')");
            $stmt->execute([$_SESSION['user_id'], $facility_name, $category, $priority, $location, $description, $image_name]);

            $report_id = $pdo->lastInsertId();
            $logStmt = $pdo->prepare("INSERT INTO status_logs (report_id, status, notes) VALUES (?, 'Dikirim', 'Laporan berhasil dibuat oleh pelapor.')");
            $logStmt->execute([$report_id]);
            $pdo->commit();

            header("Location: dashboard.php?msg=success");
            exit;
        } catch (PDOException $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            if ($image_name !== null && is_file(__DIR__ . '/uploads/' . $image_name)) {
                unlink(__DIR__ . '/uploads/' . $image_name);
            }
            throw $e;
        }
    }
}

include __DIR__ . '/includes/header.php';
?>

<div class="max-w-2xl mx-auto">
    <div class="glass-panel p-8 rounded-3xl shadow-xl">
        <h2 class="text-xl font-bold text-stone-900 mb-1">Buat Laporan Fasilitas</h2>
        <p class="text-xs text-stone-500 mb-6">Laporkan kerusakan peralatan lab, komputer, atau fasilitas FTST lainnya.</p>

        <?php if ($error): ?>
            <div class="p-3 mb-4 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-700 text-xs font-medium">
                <?= $error ?>
            </div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data" class="space-y-4">
            <div>
                <label class="block text-xs font-semibold text-stone-700 mb-1">Nama Fasilitas / Alat *</label>
                <input type="text" name="facility_name" required placeholder="Contoh: PC Lab Elektro 04 / AC Workshop Mesin" 
                       class="w-full px-4 py-2.5 rounded-xl bg-white/70 border border-stone-300 focus:ring-2 focus:ring-emerald-500 text-xs">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-stone-700 mb-1">Kategori (US-08)</label>
                    <select name="category" class="w-full px-4 py-2.5 rounded-xl bg-white/70 border border-stone-300 text-xs">
                        <option value="Hardware/Lab">Hardware / Alat Lab</option>
                        <option value="Jaringan/Internet">Jaringan / Wi-Fi</option>
                        <option value="Mebel/Ruangan">Mebel / Ruangan</option>
                        <option value="Lainnya">Lainnya</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-stone-700 mb-1">Urgensi / Prioritas (US-08)</label>
                    <select name="priority" class="w-full px-4 py-2.5 rounded-xl bg-white/70 border border-stone-300 text-xs">
                        <option value="Rendah">Rendah</option>
                        <option value="Sedang" selected>Sedang</option>
                        <option value="Tinggi">Tinggi (Mendesak)</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-stone-700 mb-1">Lokasi Detail *</label>
                <input type="text" name="location" required placeholder="Contoh: Gedung B, Lab 1 Elektro, Meja 03" 
                       class="w-full px-4 py-2.5 rounded-xl bg-white/70 border border-stone-300 text-xs">
            </div>

            <div>
                <label class="block text-xs font-semibold text-stone-700 mb-1">Deskripsi Kerusakan *</label>
                <textarea name="description" rows="4" required placeholder="Jelaskan detail masalah atau kronologi kerusakan..." 
                          class="w-full px-4 py-2.5 rounded-xl bg-white/70 border border-stone-300 text-xs"></textarea>
            </div>

            <div>
                <label class="block text-xs font-semibold text-stone-700 mb-1">Unggah Foto Kerusakan (US-09)</label>
                <input type="file" name="image" accept="image/*" class="w-full text-xs text-stone-600 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-amber-100 file:text-amber-700 hover:file:bg-amber-200">
            </div>

            <div class="pt-4 flex justify-end space-x-3">
                <a href="index.php" class="px-5 py-2.5 rounded-xl bg-white text-stone-700 text-xs font-semibold hover:bg-stone-100 transition">Batal</a>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-[#1c1917] hover:bg-black text-white font-semibold text-xs transition shadow-md">Kirim Laporan</button>
            </div>
        </form>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>