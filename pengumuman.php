<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

ensure_database();
$pdo = db();
$announcements = $pdo->query("SELECT * FROM pengumuman ORDER BY tanggal DESC, id DESC")->fetchAll();

$pageTitle = 'Pengumuman';
require_once __DIR__ . '/includes/header.php';
?>

<section class="page-hero">
    <div class="page-hero-shape" style="top:15%;right:12%;width:50px;height:50px;border-radius:50%;background:rgba(212,175,55,0.15);animation:floatGentle 7s ease-in-out infinite;"></div>
    <div class="container">
        <div class="hero-badge"><span class="dot"></span> Informasi Resmi</div>
        <h1>Pengumuman <span class="gold-text">Sekolah</span></h1>
        <p>Informasi dan pengumuman resmi terbaru dari SMK Tahfizh Al-Fatih.</p>
    </div>
</section>

<section class="section" style="padding-top:60px;">
    <div class="container" style="max-width:900px;">
        <?php if (empty($announcements)): ?>
            <div class="empty-state reveal-scale"><i class="fas fa-bullhorn"></i><h3>Belum Ada Pengumuman</h3><p>Segera kembali untuk melihat informasi terbaru.</p></div>
        <?php else: ?>
            <div class="announcement-list stagger">
                <?php foreach ($announcements as $a): ?>
                    <article class="announcement-card reveal">
                        <div class="date-badge">
                            <div class="d-day"><?php echo date('d', strtotime($a['tanggal'])); ?></div>
                            <div class="d-month"><?php echo strtoupper(date('M', strtotime($a['tanggal']))); ?></div>
                            <div class="d-year"><?php echo date('Y', strtotime($a['tanggal'])); ?></div>
                        </div>
                        <div class="announcement-body">
                            <h3><?php echo e($a['judul']); ?></h3>
                            <div class="announcement-meta">
                                <span><i class="far fa-calendar-alt"></i> <?php echo format_date($a['tanggal']); ?></span>
                            </div>
                            <p><?php echo nl2br(e($a['isi'])); ?></p>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
