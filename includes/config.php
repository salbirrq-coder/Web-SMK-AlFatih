<?php
// ============================================================
// Konfigurasi SMK Tahfizh Al-Fatih
// ============================================================

// Database
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'smk_tahfizh');

// Base URL - ganti sesuai lokasi project
// Jika di XAMPP htdocs: http://localhost/smkTahfizh
// Atur otomatis dari URL
$base_path = '/smkTahfizh';
if (defined('PHP_WINDOWS_VERSION_MAJOR')) {
    $doc = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/smkTahfizh'));
    // Gunakan BASE_URL dinamis sederhana
}
if (!defined('BASE_URL')) {
    $scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/smkTahfizh'));
    // Jika berada di subfolder admin, naikkan satu level
    if (basename($scriptDir) === 'admin') {
        $scriptDir = dirname($scriptDir);
    }
    define('BASE_URL', rtrim($scriptDir, '/') . '/');
    define('SITE_URL', (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http') . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost') . rtrim($scriptDir, '/') . '/');
}

// Info sekolah
define('SCHOOL_NAME', 'SMK Tahfizh Al-Fatih');
define('SCHOOL_TAGLINE', 'Unggul dalam Prestasi, Berakhlak Mulia, dan Berlandaskan Al-Qur\'an');
define('SCHOOL_DESC', 'Menjadi generasi yang unggul dalam ilmu pengetahuan, teknologi, keterampilan, dan akhlak berdasarkan nilai-nilai Al-Qur\'an.');
define('SCHOOL_ADDRESS', 'Jl. Kayu Manis No. 163 Komplek Beringin Indah, Sidomulyo Timur, Marpoyan Damai, Pekanbaru 28125');
define('SCHOOL_PHONE', '0821-6464-4909');
define('SCHOOL_WHATSAPP', '628216464909');
define('SCHOOL_EMAIL', 'smktahfizhalfatih@gmail.com');
define('SCHOOL_MAP', 'https://www.google.com/maps?q=Jl.+Kayu+Manis+No.+163,+Pekanbaru,+Riau&output=embed');
define('SCHOOL_MAP_LINK', 'https://www.google.com/maps/search/?api=1&query=SMK+Tahfizh+Al-Fatih+Jl.+Kayu+Manis+No.+163+Pekanbaru');

// Social Media
define('SOCIAL_YOUTUBE', 'https://www.youtube.com/@SMKTAHFIZHALFATIH');
define('SOCIAL_INSTAGRAM', 'https://linktr.ee/smktahfizhalfatih.media');
define('SOCIAL_FACEBOOK', 'https://www.facebook.com/share/17vsTQo3o1/');

// Session (dengan konfigurasi cookie aman)
require_once __DIR__ . '/security.php';
if (session_status() === PHP_SESSION_NONE) {
    sec_session_config();
    session_start();
}
sec_security_headers();
if (function_exists('sec_session_timeout')) {
    sec_session_timeout();
}

// Upload settings
define('MAX_FILE_SIZE', 2 * 1024 * 1024); // 2MB
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'webp', 'pdf']);
