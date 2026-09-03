<?php
require_once __DIR__ . '/config.php';

/**
 * Escape output untuk XSS safety.
 */
function e($value) {
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * Format tanggal Indonesia.
 */
function format_date($date, $withTime = false) {
    if (!$date) return '-';
    $ts = strtotime($date);
    $format = 'd F Y';
    if ($withTime) $format .= ' H:i';
    static $months = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
    $d = date('j', $ts);
    $m = (int)date('n', $ts);
    $y = date('Y', $ts);
    $t = $withTime ? ' ' . date('H:i', $ts) : '';
    return $d . ' ' . $months[$m - 1] . ' ' . $y . $t;
}

/**
 * Status badge label.
 */
function status_label($status) {
    $map = [
        'MENUNGGU' => 'MENUNGGU VERIFIKASI',
        'DIVERIFIKASI' => 'SEDANG DIVERIFIKASI',
        'DITERIMA' => 'DITERIMA',
        'TIDAK DITERIMA' => 'TIDAK DITERIMA',
    ];
    return $map[$status] ?? $status;
}

/**
 * Status icon (bootstrap icon).
 */
function status_icon($status) {
    $map = [
        'MENUNGGU' => '&#9888;&#65039;',
        'DIVERIFIKASI' => '&#128260;',
        'DITERIMA' => '&#127881;',
        'TIDAK DITERIMA' => '&#10060;',
    ];
    return $map[$status] ?? '&#9632;';
}

/**
 * Deskripsi status untuk tampilan publik.
 */
function status_desc($status) {
    $map = [
        'MENUNGGU' => 'Pendaftaran Anda telah diterima dan sedang menunggu proses verifikasi oleh pihak sekolah.',
        'DIVERIFIKASI' => 'Data pendaftaran Anda sedang diverifikasi oleh pihak sekolah. Mohon menunggu.',
        'DITERIMA' => 'Selamat! Anda dinyatakan diterima sebagai peserta didik baru. Silakan lakukan daftar ulang.',
        'TIDAK DITERIMA' => 'Mohon maaf, Anda belum diterima pada seleksi ini. Tetap semangat dan jangan menyerah.',
    ];
    return $map[$status] ?? '';
}

/**
 * Status badge CSS class.
 */
function status_class($status) {
    $map = [
        'MENUNGGU' => 'status-yellow',
        'DIVERIFIKASI' => 'status-blue',
        'DITERIMA' => 'status-green',
        'TIDAK DITERIMA' => 'status-red',
    ];
    return $map[$status] ?? 'status-gray';
}

/**
 * Nomor pendaftaran otomatis.
 */
function generate_nomor_pendaftaran() {
    $year = date('Y');
    $pdo = db();
    $stmt = $pdo->prepare("SELECT COUNT(*) AS c FROM pendaftar WHERE YEAR(tanggal_daftar) = ?");
    $stmt->execute([$year]);
    $count = (int)$stmt->fetch()['c'];
    $next = $count + 1;
    return 'PPDB-' . $year . '-' . str_pad($next, 5, '0', STR_PAD_LEFT);
}

/**
 * Validasi & simpan upload file. Return nama file atau null.
 */
function handle_upload($fileKey, $folder, $required = false) {
    if (!isset($_FILES[$fileKey]) || $_FILES[$fileKey]['error'] === UPLOAD_ERR_NO_FILE) {
        if ($required) return ['error' => 'File wajib diupload'];
        return ['filename' => null];
    }

    $file = $_FILES[$fileKey];
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['error' => 'Gagal mengupload file'];
    }
    if ($file['size'] > MAX_FILE_SIZE) {
        return ['error' => 'Ukuran file maksimal 2MB'];
    }

    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, ALLOWED_EXTENSIONS)) {
        return ['error' => 'Tipe file tidak diizinkan. Gunakan JPG, PNG, WEBP, atau PDF.'];
    }

    $uploadDir = __DIR__ . '/../assets/uploads/' . $folder . '/';
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

    $filename = $folder . '_' . time() . '_' . substr(md5(uniqid()), 0, 8) . '.' . $ext;
    if (!move_uploaded_file($file['tmp_name'], $uploadDir . $filename)) {
        return ['error' => 'Gagal menyimpan file'];
    }

    return ['filename' => 'assets/uploads/' . $folder . '/' . $filename];
}

/**
 * Set flash message.
 */
function flash($type, $message) {
    $_SESSION['flash'][] = ['type' => $type, 'message' => $message];
}

/**
 * Ambil & hapus flash messages.
 */
function get_flash() {
    if (isset($_SESSION['flash'])) {
        $msgs = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $msgs;
    }
    return [];
}
