<?php
/**
 * Keamanan: CSRF, rate-limiting login, dan security headers.
 * Dimuat via includes/functions.php
 */

if (!function_exists('sec_csrf_token')) {

    /**
     * Ambil (atau buat) token CSRF untuk sesi saat ini.
     */
    function sec_csrf_token() {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    /**
     * Field tersembunyi CSRF untuk disisipkan di dalam <form>.
     */
    function sec_csrf_field() {
        return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars(sec_csrf_token(), ENT_QUOTES, 'UTF-8') . '">';
    }

    /**
     * Verifikasi token CSRF pada request POST.
     * Gagal => set header 403 dan hentikan eksekusi.
     */
    function sec_verify_csrf() {
        if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
            return true;
        }
        $sent = $_POST['csrf_token'] ?? '';
        $expected = $_SESSION['csrf_token'] ?? '';
        if ($expected === '' || !hash_equals($expected, $sent)) {
            http_response_code(403);
            // Hapus sesi agar token lama tidak dipakai ulang
            session_destroy();
            die('CSRF token tidak valid. Silakan buka kembali halaman dan coba lagi.');
        }
        return true;
    }

    /**
     * Cek throttle login (brute-force protection).
     * Mengembalikan true jika request BOLEH lanjut, false jika harus diblokir.
     */
    function sec_login_throttle_allowed() {
        $maxAttempts = 5;
        $lockSeconds = 900; // 15 menit

        if (!isset($_SESSION['login_attempts'])) {
            $_SESSION['login_attempts'] = 0;
            $_SESSION['login_first_attempt'] = time();
        }

        if ($_SESSION['login_attempts'] >= $maxAttempts) {
            $elapsed = time() - (int)$_SESSION['login_first_attempt'];
            if ($elapsed < $lockSeconds) {
                $remain = $lockSeconds - $elapsed;
                $_SESSION['login_blocked'] = true;
                return ['allowed' => false, 'message' => "Terlalu banyak percobaan login. Silakan coba lagi dalam " . ceil($remain / 60) . " menit."];
            }
            // Reset setelah jeda
            $_SESSION['login_attempts'] = 0;
            $_SESSION['login_first_attempt'] = time();
            unset($_SESSION['login_blocked']);
        }

        return ['allowed' => true, 'message' => ''];
    }

    /**
     * Catat hasil percobaan login.
     * $success: true jika berhasil (reset counter), false jika gagal (increment).
     */
    function sec_login_throttle_register($success) {
        if ($success) {
            unset($_SESSION['login_attempts']);
            unset($_SESSION['login_first_attempt']);
            unset($_SESSION['login_blocked']);
        } else {
            $_SESSION['login_attempts'] = (int)($_SESSION['login_attempts'] ?? 0) + 1;
            if (!isset($_SESSION['login_first_attempt'])) {
                $_SESSION['login_first_attempt'] = time();
            }
        }
    }

    /**
     * Kirim security headers aman (dipanggil di header publik & admin).
     */
    function sec_security_headers() {
        $isSecure = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
        // Jangan izinkan situs dibingkai (clickjacking)
        if (!headers_sent()) {
            header('X-Frame-Options: SAMEORIGIN');
            header('X-Content-Type-Options: nosniff');
            header('Referrer-Policy: strict-origin-when-cross-origin');
            header('Permissions-Policy: geolocation=(), microphone=(), camera=()');
            header('Cache-Control: no-store, no-cache, must-revalidate');
            if ($isSecure) {
                header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
            }
            // CSP sederhana yang tetap mengizinkan sumber gaya/script yang dipakai situs
            header("Content-Security-Policy: default-src 'self' https://fonts.googleapis.com https://fonts.gstatic.com https://cdnjs.cloudflare.com; style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://cdnjs.cloudflare.com; font-src 'self' https://fonts.gstatic.com; script-src 'self' 'unsafe-inline' https://cdnjs.cloudflare.com; img-src 'self' data: blob:; frame-src 'self' https://www.google.com https://maps.google.com; connect-src 'self'");
        }
    }

    /**
     * Perkuat pengaturan sesi PHP (cookie flags + aktivitas timeout).
     * Dipanggil sebelum session_start().
     */
    function sec_session_config() {
        $secure = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
        session_set_cookie_params([
            'lifetime' => 0,
            'path' => '/',
            'secure' => $secure,
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
        session_name('SMKTA_SESSID');
    }

    /**
     * Cegah serangan sesi: timeout idle & absolut.
     */
    function sec_session_timeout() {
        $idleLimit = 1800;      // 30 menit idle
        $absoluteLimit = 7200;  // 120 menit total

        $now = time();
        if (isset($_SESSION['last_activity']) && ($now - $_SESSION['last_activity']) > $idleLimit) {
            session_unset();
            session_destroy();
            if (!headers_sent()) {
                session_name('SMKTA_SESSID');
                session_start();
            }
        }
        if (isset($_SESSION['login_time']) && ($now - $_SESSION['login_time']) > $absoluteLimit) {
            session_unset();
            session_destroy();
            if (function_exists('is_logged_in') && is_logged_in()) {
                header('Location: ' . SITE_URL . 'admin/login.php');
                exit;
            }
        }
        $_SESSION['last_activity'] = $now;
    }
}
