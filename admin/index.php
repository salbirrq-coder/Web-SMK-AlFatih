<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';

if (is_logged_in()) {
    header('Location: ' . SITE_URL . 'admin/dashboard.php');
} else {
    header('Location: ' . SITE_URL . 'admin/login.php');
}
exit;
