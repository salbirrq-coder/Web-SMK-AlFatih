<?php
require_once __DIR__ . '/config.php';
$footerMenu = [
    ['label' => 'Beranda', 'href' => SITE_URL],
    ['label' => 'Tentang', 'href' => SITE_URL . 'index.php#tentang'],
    ['label' => 'Program Keahlian', 'href' => SITE_URL . 'index.php#program'],
    ['label' => 'Pendaftaran', 'href' => SITE_URL . 'pendaftaran.php'],
    ['label' => 'Cek Status', 'href' => SITE_URL . 'cek-status.php'],
    ['label' => 'Pengumuman', 'href' => SITE_URL . 'pengumuman.php'],
    ['label' => 'Kontak', 'href' => SITE_URL . 'kontak.php'],
];
?>
<footer class="footer">
    <div class="container">
        <div class="footer-grid">
            <div class="footer-brand">
                <img src="<?php echo SITE_URL; ?>assets/img/logo.png" alt="Logo" class="footer-logo">
                <div>
                    <h3>SMK TAHFIZH</h3>
                    <p class="footer-brand-sub">AL-FATIH</p>
                </div>
                <p class="footer-tagline"><?php echo SCHOOL_TAGLINE; ?></p>
                <div class="footer-social">
                    <a href="<?php echo SOCIAL_YOUTUBE; ?>" target="_blank" rel="noopener noreferrer" title="YouTube" class="social-btn social-youtube"><i class="fab fa-youtube"></i></a>
                    <a href="<?php echo SOCIAL_INSTAGRAM; ?>" target="_blank" rel="noopener noreferrer" title="Instagram" class="social-btn social-instagram"><i class="fab fa-instagram"></i></a>
                    <a href="<?php echo SOCIAL_FACEBOOK; ?>" target="_blank" rel="noopener noreferrer" title="Facebook" class="social-btn social-facebook"><i class="fab fa-facebook-f"></i></a>
                </div>
            </div>

            <div class="footer-col">
                <h4>Menu</h4>
                <ul>
                    <?php foreach ($footerMenu as $m): ?>
                        <li><a href="<?php echo e($m['href']); ?>"><?php echo e($m['label']); ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <div class="footer-col">
                <h4>Kontak</h4>
                <ul class="footer-contact">
                    <li><i class="fas fa-map-marker-alt"></i> <span><?php echo SCHOOL_ADDRESS; ?></span></li>
                    <li><i class="fas fa-phone-alt"></i> <a href="tel:<?php echo SCHOOL_PHONE; ?>"><?php echo SCHOOL_PHONE; ?></a></li>
                    <li><i class="fas fa-envelope"></i> <a href="mailto:<?php echo SCHOOL_EMAIL; ?>"><?php echo SCHOOL_EMAIL; ?></a></li>
                </ul>
            </div>

            <div class="footer-col">
                <h4>Ikuti Media Sosial</h4>
                <p class="footer-social-text">Temukan informasi terbaru dan kegiatan sekolah melalui media sosial resmi kami.</p>
                <a href="<?php echo SOCIAL_YOUTUBE; ?>" target="_blank" rel="noopener noreferrer" class="footer-social-link"><i class="fab fa-youtube"></i> YouTube</a>
                <a href="<?php echo SOCIAL_INSTAGRAM; ?>" target="_blank" rel="noopener noreferrer" class="footer-social-link"><i class="fab fa-instagram"></i> Instagram</a>
                <a href="<?php echo SOCIAL_FACEBOOK; ?>" target="_blank" rel="noopener noreferrer" class="footer-social-link"><i class="fab fa-facebook-f"></i> Facebook</a>
            </div>
        </div>

        <div class="footer-bottom">
            <p>&copy; 2026 <?php echo SCHOOL_NAME; ?>. All Rights Reserved.</p>
        </div>
    </div>
</footer>

<a href="#" class="back-to-top" id="backToTop" aria-label="Kembali ke atas"><i class="fas fa-chevron-up"></i></a>

<script src="<?php echo SITE_URL; ?>assets/js/main.js"></script>
</body>
</html>
