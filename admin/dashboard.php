<?php
$activeMenu = 'dashboard';
$pageTitle = 'Dashboard';
require_once __DIR__ . '/includes/admin_header.php';

$pdo = db();

// Stats
$totalPendaftar = (int)$pdo->query("SELECT COUNT(*) FROM pendaftar")->fetchColumn();
$totalMenunggu = (int)$pdo->query("SELECT COUNT(*) FROM pendaftar WHERE status = 'MENUNGGU'")->fetchColumn();
$totalVerifikasi = (int)$pdo->query("SELECT COUNT(*) FROM pendaftar WHERE status = 'DIVERIFIKASI'")->fetchColumn();
$totalDiterima = (int)$pdo->query("SELECT COUNT(*) FROM pendaftar WHERE status = 'DITERIMA'")->fetchColumn();
$totalDitolak = (int)$pdo->query("SELECT COUNT(*) FROM pendaftar WHERE status = 'TIDAK DITERIMA'")->fetchColumn();

// Pendaftar per program
$perProgram = $pdo->query("
    SELECT pk.nama, COUNT(p.id) AS jml
    FROM program_keahlian pk
    LEFT JOIN pendaftar p ON p.program_keahlian_id = pk.id
    GROUP BY pk.id, pk.nama
    ORDER BY jml DESC
")->fetchAll();
$maxProgram = $perProgram ? max(array_column($perProgram, 'jml')) : 1;
if ($maxProgram < 1) $maxProgram = 1;

// Recent pendaftar
$recent = $pdo->query("
    SELECT p.*, pk.nama AS program
    FROM pendaftar p
    LEFT JOIN program_keahlian pk ON p.program_keahlian_id = pk.id
    ORDER BY p.tanggal_daftar DESC
    LIMIT 8
")->fetchAll();
?>

<div class="page-heading">
    <div>
        <h1>Selamat Datang, <?php echo e($admin['name'] ?? 'Admin'); ?>!</h1>
        <div class="sub">Ringkasan data pendaftaran peserta didik baru.</div>
    </div>
    <a href="<?php echo SITE_URL; ?>admin/pendaftar.php" class="btn btn-green btn-sm"><i class="fas fa-users"></i> Kelola Pendaftar</a>
</div>

<!-- Stats -->
<div class="dash-stats">
    <div class="dash-stat">
        <div class="d-icon dash-icon-green"><i class="fas fa-users"></i></div>
        <div class="d-value"><?php echo $totalPendaftar; ?></div>
        <div class="d-label">Total Pendaftar</div>
    </div>
    <div class="dash-stat">
        <div class="d-icon dash-icon-yellow"><i class="fas fa-clock"></i></div>
        <div class="d-value"><?php echo $totalMenunggu; ?></div>
        <div class="d-label">Menunggu Verifikasi</div>
    </div>
    <div class="dash-stat">
        <div class="d-icon dash-icon-blue"><i class="fas fa-search"></i></div>
        <div class="d-value"><?php echo $totalVerifikasi; ?></div>
        <div class="d-label">Diverifikasi</div>
    </div>
    <div class="dash-stat">
        <div class="d-icon dash-icon-emerald"><i class="fas fa-check-circle"></i></div>
        <div class="d-value"><?php echo $totalDiterima; ?></div>
        <div class="d-label">Diterima</div>
    </div>
    <div class="dash-stat">
        <div class="d-icon dash-icon-red"><i class="fas fa-times-circle"></i></div>
        <div class="d-value"><?php echo $totalDitolak; ?></div>
        <div class="d-label">Ditolak</div>
    </div>
</div>

<div style="display:grid;grid-template-columns:1.3fr 1fr;gap:22px;margin-top:22px;">
    <!-- Recent pendaftar -->
    <div class="admin-card">
        <div class="card-head">
            <h3><i class="fas fa-user-clock" style="color:var(--forest-500);margin-right:8px;"></i>Pendaftar Terbaru</h3>
            <a href="<?php echo SITE_URL; ?>admin/pendaftar.php" style="font-size:13px;color:var(--forest-600);font-weight:600;">Lihat Semua &raquo;</a>
        </div>
        <?php if (empty($recent)): ?>
            <div class="empty-state" style="padding:30px 10px;"><i class="fas fa-inbox" style="font-size:40px;"></i><h3>Belum Ada Pendaftar</h3><p>Pendaftar baru akan muncul di sini.</p></div>
        <?php else: ?>
            <?php foreach ($recent as $r): ?>
                <div class="recent-item">
                    <div class="r-avatar"><?php echo strtoupper(mb_substr($r['nama_lengkap'], 0, 1)); ?></div>
                    <div style="flex:1;min-width:0;">
                        <div style="font-weight:600;font-size:14px;"><?php echo e($r['nama_lengkap']); ?></div>
                        <div style="font-size:12px;color:var(--text-gray);">
                            <?php echo e($r['nomor_pendaftaran']); ?> &middot; <?php echo e($r['program'] ?? '-'); ?>
                        </div>
                    </div>
                    <span class="status-badge <?php echo status_class($r['status']); ?>"><?php echo status_label($r['status']); ?></span>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- Pendaftar per program -->
    <div class="admin-card">
        <div class="card-head">
            <h3><i class="fas fa-chart-pie" style="color:var(--forest-500);margin-right:8px;"></i>Pendaftar per Program</h3>
        </div>
        <?php if (empty($perProgram)): ?>
            <div class="empty-state" style="padding:30px 10px;"><h3>Belum Ada Data</h3></div>
        <?php else: ?>
            <?php foreach ($perProgram as $pp): ?>
                <div class="bar-item">
                    <div class="bar-head">
                        <span class="b-label"><?php echo e($pp['nama']); ?></span>
                        <span class="b-val"><?php echo (int)$pp['jml']; ?> pendaftar</span>
                    </div>
                    <div class="bar-track">
                        <div class="bar-fill" style="width:<?php echo round(((int)$pp['jml'] / $maxProgram) * 100); ?>%;"></div>
                    </div>
                </div>
            <?php endforeach; ?>
            <div class="bar-item" style="margin-top:8px;">
                <div class="bar-head">
                    <span class="b-label" style="color:var(--forest-700);font-weight:600;"><i class="fas fa-check-circle"></i> Diterima</span>
                    <span class="b-val"><?php echo $totalDiterima; ?></span>
                </div>
                <div class="bar-track">
                    <div class="bar-fill gold" style="width:<?php echo $totalPendaftar ? round(($totalDiterima / $totalPendaftar) * 100) : 0; ?>%;"></div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
