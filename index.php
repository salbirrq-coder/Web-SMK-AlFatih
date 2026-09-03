<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

ensure_database();
$pdo = db();

$programs = $pdo->query("SELECT * FROM program_keahlian ORDER BY id")->fetchAll();
$fasilitas = $pdo->query("SELECT * FROM fasilitas ORDER BY id")->fetchAll();
$berita = $pdo->query("SELECT * FROM berita ORDER BY tanggal DESC, id DESC LIMIT 3")->fetchAll();

$pageTitle = 'Selamat Datang';
require_once __DIR__ . '/includes/header.php';
?>

<!-- ======= HERO ======= -->
<section class="hero">
    <canvas id="heroCanvas"></canvas>

    <div class="hero-shape hero-shape-1"></div>
    <div class="hero-shape hero-shape-2"></div>
    <div class="hero-shape hero-shape-3"></div>
    <div class="hero-shape hero-shape-4"></div>
    <div class="hero-geo hero-geo-1">&#10043;</div>
    <div class="hero-geo hero-geo-2">&#10043;</div>

    <div class="hero-float-shape" style="top:20%;right:15%;width:50px;height:50px;border:2px solid rgba(212,175,55,0.2);border-radius:12px;animation:spinSlow 25s linear infinite;"></div>
    <div class="hero-float-shape" style="bottom:35%;left:12%;width:30px;height:30px;border:2px solid rgba(16,185,129,0.2);border-radius:50%;animation:floatDiag 8s ease-in-out infinite;"></div>

    <div class="hero-content">
        <div class="hero-badge"><span class="dot"></span> Penerimaan Peserta Didik Baru 2026/2027</div>
        <p class="hero-eyebrow">Selamat Datang di</p>
        <h1 class="hero-title">
            <span class="white">SMK TAHFIZH</span><br>
            <span class="gold">AL-FATIH</span>
        </h1>
        <p class="hero-subtitle">"Unggul dalam Prestasi, Berakhlak Mulia, dan <span class="gold-text">Berlandaskan Al-Qur'an</span>"</p>
        <p class="hero-desc">Menjadi generasi yang unggul dalam ilmu pengetahuan, teknologi, keterampilan, dan akhlak berdasarkan nilai-nilai Al-Qur'an.</p>
        <div class="hero-actions">
            <a href="<?php echo SITE_URL; ?>pendaftaran.php" class="btn btn-gold btn-lg glow-gold"><i class="fas fa-user-graduate"></i> DAFTAR SEKARANG</a>
            <a href="#tentang" class="btn btn-ghost btn-lg"><i class="fas fa-building"></i> LIHAT PROFIL SEKOLAH</a>
        </div>
    </div>

    <a href="#tentang" class="hero-scroll">Scroll <i class="fas fa-chevron-down"></i></a>
</section>

<!-- ======= TENTANG ======= -->
<section class="section" id="tentang">
    <div class="container">
        <div class="text-center" style="max-width:700px;margin:0 auto 60px;">
            <span class="section-label">Profil Sekolah</span>
            <h2 class="section-title">Tentang SMK Tahfizh Al-Fatih</h2>
        </div>

        <!-- Stats -->
        <div class="stats-grid stagger">
            <div class="stat-card reveal-scale">
                <div class="stat-icon"><i class="fas fa-users"></i></div>
                <div class="stat-value count-up" data-target="1200" data-suffix="+">0</div>
                <div class="stat-label">Jumlah Siswa</div>
            </div>
            <div class="stat-card reveal-scale">
                <div class="stat-icon"><i class="fas fa-chalkboard-teacher"></i></div>
                <div class="stat-value count-up" data-target="85" data-suffix="+">0</div>
                <div class="stat-label">Tenaga Pengajar</div>
            </div>
            <div class="stat-card reveal-scale">
                <div class="stat-icon"><i class="fas fa-book-open"></i></div>
                <div class="stat-value count-up" data-target="<?php echo count($programs); ?>" data-suffix="+">0</div>
                <div class="stat-label">Program Keahlian</div>
            </div>
            <div class="stat-card reveal-scale">
                <div class="stat-icon"><i class="fas fa-trophy"></i></div>
                <div class="stat-value count-up" data-target="150" data-suffix="+">0</div>
                <div class="stat-label">Prestasi</div>
            </div>
        </div>

        <!-- Visi Misi -->
        <div class="visi-misi-grid">
            <div class="vm-card dark reveal-left">
                <div class="vm-icon"><i class="fas fa-eye"></i></div>
                <h3>Visi</h3>
                <p>Terwujudnya generasi yang unggul dalam prestasi, berakhlak mulia, dan berlandaskan Al-Qur'an untuk menghadapi tantangan global.</p>
            </div>
            <div class="vm-card light reveal-right">
                <div class="vm-icon"><i class="fas fa-bullseye"></i></div>
                <h3>Misi</h3>
                <ul class="misi-list">
                    <li><span class="misi-num">1</span> Menyelenggarakan pembelajaran yang berorientasi pada Al-Qur'an dan akhlak mulia.</li>
                    <li><span class="misi-num">2</span> Mengembangkan potensi siswa dalam ilmu pengetahuan, teknologi, dan keterampilan.</li>
                    <li><span class="misi-num">3</span> Menumbuhkan budaya literasi dan kecintaan terhadap Al-Qur'an.</li>
                    <li><span class="misi-num">4</span> Membangun karakter islami yang kuat dalam diri setiap siswa.</li>
                    <li><span class="misi-num">5</span> Menyiapkan lulusan yang siap bekerja, berwirausaha, dan melanjutkan studi.</li>
                </ul>
            </div>
        </div>

        <!-- Nilai -->
        <div class="values-band reveal-scale">
            <h3>Nilai-nilai Sekolah</h3>
            <div class="values-grid stagger">
                <div class="value-card tilt-card">
                    <div class="value-icon"><i class="fas fa-heart"></i></div>
                    <h4>Akhlak Mulia</h4>
                    <p>Berperilaku baik dalam segala situasi</p>
                </div>
                <div class="value-card tilt-card">
                    <div class="value-icon"><i class="fas fa-scale-balanced"></i></div>
                    <h4>Integritas</h4>
                    <p>Jujur dan dapat dipercaya</p>
                </div>
                <div class="value-card tilt-card">
                    <div class="value-icon"><i class="fas fa-award"></i></div>
                    <h4>Unggul</h4>
                    <p>Selalu berusaha menjadi yang terbaik</p>
                </div>
                <div class="value-card tilt-card">
                    <div class="value-icon"><i class="fas fa-book-quran"></i></div>
                    <h4>Al-Qur'an</h4>
                    <p>Berlandaskan nilai-nilai Al-Qur'an</p>
                </div>
            </div>
        </div>

        <div class="reveal" style="max-width:760px;margin:60px auto 0;text-align:center;">
            <p style="color:var(--text-gray);font-size:16px;line-height:1.8;">SMK Tahfizh Al-Fatih adalah sekolah menengah kejuruan yang memadukan pendidikan vokasi dengan pembelajaran tahfizh Al-Qur'an. Kami berkomitmen mencetak generasi yang tidak hanya unggul dalam keterampilan profesional, tetapi juga memiliki fondasi akhlak dan spiritual yang kuat.</p>
        </div>
    </div>
</section>

<!-- ======= PROGRAM ======= -->
<section class="section section-alt" id="program">
    <div class="container">
        <div class="text-center" style="max-width:700px;margin:0 auto 60px;">
            <span class="section-label">Kompetensi Keahlian</span>
            <h2 class="section-title">Program Keahlian</h2>
            <p class="section-subtitle" style="margin:20px auto 0;">Pilih program keahlian yang sesuai dengan minat dan bakat Anda. Setiap program dilengkapi dengan kurikulum modern dan tenaga pengajar profesional.</p>
        </div>

        <div class="program-grid stagger">
            <?php foreach ($programs as $i => $p): ?>
                <a href="<?php echo SITE_URL; ?>pendaftaran.php" class="program-card reveal">
                    <div class="program-icon"><?php echo e($p['icon'] ?: '🎓'); ?></div>
                    <h3><?php echo e($p['nama']); ?></h3>
                    <p><?php echo e($p['deskripsi']); ?></p>
                    <span class="program-link">Pelajari & Daftar <i class="fas fa-arrow-right"></i></span>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ======= FASILITAS ======= -->
<section class="section section-dark" id="fasilitas">
    <div class="container">
        <div class="text-center" style="max-width:700px;margin:0 auto 60px;">
            <span class="section-label" style="color:var(--gold-400);">Sarana & Prasarana</span>
            <h2 class="section-title">Fasilitas Sekolah</h2>
            <p class="section-subtitle" style="margin:20px auto 0;">Kami menyediakan fasilitas modern dan nyaman untuk mendukung kegiatan belajar mengajar siswa.</p>
        </div>

        <div class="fasilitas-grid stagger">
            <?php foreach ($fasilitas as $i => $f): ?>
                <div class="fasilitas-card reveal-scale">
                    <div class="bg">
                        <?php if ($f['gambar']): ?>
                            <img src="<?php echo e($f['gambar']); ?>" alt="<?php echo e($f['nama']); ?>">
                        <?php else: ?>
                            <span class="placeholder-icon"><i class="fas fa-building"></i></span>
                        <?php endif; ?>
                    </div>
                    <div class="overlay">
                        <h3><?php echo e($f['nama']); ?></h3>
                        <div class="desc"><?php echo e($f['deskripsi']); ?></div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ======= BERITA ======= -->
<section class="section" id="berita">
    <div class="container">
        <div class="text-center" style="max-width:700px;margin:0 auto 60px;">
            <span class="section-label">Kabar Terkini</span>
            <h2 class="section-title">Berita & Informasi Terbaru</h2>
        </div>

        <div class="berita-grid stagger">
            <?php foreach ($berita as $b): ?>
                <article class="berita-card reveal">
                    <div class="berita-thumb">
                        <?php if ($b['gambar']): ?>
                            <img src="<?php echo e($b['gambar']); ?>" alt="<?php echo e($b['judul']); ?>">
                        <?php else: ?>
                            <div class="placeholder-icon"><i class="fas fa-file-lines"></i></div>
                        <?php endif; ?>
                        <span class="berita-kategori"><?php echo e($b['kategori']); ?></span>
                    </div>
                    <div class="berita-body">
                        <div class="berita-meta">
                            <span><i class="far fa-calendar"></i> <?php echo format_date($b['tanggal']); ?></span>
                            <span><i class="far fa-folder"></i> <?php echo e($b['kategori']); ?></span>
                        </div>
                        <h3 class="berita-title"><?php echo e($b['judul']); ?></h3>
                        <p class="berita-desc"><?php echo e($b['deskripsi']); ?></p>
                        <a href="#berita" class="berita-read">Baca Selengkapnya <i class="fas fa-arrow-right"></i></a>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ======= CTA ======= -->
<section class="cta-section" id="daftar">
    <div class="container text-center">
        <span class="section-label" style="justify-content:center;color:var(--gold-400);">Cara Mendaftar</span>
        <h2 class="section-title" style="color:#fff;margin-bottom:10px;">Cara Mendaftar di <span class="hero-title gold" style="font-size:inherit;">SMK Tahfizh Al-Fatih</span></h2>
        <p class="section-subtitle" style="margin:20px auto 0;color:rgba(255,255,255,0.7);">Proses pendaftaran sederhana dan cepat. Ikuti langkah-langkah berikut untuk bergabung bersama kami.</p>

        <div class="steps-grid stagger">
            <div class="step-card reveal-scale">
                <span class="step-num">1</span>
                <div class="step-icon"><i class="fas fa-user-graduate"></i></div>
                <h3>Isi Formulir</h3>
                <p>Lengkapi data siswa dan orang tua</p>
            </div>
            <div class="step-card reveal-scale">
                <span class="step-num">2</span>
                <div class="step-icon"><i class="fas fa-check-circle"></i></div>
                <h3>Dapatkan No. Pendaftaran</h3>
                <p>Nomor pendaftaran otomatis dibuat</p>
            </div>
            <div class="step-card reveal-scale">
                <span class="step-num">3</span>
                <div class="step-icon"><i class="fas fa-search"></i></div>
                <h3>Cek Status</h3>
                <p>Pantau status pendaftaran Anda</p>
            </div>
            <div class="step-card reveal-scale">
                <span class="step-num">4</span>
                <div class="step-icon"><i class="fas fa-bullhorn"></i></div>
                <h3>Lihat Pengumuman</h3>
                <p>Hasil seleksi diumumkan</p>
            </div>
        </div>

        <a href="<?php echo SITE_URL; ?>pendaftaran.php" class="btn btn-gold btn-lg glow-gold reveal-scale"><i class="fas fa-arrow-right"></i> MULAI PENDAFTARAN SEKARANG</a>
    </div>
</section>

<!-- ======= SOCIAL MEDIA ======= -->
<section class="section">
    <div class="container">
        <div class="text-center" style="max-width:700px;margin:0 auto 60px;">
            <span class="section-label">Stay Connected</span>
            <h2 class="section-title">Ikuti Media Sosial Kami</h2>
            <p class="section-subtitle" style="margin:20px auto 0;">Temukan informasi terbaru dan kegiatan SMK Tahfizh Al-Fatih melalui media sosial resmi kami.</p>
        </div>

        <div class="social-grid stagger">
            <a href="<?php echo SOCIAL_YOUTUBE; ?>" target="_blank" rel="noopener noreferrer" title="YouTube" class="social-card reveal-scale tilt-card">
                <div class="social-icon soc-youtube"><i class="fab fa-youtube"></i></div>
                <h3>YouTube</h3>
                <p>Video kegiatan & dokumentasi sekolah</p>
                <div class="social-visit">KUNJUNGI &#8594;</div>
            </a>
            <a href="<?php echo SOCIAL_INSTAGRAM; ?>" target="_blank" rel="noopener noreferrer" title="Instagram" class="social-card reveal-scale tilt-card">
                <div class="social-icon soc-instagram"><i class="fab fa-instagram"></i></div>
                <h3>Instagram</h3>
                <p>Foto & update harian sekolah</p>
                <div class="social-visit">KUNJUNGI &#8594;</div>
            </a>
            <a href="<?php echo SOCIAL_FACEBOOK; ?>" target="_blank" rel="noopener noreferrer" title="Facebook" class="social-card reveal-scale tilt-card">
                <div class="social-icon soc-facebook"><i class="fab fa-facebook-f"></i></div>
                <h3>Facebook</h3>
                <p>Informasi resmi & pengumuman</p>
                <div class="social-visit">KUNJUNGI &#8594;</div>
            </a>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
