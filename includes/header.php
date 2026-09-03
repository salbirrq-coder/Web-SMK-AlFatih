<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/functions.php';
ensure_database();
$navItems = [
    ['label' => 'Beranda', 'href' => SITE_URL],
    ['label' => 'Tentang', 'href' => SITE_URL . 'index.php#tentang'],
    ['label' => 'Program Keahlian', 'href' => SITE_URL . 'index.php#program'],
    ['label' => 'Fasilitas', 'href' => SITE_URL . 'index.php#fasilitas'],
    ['label' => 'Berita', 'href' => SITE_URL . 'index.php#berita'],
    ['label' => 'Pendaftaran', 'href' => SITE_URL . 'pendaftaran.php'],
    ['label' => 'Cek Status', 'href' => SITE_URL . 'cek-status.php'],
    ['label' => 'Pengumuman', 'href' => SITE_URL . 'pengumuman.php'],
    ['label' => 'Kontak', 'href' => SITE_URL . 'kontak.php'],
];
$currentPage = basename($_SERVER['SCRIPT_NAME'] ?? '');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle . ' | ' : ''; ?><?php echo SCHOOL_NAME; ?></title>
    <meta name="description" content="<?php echo SCHOOL_TAGLINE; ?>">
    <link rel="icon" href="<?php echo SITE_URL; ?>assets/img/logo.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Amiri:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>assets/css/style.css">
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>assets/css/pages.css">
</head>
<body>
<?php
// Flash messages
$flashes = get_flash();
?>
<div id="toast-container"></div>

<?php if (!empty($flashes)): ?>
<script>
    const flashes = <?php echo json_encode($flashes); ?>;
    document.addEventListener('DOMContentLoaded', function() {
        flashes.forEach(function(f) {
            showToast(f.type, f.message);
        });
    });
</script>
<?php endif; ?>

<!-- Navbar -->
<header class="navbar" id="navbar">
    <div class="navbar-inner">
        <a href="<?php echo SITE_URL; ?>" class="brand">
            <img src="<?php echo SITE_URL; ?>assets/img/logo.png" alt="Logo" class="brand-logo">
            <div class="brand-text">
                <span class="brand-name">SMK TAHFIZH</span>
                <span class="brand-sub">AL-FATIH</span>
            </div>
        </a>

        <nav class="nav-menu" id="navMenu">
            <?php foreach ($navItems as $item): ?>
                <a href="<?php echo e($item['href']); ?>" class="nav-link"><?php echo e($item['label']); ?></a>
            <?php endforeach; ?>
            <a href="<?php echo SITE_URL; ?>pendaftaran.php" class="btn btn-gold btn-sm nav-cta">
                <i class="fas fa-user-graduate"></i> DAFTAR SEKARANG
            </a>
        </nav>

        <button class="hamburger" id="hamburger" aria-label="Menu">
            <span></span><span></span><span></span>
        </button>
    </div>
</header>

<div class="mobile-overlay" id="mobileOverlay"></div>
