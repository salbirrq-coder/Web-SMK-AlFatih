<?php
$activeMenu = 'program';
$pageTitle = 'Kelola Program Keahlian';
require_once __DIR__ . '/includes/admin_header.php';
$pdo = db();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    sec_verify_csrf();
    $action = $_POST['action'] ?? '';
    $id = (int)($_POST['id'] ?? 0);
    if ($action === 'add' || $action === 'edit') {
        $nama = trim($_POST['nama'] ?? '');
        $deskripsi = trim($_POST['deskripsi'] ?? '');
        $icon = $_POST['icon'] ?? '🎓';
        if ($nama !== '') {
            if ($action === 'add') $pdo->prepare("INSERT INTO program_keahlian (nama, deskripsi, icon) VALUES (?,?,?)")->execute([$nama, $deskripsi, $icon]);
            else $pdo->prepare("UPDATE program_keahlian SET nama=?, deskripsi=?, icon=? WHERE id=?")->execute([$nama, $deskripsi, $icon, $id]);
            flash('success', 'Program keahlian disimpan.');
        } else flash('error', 'Nama program wajib diisi.');
    } elseif ($action === 'delete' && $id) {
        $pdo->prepare("DELETE FROM program_keahlian WHERE id=?")->execute([$id]);
        flash('success', 'Program keahlian dihapus.');
    }
    header('Location: ' . SITE_URL . 'admin/program.php');
    exit;
}

$items = $pdo->query("SELECT * FROM program_keahlian ORDER BY id")->fetchAll();
?>

<div class="page-heading"><div><h1>Kelola Program Keahlian</h1></div></div>

<div class="admin-card">
    <div class="card-head"><h3><i class="fas fa-school" style="color:var(--forest-500);margin-right:8px;"></i>Tambah Program</h3></div>
    <form method="POST">
        <input type="hidden" name="action" value="add">
        <?php echo sec_csrf_field(); ?>
        <div class="form-grid">
            <div class="form-group full"><label class="form-label">Nama Program <span class="req">*</span></label><input class="form-control" name="nama" required></div>
            <div class="form-group full"><label class="form-label">Deskripsi</label><textarea class="form-control" name="deskripsi" rows="3"></textarea></div>
        </div>
        <button type="submit" class="btn btn-green"><i class="fas fa-plus"></i> Tambah</button>
    </form>
</div>

<div class="table-card">
    <div class="table-header"><h3 style="font-size:16px;color:var(--forest-900);">Daftar Program</h3></div>
    <?php if (empty($items)): ?><div class="empty-state"><h3>Belum Ada Program</h3></div>
    <?php else: ?>
    <div class="table-wrap">
        <table class="data-table">
            <thead><tr><th>Ikon</th><th>Nama</th><th>Deskripsi</th><th>Aksi</th></tr></thead>
            <tbody>
            <?php foreach ($items as $it): ?>
                <tr>
                    <td style="font-size:24px;"><?php echo e($it['icon']); ?></td>
                    <td class="nama-cell"><?php echo e($it['nama']); ?></td>
                    <td style="max-width:300px;"><?php echo e($it['deskripsi']); ?></td>
                    <td>
                        <div style="display:flex;gap:6px;">
                            <button class="action-btn action-green" onclick="editProgram(<?php echo $it['id']; ?>)"><i class="fas fa-pen"></i></button>
                            <form method="POST" onsubmit="return confirm('Hapus program ini?');">
                                <?php echo sec_csrf_field(); ?>
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
var progData = <?php echo json_encode($items, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;
function editProgram(id) {
    var it = progData.find(function(x){ return x.id == id; });
    if (!it) return;
    var f = document.getElementById('editForm');
    if (!f) {
        f = document.createElement('form'); f.method='POST'; f.id='editForm'; f.style.display='none'; document.body.appendChild(f);
        f.innerHTML = '<input type="hidden" name="csrf_token" value="<?php echo e(sec_csrf_token()); ?>"><input type="hidden" name="action" value="edit"><input type="hidden" name="id" id="pfId"><input type="text" name="nama" id="pfNama"><textarea name="deskripsi" id="pfDesk"></textarea><button id="pfSubmit" type="submit"></button>';
    }
    document.getElementById('pfId').value=it.id; document.getElementById('pfNama').value=it.nama; document.getElementById('pfDesk').value=it.deskripsi; document.getElementById('pfSubmit').click();
}
</script>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
