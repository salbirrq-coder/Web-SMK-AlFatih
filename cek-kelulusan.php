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

$pageTitle = 'Cek Kelulusan';
require_once __DIR__ . '/includes/header.php';
?>

<section class="page-hero">
    <div class="page-hero-shape" style="top:22%;left:12%;width:45px;height:45px;border-radius:12px;border:2px solid rgba(255,255,255,0.08);animation:spinSlow 18s linear infinite;"></div>
    <div class="container">
        <div class="hero-badge"><span class="dot"></span> Hasil Seleksi</div>
        <h1>Cek <span class="gold-text">Kelulusan</span> PPDB</h1>
        <p>Masukkan nomor pendaftaran Anda untuk melihat hasil kelulusan seleksi PPDB.</p>
    </div>
</section>

<div class="container" style="padding-top:40px;padding-bottom:80px;max-width:760px;">
    <div class="form-card reveal-scale" style="margin-bottom:30px;">
        <form action="" method="POST">
            <label class="form-label">Nomor Pendaftaran</label>
            <div style="display:flex;gap:12px;align-items:center;">
                <input class="form-control" type="text" name="nomor" placeholder="Contoh: PPDB-2026-00125" value="<?php echo e($_POST['nomor'] ?? ''); ?>" style="flex:1;">
                <button type="submit" class="btn btn-green"><i class="fas fa-check-circle"></i> CEK KELULUSAN</button>
            </div>
        </form>
    </div>

    <?php if ($noResult): ?>
        <div class="alert alert-error"><i class="fas fa-search-minus"></i> <div>Nomor pendaftaran tidak ditemukan. Pastikan nomor yang Anda masukkan benar.</div></div>
    <?php elseif ($result): ?>
        <?php if ($result['status'] === 'DITERIMA'): ?>
            <div class="congrats-card">
                <div style="font-size:64px;margin-bottom:10px;">&#127942;</div>
                <h3>SELAMAT!</h3>
                <p>Berdasarkan hasil seleksi PPDB, Anda dinyatakan <strong>LULUS / DITERIMA</strong> sebagai peserta didik baru di SMK Tahfizh Al-Fatih.</p>
                <div class="lulus-info">
                    <div class="row"><div class="l-f"><i class="fas fa-id-card"></i> Nomor Pendaftaran</div><div class="l-v"><?php echo e($result['nomor_pendaftaran']); ?></div></div>
                    <div class="row"><div class="l-f"><i class="fas fa-user"></i> Nama Lengkap</div><div class="l-v"><?php echo e($result['nama_lengkap']); ?></div></div>
                    <div class="row"><div class="l-f"><i class="fas fa-school"></i> Program Keahlian</div>
                        <div class="l-v"><?php echo e($pdo->query("SELECT nama FROM program_keahlian WHERE id = {$result['program_keahlian_id']}")->fetchColumn()); ?></div>
                    </div>
                </div>
                <p style="font-size:14px;">Selanjutnya silakan melakukan daftar ulang sesuai jadwal yang telah ditentukan. Hubungi pihak sekolah untuk informasi lebih lanjut.</p>
                <a href="https://wa.me/<?php echo SCHOOL_WHATSAPP; ?>" target="_blank" rel="noopener noreferrer" class="btn btn-gold"><i class="fab fa-whatsapp"></i> HUBUNGI VIA WHATSAPP</a>
            </div>
            <div class="alert alert-info" style="margin-top:20px;"><i class="fas fa-info-circle"></i> <div>Simpan bukti kelulusan ini sebagai dokumen penting Anda.</div></div>
        <?php else: ?>
            <div class="result-card">
                <div class="result-icon" style="background:#fef2f2;color:#ef4444;"><i class="fas fa-hourglass-half"></i></div>
                <h2 style="font-size:22px;color:var(--forest-900);margin-bottom:18px;">Hasil Kelulusan Belum Tersedia</h2>
                <div class="status-result <?php echo in_array($result['status'], ['DITERIMA','TIDAK DITERIMA']) ? status_class($result['status']) : 'status-yellow'; ?>">
                    <div class="status-header">
                        <div class="icon"><?php echo status_icon($result['status']); ?></div>
                        <div class="status-title"><?php echo status_label($result['status']); ?></div>
                    </div>
                    <p class="status-desc">Status pendaftaran Anda saat ini adalah <strong>'<?php echo e(status_label($result['status'])); ?>'</strong>. Hasil kelulusan baru akan diumumkan setelah proses seleksi selesai. Silakan cek kembali secara berkala.</p>
                </div>
                <div class="nomor-card" style="margin-top:24px;">
                    <div class="label">Nomor Pendaftaran Anda</div>
                    <div class="value"><?php echo e($result['nomor_pendaftaran']); ?></div>
                </div>
            </div>
        <?php endif; ?>
    <?php endif; ?>

    <a href="<?php echo SITE_URL; ?>" class="btn btn-outline-green" style="width:100%;"><i class="fas fa-arrow-left"></i> KEMBALI KE BERANDA</a>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
