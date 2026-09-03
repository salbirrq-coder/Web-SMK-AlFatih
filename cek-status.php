<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

ensure_database();
$pdo = db();
$result = null;
$noResult = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nomor = trim($_POST['nomor'] ?? '');
    if ($nomor === '') {
        $noResult = true;
    } else {
        $stmt = $pdo->prepare("SELECT * FROM pendaftar WHERE nomor_pendaftaran = ?");
        $stmt->execute([$nomor]);
        $result = $stmt->fetch() ?: null;
        if (!$result) $noResult = true;
    }
}

$pageTitle = 'Cek Status Pendaftaran';
require_once __DIR__ . '/includes/header.php';
?>

<section class="page-hero">
    <div class="page-hero-shape" style="top:20%;right:15%;width:50px;height:50px;border-radius:50%;background:rgba(212,175,55,0.12);animation:floatGentle 8s ease-in-out infinite;"></div>
    <div class="container">
        <div class="hero-badge"><span class="dot"></span> Info Pendaftaran</div>
        <h1>Cek <span class="gold-text">Status</span> Pendaftaran</h1>
        <p>Masukkan nomor pendaftaran Anda untuk melihat status dan perkembangan proses seleksi.</p>
    </div>
</section>

<div class="container" style="padding-top:40px;padding-bottom:80px;max-width:760px;">
    <!-- Search form -->
    <div class="form-card reveal-scale" style="margin-bottom:30px;">
        <form action="" method="POST">
            <label class="form-label">Nomor Pendaftaran</label>
            <div style="display:flex;gap:12px;align-items:center;">
                <input class="form-control" type="text" name="nomor" placeholder="Contoh: PPDB-2026-00125" value="<?php echo e($_POST['nomor'] ?? ''); ?>" style="flex:1;">
                <button type="submit" class="btn btn-green"><i class="fas fa-search"></i> CEK STATUS</button>
            </div>
        </form>
    </div>

    <?php if ($noResult): ?>
        <div class="alert alert-error"><i class="fas fa-search-minus"></i> <div>Nomor pendaftaran tidak ditemukan. Pastikan nomor yang Anda masukkan benar.</div></div>
    <?php elseif ($result): ?>
        <!-- Status result -->
        <?php if ($result['status'] === 'DITERIMA'): ?>
            <div class="congrats-card">
                <h3>Selamat! &#127881;</h3>
                <p>Selamat, Anda dinyatakan <strong>DITERIMA</strong> sebagai peserta didik baru SMK Tahfizh Al-Fatih.</p>
                <div class="lulus-info">
                    <div class="row"><div class="l-f"><i class="fas fa-id-card"></i> Nomor Pendaftaran</div><div class="l-v"><?php echo e($result['nomor_pendaftaran']); ?></div></div>
                    <div class="row"><div class="l-f"><i class="fas fa-user"></i> Nama Lengkap</div><div class="l-v"><?php echo e($result['nama_lengkap']); ?></div></div>
                    <div class="row"><div class="l-f"><i class="fas fa-school"></i> Program Keahlian</div>
                        <div class="l-v"><?php echo e($pdo->query("SELECT nama FROM program_keahlian WHERE id = " . (int)$result['program_keahlian_id'])->fetchColumn()); ?></div>
                    </div>
                    <div class="row"><div class="l-f"><i class="fas fa-info-circle"></i> Status</div><div class="l-v"><span class="status-badge status-green">DITERIMA</span></div></div>
                </div>
                <p style="font-size:14px;">Silakan lakukan daftar ulang sesuai jadwal yang akan diumumkan melalui pengumuman.</p>
            </div>
        <?php else: ?>
            <div class="result-card">
                <div class="result-icon"><i class="fas fa-file-invoice"></i></div>
                <h2 style="font-size:22px;color:var(--forest-900);margin-bottom:18px;">Status Pendaftaran Anda</h2>
                <div class="status-result <?php echo status_class($result['status']); ?>">
                    <div class="status-header">
                        <div class="icon"><?php echo status_icon($result['status']); ?></div>
                        <div class="status-title"><?php echo status_label($result['status']); ?></div>
                    </div>
                    <p class="status-desc"><?php echo status_desc($result['status']); ?></p>
                    <?php if (!empty($result['catatan'])): ?>
                        <p class="status-desc" style="margin-top:12px;padding-top:12px;border-top:1px dashed #ddd;"><strong>Catatan:</strong> <?php echo e($result['catatan']); ?></p>
                    <?php endif; ?>
                </div>
                <div class="nomor-card" style="margin-top:24px;">
                    <div class="label">Nomor Pendaftaran</div>
                    <div class="value"><?php echo e($result['nomor_pendaftaran']); ?></div>
                </div>
            </div>
            <div class="alert alert-info" style="margin-top:20px;"><i class="fas fa-info-circle"></i> <div>Pendaftaran Anda saat ini sedang diproses oleh pihak sekolah. Pantau terus status ini secara berkala.</div></div>
        <?php endif; ?>
    <?php endif; ?>

    <a href="<?php echo SITE_URL; ?>" class="btn btn-outline-green" style="width:100%;"><i class="fas fa-arrow-left"></i> KEMBALI KE BERANDA</a>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
