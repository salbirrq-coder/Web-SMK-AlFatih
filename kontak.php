<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';
ensure_database();

$status = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $subjek = trim($_POST['subjek'] ?? '');
    $pesan = trim($_POST['pesan'] ?? '');
    if ($nama && $email && $pesan) {
        $status = 'success';
    } else {
        $status = 'error';
    }
}

$pageTitle = 'Kontak Kami';
require_once __DIR__ . '/includes/header.php';
?>

<section class="page-hero">
    <div class="page-hero-shape" style="top:18%;left:8%;width:45px;height:45px;border-radius:12px;border:2px solid rgba(255,255,255,0.1);animation:spinSlow 22s linear infinite;"></div>
    <div class="page-hero-shape" style="bottom:20%;right:10%;width:55px;height:55px;border-radius:50%;background:rgba(212,175,55,0.12);animation:floatDiag 9s ease-in-out infinite;"></div>
    <div class="container">
        <div class="hero-badge"><span class="dot"></span> Hubungi Kami</div>
        <h1>Kontak <span class="gold-text">SMK Tahfizh Al-Fatih</span></h1>
        <p>Kami siap membantu Anda. Silakan hubungi kami melalui formulir atau informasi kontak di bawah ini.</p>
    </div>
</section>

<section class="section" style="padding-top:60px;">
    <div class="container" style="max-width:1000px;">
        <div class="contact-grid">
            <div class="reveal-left">
                <?php if ($status === 'success'): ?>
                    <div class="alert alert-success"><i class="fas fa-check-circle"></i> <div>Terima kasih! Pesan Anda telah terkirim. Kami akan segera menghubungi Anda.</div></div>
                <?php elseif ($status === 'error'): ?>
                    <div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> <div>Mohon lengkapi semua field yang wajib diisi.</div></div>
                <?php endif; ?>

                <div class="form-card">
                    <h2 style="font-size:20px;color:var(--forest-900);margin-bottom:20px;"><i class="fas fa-paper-plane" style="color:var(--forest-500);margin-right:10px;"></i>Kirim Pesan</h2>
                    <form action="" method="POST">
                        <div class="form-grid" style="grid-template-columns:1fr;">
                            <div class="form-group"><label class="form-label">Nama Lengkap <span class="req">*</span></label><input class="form-control" type="text" name="nama"></div>
                            <div class="form-group"><label class="form-label">Email <span class="req">*</span></label><input class="form-control" type="email" name="email"></div>
                            <div class="form-group"><label class="form-label">Subjek</label><input class="form-control" type="text" name="subjek"></div>
                            <div class="form-group"><label class="form-label">Pesan <span class="req">*</span></label><textarea class="form-control" name="pesan" rows="4"></textarea></div>
                        </div>
                        <button type="submit" class="btn btn-green btn-lg" style="width:100%;"><i class="fas fa-paper-plane"></i> KIRIM PESAN</button>
                    </form>
                </div>
            </div>

            <div class="reveal-right">
                <div class="contact-info">
                    <div class="contact-info-item">
                        <div class="ci-icon"><i class="fas fa-map-marker-alt"></i></div>
                        <div><h4>Alamat Sekolah</h4><p><?php echo SCHOOL_ADDRESS; ?></p></div>
                    </div>
                    <div class="contact-info-item">
                        <div class="ci-icon"><i class="fas fa-phone-alt"></i></div>
                        <div><h4>Telepon</h4><p><?php echo SCHOOL_PHONE; ?></p></div>
                    </div>
                    <div class="contact-info-item">
                        <div class="ci-icon"><i class="fab fa-whatsapp"></i></div>
                        <div><h4>WhatsApp</h4><p><a href="https://wa.me/<?php echo SCHOOL_WHATSAPP; ?>" target="_blank" rel="noopener noreferrer" style="color:var(--forest-600);"><?php echo SCHOOL_PHONE; ?></a></p></div>
                    </div>
                    <div class="contact-info-item">
                        <div class="ci-icon"><i class="fas fa-envelope"></i></div>
                        <div><h4>Email</h4><p><?php echo SCHOOL_EMAIL; ?></p></div>
                    </div>
                </div>
                <div class="map-box">
                    <iframe src="<?php echo SCHOOL_MAP; ?>" width="100%" height="280" style="border:0;" allowfullscreen loading="lazy" referrerpolicy="no-referrer-when-downgrade" title="Lokasi Sekolah"></iframe>
                </div>
                <div class="qr-location">
                    <div class="qr-card reveal-scale">
                        <div id="qrCode"></div>
                        <div class="qr-text">
                            <i class="fas fa-qrcode"></i>
                            <h4>Lokasi Sekolah</h4>
                            <p>Scan QR code untuk membuka lokasi di Google Maps.</p>
                            <a href="<?php echo SCHOOL_MAP_LINK; ?>" target="_blank" rel="noopener noreferrer" class="btn btn-outline-green btn-sm"><i class="fas fa-map-marked-alt"></i> BUKA LOKASI</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="section" style="padding-top:0;padding-bottom:80px;">
    <div class="container" style="max-width:1000px;text-align:center;">
        <div class="cta-quick reveal">
            <h3 style="font-size:24px;color:var(--forest-900);margin-bottom:10px;">Mau Bertanya Langsung?</h3>
            <p style="color:var(--text-gray);margin-bottom:20px;">Hubungi kami melalui WhatsApp untuk informasi pendaftaran dan pertanyaan lainnya.</p>
            <a href="https://wa.me/<?php echo SCHOOL_WHATSAPP; ?>" target="_blank" rel="noopener noreferrer" class="btn btn-green btn-lg"><i class="fab fa-whatsapp" style="font-size:20px;"></i> CHAT VIA WHATSAPP</a>
        </div>
    </div>
</div>

<script src="<?php echo SITE_URL; ?>assets/js/qrcode.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    var el = document.getElementById('qrCode');
    if (el && typeof QRCode !== 'undefined') {
        var qr = new QRCode(el, {
            text: <?php echo json_encode(SCHOOL_MAP_LINK, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>,
            width: 180,
            height: 180,
            colorDark: '#064e3b',
            colorLight: '#ffffff',
            correctLevel: QRCode.CorrectLevel.H
        });
    }
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>