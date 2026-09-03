<?php
$activeMenu = 'pengumuman';
$pageTitle = 'Kelola Pengumuman';
require_once __DIR__ . '/includes/admin_header.php';
$pdo = db();

// CRUD
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    sec_verify_csrf();
    $action = $_POST['action'] ?? '';
    $id = (int)($_POST['id'] ?? 0);

    if ($action === 'add' || $action === 'edit') {
        $judul = trim($_POST['judul'] ?? '');
        $isi = trim($_POST['isi'] ?? '');
        $tanggal = $_POST['tanggal'] ?? date('Y-m-d');
        if ($judul !== '' && $isi !== '') {
            if ($action === 'add') {
                $pdo->prepare("INSERT INTO pengumuman (judul, isi, tanggal) VALUES (?,?,?)")->execute([$judul, $isi, $tanggal]);
                flash('success', 'Pengumuman berhasil ditambahkan.');
            } else {
                $pdo->prepare("UPDATE pengumuman SET judul=?, isi=?, tanggal=? WHERE id=?")->execute([$judul, $isi, $tanggal, $id]);
                flash('success', 'Pengumuman berhasil diperbarui.');
            }
        } else {
            flash('error', 'Judul dan isi pengumuman wajib diisi.');
        }
    } elseif ($action === 'delete' && $id) {
        $pdo->prepare("DELETE FROM pengumuman WHERE id=?")->execute([$id]);
        flash('success', 'Pengumuman dihapus.');
    }
    header('Location: ' . SITE_URL . 'admin/pengumuman.php');
    exit;
}

$items = $pdo->query("SELECT * FROM pengumuman ORDER BY tanggal DESC, id DESC")->fetchAll();
?>

<div class="page-heading">
    <div><h1>Kelola Pengumuman</h1></div>
</div>

<div class="admin-card">
    <div class="card-head"><h3><i class="fas fa-bullhorn" style="color:var(--forest-500);margin-right:8px;"></i>Tambah Pengumuman</h3></div>
    <form method="POST">
        <input type="hidden" name="action" value="add">
        <?php echo sec_csrf_field(); ?>
        <div class="form-grid">
            <div class="form-group full"><label class="form-label">Judul <span class="req">*</span></label><input class="form-control" type="text" name="judul" required></div>
            <div class="form-group full"><label class="form-label">Tanggal</label><input class="form-control" type="date" name="tanggal" value="<?php echo date('Y-m-d'); ?>"></div>
            <div class="form-group full"><label class="form-label">Isi Pengumuman <span class="req">*</span></label><textarea class="form-control" name="isi" rows="5" required></textarea></div>
        </div>
        <button type="submit" class="btn btn-green"><i class="fas fa-plus"></i> Tambah</button>
    </form>
</div>

<div class="table-card">
    <div class="table-header"><h3 style="font-size:16px;color:var(--forest-900);">Daftar Pengumuman</h3></div>
    <?php if (empty($items)): ?>
        <div class="empty-state"><i class="fas fa-bullhorn"></i><h3>Belum Ada Pengumuman</h3></div>
    <?php else: ?>
        <div class="table-wrap">
            <table class="data-table">
                <thead><tr><th>Judul</th><th>Tanggal</th><th>Aksi</th></tr></thead>
                <tbody>
                <?php foreach ($items as $it): ?>
                    <tr>
                        <td class="nama-cell"><?php echo e($it['judul']); ?></td>
                        <td><?php echo format_date($it['tanggal']); ?></td>
                        <td>
                            <div style="display:flex;gap:6px;">
                                <button class="action-btn action-green" onclick="editPengumuman(<?php echo $it['id']; ?>)"><i class="fas fa-pen"></i></button>
                                <form method="POST" onsubmit="return confirm('Hapus pengumuman ini?');">
                                    <?php echo sec_csrf_field(); ?>
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?php echo $it['id']; ?>">
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
var pengData = <?php echo json_encode($items, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;
function editPengumuman(id) {
    var it = pengData.find(function(x){ return x.id == id; });
    if (!it) return;
    var f = document.getElementById('editForm');
    if (!f) {
        f = document.createElement('form');
        f.method = 'POST';
        f.action = '';
        f.id = 'editForm';
        f.style.display = 'none';
        document.body.appendChild(f);
        f.innerHTML = '<input type="hidden" name="csrf_token" value="<?php echo e(sec_csrf_token()); ?>"><input type="hidden" name="action" value="edit">' +
            '<input type="hidden" name="id" id="efId">' +
            '<input type="text" name="judul" id="efJudul">' +
            '<input type="date" name="tanggal" id="efTanggal">' +
            '<textarea name="isi" id="efIsi"></textarea>' +
            '<button type="submit" id="efSubmit"></button>';
    }
    document.getElementById('efId').value = it.id;
    document.getElementById('efJudul').value = it.judul;
    document.getElementById('efTanggal').value = it.tanggal;
    document.getElementById('efIsi').value = it.isi;
    document.getElementById('efSubmit').click();
}
</script>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
