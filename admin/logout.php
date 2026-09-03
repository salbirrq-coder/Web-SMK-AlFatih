<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';

session_unset();
session_destroy();
header('Location: ' . SITE_URL . 'admin/login.php');
exit;
