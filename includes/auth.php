<?php
require_once __DIR__ . '/db.php';

/**
 * Cek apakah admin sudah login.
 */
function is_logged_in() {
    return isset($_SESSION['admin_id']);
}

/**
 * Redirect ke halaman login jika belum login.
 */
function require_login() {
    if (!is_logged_in()) {
        header('Location: ' . SITE_URL . 'admin/login.php');
        exit;
    }
}

/**
 * Ambil data admin yang login.
 */
function current_admin() {
    if (!is_logged_in()) return null;
    $stmt = db()->prepare("SELECT id, email, username, name FROM admin WHERE id = ?");
    $stmt->execute([$_SESSION['admin_id']]);
    return $stmt->fetch();
}

/**
 * Verifikasi login admin.
 */
function attempt_login($emailOrUsername, $password) {
    $stmt = db()->prepare("SELECT * FROM admin WHERE email = ? OR username = ?");
    $stmt->execute([$emailOrUsername, $emailOrUsername]);
    $admin = $stmt->fetch();
    if ($admin && password_verify($password, $admin['password'])) {
        session_regenerate_id(true);
        $_SESSION['admin_id'] = $admin['id'];
        return $admin;
    }
    return null;
}
