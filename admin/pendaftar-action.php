<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';
require_login();
sec_verify_csrf();

$pdo = db();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . SITE_URL . 'admin/pendaftar.php');
    exit;
}

$id = (int)($_POST['id'] ?? 0);
$action = $_POST['action'] ?? '';
$catatan = trim($_POST['catatan'] ?? '');

if (!$id) {
    flash('error', 'Data pendaftar tidak ditemukan.');
    header('Location: ' . SITE_URL . 'admin/pendaftar.php');
    exit;
}

// Ambil data pendaftar
$stmt = $pdo->prepare("SELECT * FROM pendaftar WHERE id = ?");
$stmt->execute([$id]);
$p = $stmt->fetch();
if (!$p) {
    flash('error', 'Data pendaftar tidak ditemukan.');
    header('Location: ' . SITE_URL . 'admin/pendaftar.php');
    exit;
}

switch ($action) {
    case 'verifikasi':
        $pdo->prepare("UPDATE pendaftar SET status = 'DIVERIFIKASI', catatan = NULL WHERE id = ?")->execute([$id]);
        flash('success', "Pendaftaran <strong>" . $p['nomor_pendaftaran'] . "</strong> ditandai sebagai diverifikasi.");
        break;

    case 'terima':
        if ($catatan === '') $catatan = 'Selamat, Anda diterima sebagai peserta didik baru SMK Tahfizh Al-Fatih. Silakan lakukan daftar ulang sesuai jadwal yang ditentukan.';
        $pdo->prepare("UPDATE pendaftar SET status = 'DITERIMA', catatan = ? WHERE id = ?")->execute([$catatan, $id]);
        flash('success', "Pendaftaran <strong>" . $p['nomor_pendaftaran'] . "</strong> telah DITERIMA.");
        break;

    case 'tolak':
        if ($catatan === '') {
            flash('error', 'Alasan penolakan wajib diisi.');
            header('Location: ' . SITE_URL . 'admin/pendaftar.php');
            exit;
        }
        $pdo->prepare("UPDATE pendaftar SET status = 'TIDAK DITERIMA', catatan = ? WHERE id = ?")->execute([$catatan, $id]);
        flash('success', "Pendaftaran <strong>" . $p['nomor_pendaftaran'] . "</strong> telah DITOLAK dengan alasan yang dicatat.");
        break;

    case 'kembalikan':
        $pdo->prepare("UPDATE pendaftar SET status = 'MENUNGGU', catatan = NULL WHERE id = ?")->execute([$id]);
        flash('success', "Status pendaftaran <strong>" . $p['nomor_pendaftaran'] . "</strong> dikembalikan ke MENUNGGU.");
        break;

    default:
        flash('error', 'Aksi tidak dikenali.');
        break;
}

header('Location: ' . SITE_URL . 'admin/pendaftar.php');
exit;
