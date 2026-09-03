<?php
$activeMenu = 'pengaturan';
$pageTitle = 'Pengaturan';
require_once __DIR__ . '/includes/admin_header.php';
$pdo = db();

$msg = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $current = $_POST['current'] ?? '';
    $new = $_POST['newpass'] ?? '';
    $confirm = $_POST['confirm'] ?? '';

    $stmt = $pdo->prepare("SELECT * FROM admin WHERE id = ?");
    $stmt->execute([$admin['id']]);
    $row = $stmt->fetch();

    if (!password_verify($current, $row['password'])) {
        flash('error', 'Password saat ini salah.');
    } elseif (strlen($new) < 6) {
        flash('error', 'Password baru minimal 6 karakter.');
    } elseif ($new !== $confirm) {
        flash('error', 'Konfirmasi password tidak cocok.');
    } else {
        $hash = password_hash($new, PASSWORD_BCRYPT);
        $pdo->prepare("UPDATE admin SET password=? WHERE id=?")->execute([$hash, $admin['id']]);
        flash('success', 'Password berhasil diubah.');
    }
    header('Location: ' . SITE_URL . 'admin/pengaturan.php');
    exit;
}
?>

<div class="page-heading"><div><h1>Pengaturan Akun</h1></div></div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:22px;">
    <div class="admin-card">
        <div class="card-head"><h3><i class="fas fa-user" style="color:var(--forest-500);margin-right:8px;"></i>Profil Admin</h3></div>
        <div style="display:flex;gap:16px;align-items:center;">
            <div class="avatar" style="width:72px;height:72px;border-radius:20px;background:linear-gradient(135deg,var(--gold-500),var(--gold-700));color:var(--forest-950);display:flex;align-items:center;justify-content:center;font-size:30px;font-weight:800;"><?php echo strtoupper(mb_substr($admin['name'] ?? 'A', 0, 1)); ?></div>
            <div>
                <div style="font-size:18px;font-weight:800;color:var(--forest-900);"><?php echo e($admin['name']); ?></div>
                <div style="color:var(--text-gray);font-size:14px;">@<?php echo e($admin['username']); ?></div>
                <div style="color:var(--text-gray);font-size:14px;"><?php echo e($admin['email']); ?></div>
            </div>
        </div>
    </div>

    <div class="admin-card">
        <div class="card-head"><h3><i class="fas fa-key" style="color:var(--forest-500);margin-right:8px;"></i>Ubah Password</h3></div>
        <form method="POST">
            <div class="form-group" style="margin-bottom:16px;"><label class="form-label">Password Saat Ini</label><input class="form-control" type="password" name="current" required></div>
            <div class="form-group" style="margin-bottom:16px;"><label class="form-label">Password Baru</label><input class="form-control" type="password" name="newpass" required></div>
            <div class="form-group" style="margin-bottom:22px;"><label class="form-label">Konfirmasi Password Baru</label><input class="form-control" type="password" name="confirm" required></div>
            <button type="submit" class="btn btn-green"><i class="fas fa-save"></i> Simpan Password</button>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
