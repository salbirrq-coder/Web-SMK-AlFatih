<?php
$activeMenu = 'berita';
$pageTitle = 'Kelola Berita';
require_once __DIR__ . '/includes/admin_header.php';
$pdo = db();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $id = (int)($_POST['id'] ?? 0);
    if ($action === 'add' || $action === 'edit') {
        $judul = trim($_POST['judul'] ?? '');
        $kategori = trim($_POST['kategori'] ?? 'Umum');
        $deskripsi = trim($_POST['deskripsi'] ?? '');
        $tanggal = $_POST['tanggal'] ?? date('Y-m-d');
        $gambar = null;
        $upload = handle_upload('gambar', 'berita', false);
        if (isset($upload['error'])) { flash('error', $upload['error']); $gambar = 'skip'; }
        else $gambar = $upload['filename'];

        if ($judul !== '' && $gambar !== 'skip') {
            if ($action === 'add') $pdo->prepare("INSERT INTO berita (judul, kategori, deskripsi, gambar, tanggal) VALUES (?,?,?,?,?)")->execute([$judul, $kategori, $deskripsi, $gambar, $tanggal]);
            else {
                if ($gambar) $pdo->prepare("UPDATE berita SET judul=?, kategori=?, deskripsi=?, gambar=?, tanggal=? WHERE id=?")->execute([$judul, $kategori, $deskripsi, $gambar, $tanggal, $id]);
                else $pdo->prepare("UPDATE berita SET judul=?, kategori=?, deskripsi=?, tanggal=? WHERE id=?")->execute([$judul, $kategori, $deskripsi, $tanggal, $id]);
            }
            flash('success', 'Berita disimpan.');
        } else if ($gambar !== 'skip') flash('error', 'Judul berita wajib diisi.');
    } elseif ($action === 'delete' && $id) {
        $pdo->prepare("DELETE FROM berita WHERE id=?")->execute([$id]);
        flash('success', 'Berita dihapus.');
    }
    header('Location: ' . SITE_URL . 'admin/berita.php');
    exit;
}

$items = $pdo->query("SELECT * FROM berita ORDER BY tanggal DESC, id DESC")->fetchAll();
?>

<div class="page-heading"><div><h1>Kelola Berita</h1></div></div>

<div class="admin-card">
    <div class="card-head"><h3><i class="fas fa-newspaper" style="color:var(--forest-500);margin-right:8px;"></i>Tambah Berita</h3></div>
    <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="action" value="add">
        <div class="form-grid">
            <div class="form-group"><label class="form-label">Judul <span class="req">*</span></label><input class="form-control" name="judul" required></div>
            <div class="form-group"><label class="form-label">Kategori</label><input class="form-control" name="kategori" value="Umum"></div>
            <div class="form-group"><label class="form-label">Tanggal</label><input class="form-control" type="date" name="tanggal" value="<?php echo date('Y-m-d'); ?>"></div>
            <div class="form-group"><label class="form-label">Gambar</label><input class="form-control" type="file" name="gambar" accept=".jpg,.jpeg,.png,.webp"></div>
            <div class="form-group full"><label class="form-label">Deskripsi</label><textarea class="form-control" name="deskripsi" rows="3"></textarea></div>
        </div>
        <button type="submit" class="btn btn-green"><i class="fas fa-plus"></i> Tambah</button>
    </form>
</div>

<div class="table-card">
    <div class="table-header"><h3 style="font-size:16px;color:var(--forest-900);">Daftar Berita</h3></div>
    <?php if (empty($items)): ?><div class="empty-state"><h3>Belum Ada Berita</h3></div>
    <?php else: ?>
    <div class="table-wrap">
        <table class="data-table">
            <thead><tr><th>Judul</th><th>Kategori</th><th>Tanggal</th><th>Aksi</th></tr></thead>
            <tbody>
            <?php foreach ($items as $it): ?>
                <tr>
                    <td class="nama-cell"><?php echo e($it['judul']); ?></td>
                    <td><span class="status-badge status-blue"><?php echo e($it['kategori']); ?></span></td>
                    <td><?php echo format_date($it['tanggal']); ?></td>
                    <td>
                        <form method="POST" onsubmit="return confirm('Hapus berita ini?');">
                            <input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?php echo $it['id']; ?>">
                            <button type="submit" class="action-btn action-red"><i class="fas fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
