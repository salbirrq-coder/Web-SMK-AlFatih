<?php
$activeMenu = 'fasilitas';
$pageTitle = 'Kelola Fasilitas';
require_once __DIR__ . '/includes/admin_header.php';
$pdo = db();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $id = (int)($_POST['id'] ?? 0);
    if ($action === 'add' || $action === 'edit') {
        $nama = trim($_POST['nama'] ?? '');
        $deskripsi = trim($_POST['deskripsi'] ?? '');
        $gambar = null;
        $upload = handle_upload('gambar', 'fasilitas', false);
        if (isset($upload['error'])) { flash('error', $upload['error']); $gambar = 'skip'; }
        else $gambar = $upload['filename'];

        if ($nama !== '' && $gambar !== 'skip') {
            if ($action === 'add') $pdo->prepare("INSERT INTO fasilitas (nama, deskripsi, gambar) VALUES (?,?,?)")->execute([$nama, $deskripsi, $gambar]);
            else {
                if ($gambar) $pdo->prepare("UPDATE fasilitas SET nama=?, deskripsi=?, gambar=? WHERE id=?")->execute([$nama, $deskripsi, $gambar, $id]);
                else $pdo->prepare("UPDATE fasilitas SET nama=?, deskripsi=? WHERE id=?")->execute([$nama, $deskripsi, $id]);
            }
            flash('success', 'Fasilitas disimpan.');
        } else if ($gambar !== 'skip') flash('error', 'Nama fasilitas wajib diisi.');
    } elseif ($action === 'delete' && $id) {
        $pdo->prepare("DELETE FROM fasilitas WHERE id=?")->execute([$id]);
        flash('success', 'Fasilitas dihapus.');
    }
    header('Location: ' . SITE_URL . 'admin/fasilitas.php');
    exit;
}

$items = $pdo->query("SELECT * FROM fasilitas ORDER BY id")->fetchAll();
?>

<div class="page-heading"><div><h1>Kelola Fasilitas</h1></div></div>

<div class="admin-card">
    <div class="card-head"><h3><i class="fas fa-building" style="color:var(--forest-500);margin-right:8px;"></i>Tambah Fasilitas</h3></div>
    <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="action" value="add">
        <div class="form-grid">
            <div class="form-group"><label class="form-label">Nama <span class="req">*</span></label><input class="form-control" name="nama" required></div>
            <div class="form-group"><label class="form-label">Gambar</label><input class="form-control" type="file" name="gambar" accept=".jpg,.jpeg,.png,.webp"></div>
            <div class="form-group full"><label class="form-label">Deskripsi</label><textarea class="form-control" name="deskripsi" rows="3"></textarea></div>
        </div>
        <button type="submit" class="btn btn-green"><i class="fas fa-plus"></i> Tambah</button>
    </form>
</div>

<div class="table-card">
    <div class="table-header"><h3 style="font-size:16px;color:var(--forest-900);">Daftar Fasilitas</h3></div>
    <?php if (empty($items)): ?><div class="empty-state"><h3>Belum Ada Fasilitas</h3></div>
    <?php else: ?>
    <div class="table-wrap">
        <table class="data-table">
            <thead><tr><th>Nama</th><th>Deskripsi</th><th>Aksi</th></tr></thead>
            <tbody>
            <?php foreach ($items as $it): ?>
                <tr>
                    <td class="nama-cell"><?php echo e($it['nama']); ?></td>
                    <td style="max-width:300px;"><?php echo e($it['deskripsi']); ?></td>
                    <td>
                        <div style="display:flex;gap:6px;">
                            <button class="action-btn action-green" onclick="editFasilitas(<?php echo $it['id']; ?>)"><i class="fas fa-pen"></i></button>
                            <form method="POST" onsubmit="return confirm('Hapus fasilitas ini?');">
                                <input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?php echo $it['id']; ?>">
                                <button type="submit" class="action-btn action-red"><i class="fas fa-trash"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>

<script>
var fasData = <?php echo json_encode($items); ?>;
function editFasilitas(id) {
    var it = fasData.find(function(x){ return x.id == id; });
    if (!it) return;
    var f = document.getElementById('editForm');
    if (!f) {
        f = document.createElement('form'); f.method='POST'; f.id='editForm'; f.style.display='none'; document.body.appendChild(f);
        f.innerHTML = '<input type="hidden" name="action" value="edit"><input type="hidden" name="id" id="ffId"><input type="text" name="nama" id="ffNama"><textarea name="deskripsi" id="ffDesk"></textarea><button id="ffSubmit" type="submit"></button>';
    }
    document.getElementById('ffId').value=it.id; document.getElementById('ffNama').value=it.nama; document.getElementById('ffDesk').value=it.deskripsi; document.getElementById('ffSubmit').click();
}
</script>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
