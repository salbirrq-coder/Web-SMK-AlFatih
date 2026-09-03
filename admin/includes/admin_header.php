<?php
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/auth.php';
require_login();
$admin = current_admin();

if (!isset($activeMenu)) $activeMenu = '';

// Counts for sidebar badges
$pdo = db();
$menungguCount = (int)$pdo->query("SELECT COUNT(*) FROM pendaftar WHERE status = 'MENUNGGU'")->fetchColumn();
$verifikasiCount = (int)$pdo->query("SELECT COUNT(*) FROM pendaftar WHERE status = 'DIVERIFIKASI'")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle . ' | ' : ''; ?>Admin <?php echo SCHOOL_NAME; ?></title>
    <link rel="icon" href="<?php echo SITE_URL; ?>assets/img/logo.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>assets/css/style.css">
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>assets/css/pages.css">
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>admin/assets/admin.css">
</head>
<body>
<div id="toast-container"></div>

<?php foreach (get_flash() as $f): ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    showToast(<?php echo json_encode($f['type'], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>, <?php echo json_encode($f['message'], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>);
});
</script>
<?php endforeach; ?>

<div class="admin-layout">
    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <div class="sb-brand">
            <img src="<?php echo SITE_URL; ?>assets/img/logo.png" alt="Logo">
            <div>
                <div class="t"><?php echo SCHOOL_NAME; ?></div>
                <div class="s">Admin Panel</div>
            </div>
        </div>
        <div class="sb-user">
            <div class="avatar"><?php echo strtoupper(mb_substr($admin['name'] ?? 'A', 0, 1)); ?></div>
            <div>
                <div class="name"><?php echo e($admin['name'] ?? 'Administrator'); ?></div>
                <div class="role"><?php echo e($admin['username'] ?? 'admin'); ?></div>
            </div>
        </div>

        <nav class="sb-menu">
            <div class="sb-menu-group">
                <div class="sb-menu-tag">Menu Utama</div>
                <a href="<?php echo SITE_URL; ?>admin/dashboard.php" class="<?php echo $activeMenu === 'dashboard' ? 'active' : ''; ?>"><i class="fas fa-gauge-high"></i> Dashboard</a>
                <a href="<?php echo SITE_URL; ?>admin/pendaftar.php" class="<?php echo $activeMenu === 'pendaftar' ? 'active' : ''; ?>"><i class="fas fa-users"></i> Data Pendaftar
                    <?php if ($menungguCount > 0): ?><span class="badge"><?php echo $menungguCount; ?></span><?php endif; ?>
                </a>
            </div>
            <div class="sb-menu-group">
                <div class="sb-menu-tag">Konten</div>
                <a href="<?php echo SITE_URL; ?>admin/pengumuman.php" class="<?php echo $activeMenu === 'pengumuman' ? 'active' : ''; ?>"><i class="fas fa-bullhorn"></i> Pengumuman</a>
                <a href="<?php echo SITE_URL; ?>admin/program.php" class="<?php echo $activeMenu === 'program' ? 'active' : ''; ?>"><i class="fas fa-school"></i> Program Keahlian</a>
                <a href="<?php echo SITE_URL; ?>admin/berita.php" class="<?php echo $activeMenu === 'berita' ? 'active' : ''; ?>"><i class="fas fa-newspaper"></i> Berita</a>
                <a href="<?php echo SITE_URL; ?>admin/fasilitas.php" class="<?php echo $activeMenu === 'fasilitas' ? 'active' : ''; ?>"><i class="fas fa-building"></i> Fasilitas</a>
            </div>
            <div class="sb-menu-group">
                <div class="sb-menu-tag">Akun</div>
                <a href="<?php echo SITE_URL; ?>admin/pengaturan.php" class="<?php echo $activeMenu === 'pengaturan' ? 'active' : ''; ?>"><i class="fas fa-user-cog"></i> Pengaturan</a>
                <a href="<?php echo SITE_URL; ?>admin/logout.php" onclick="return confirm('Yakin ingin keluar?');"><i class="fas fa-right-from-bracket"></i> Keluar</a>
            </div>
        </nav>
    </aside>

    <!-- Sidebar overlay (mobile) -->
    <div class="mobile-overlay" id="sbOverlay" style="z-index:45;"></div>

    <!-- Main -->
    <main class="admin-main">
        <div class="admin-topbar">
            <div style="display:flex;align-items:center;gap:14px;">
                <button class="sb-toggle" id="sbToggle" aria-label="Menu"><i class="fas fa-bars"></i></button>
                <div class="page-title"><?php echo e($pageTitle ?? 'Dashboard'); ?></div>
            </div>
            <div class="top-actions">
                <a href="<?php echo SITE_URL; ?>" target="_blank" class="btn btn-outline-green btn-sm" style="font-size:12px;"><i class="fas fa-external-link-alt"></i> Lihat Situs</a>
            </div>
        </div>
        <div class="admin-content">
