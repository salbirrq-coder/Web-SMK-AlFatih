<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';

// Jika sudah login, redirect ke dashboard
if (is_logged_in()) {
    header('Location: ' . SITE_URL . 'admin/dashboard.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    sec_verify_csrf();
    $throttle = sec_login_throttle_allowed();
    if (!$throttle['allowed']) {
        $error = $throttle['message'];
    } else {
        $identity = trim($_POST['identity'] ?? '');
        $password = $_POST['password'] ?? '';
        if ($identity === '' || $password === '') {
            $error = 'Username/email dan password wajib diisi.';
        } else {
            $admin = attempt_login($identity, $password);
            if ($admin) {
                sec_login_throttle_register(true);
                header('Location: ' . SITE_URL . 'admin/dashboard.php');
                exit;
            } else {
                sec_login_throttle_register(false);
                $error = 'Username/email atau password salah.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin | <?php echo SCHOOL_NAME; ?></title>
    <link rel="icon" href="<?php echo SITE_URL; ?>assets/img/logo.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>assets/css/style.css">
    <style>
        body { min-height: 100vh; display: flex; align-items: center; justify-content: center; background: linear-gradient(135deg, var(--forest-950), var(--forest-800), var(--forest-600)); padding: 24px; }
        .login-wrap { width: 100%; max-width: 420px; }
        .login-card { background: #fff; border-radius: 26px; padding: 40px; box-shadow: var(--shadow-lg); }
        .login-logo { width: 84px; height: 84px; margin: 0 auto 18px; border-radius: 20px; object-fit: contain; background: var(--forest-50); padding: 6px; display: block; }
        .login-title { text-align: center; font-size: 22px; color: var(--forest-900); margin-bottom: 4px; }
        .login-sub { text-align: center; color: var(--text-gray); font-size: 14px; margin-bottom: 26px; }
        .login-input { position: relative; margin-bottom: 18px; }
        .login-input i { position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: var(--forest-400); }
        .login-input input { width: 100%; padding: 14px 16px 14px 46px; border: 1px solid var(--border); border-radius: 12px; font-size: 14px; }
        .login-input input:focus { outline: none; border-color: var(--forest-500); box-shadow: 0 0 0 3px rgba(16,185,129,0.15); }
        .back-link { display: block; text-align: center; margin-top: 18px; color: rgba(255,255,255,0.8); font-size: 13px; }
        .back-link:hover { color: var(--gold-400); }
        .login-hint { text-align: center; margin-top: 18px; font-size: 12px; color: var(--text-gray); background: var(--forest-50); padding: 10px; border-radius: 10px; }
    </style>
</head>
<body>
    <div class="login-wrap">
        <div class="login-card">
            <img src="<?php echo SITE_URL; ?>assets/img/logo.png" alt="Logo" class="login-logo">
            <h1 class="login-title">Admin Panel</h1>
            <p class="login-sub"><?php echo SCHOOL_NAME; ?></p>

            <?php if ($error): ?>
                <div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> <div><?php echo e($error); ?></div></div>
            <?php endif; ?>

            <form action="" method="POST">
                <?php echo sec_csrf_field(); ?>
                <div class="login-input">
                    <i class="fas fa-user"></i>
                    <input type="text" name="identity" placeholder="Username atau Email" value="<?php echo e($_POST['identity'] ?? ''); ?>" autocomplete="username">
                </div>
                <div class="login-input">
                    <i class="fas fa-lock"></i>
                    <input type="password" name="password" placeholder="Password" autocomplete="current-password">
                </div>
                <button type="submit" class="btn btn-green btn-block" style="width:100%;"><i class="fas fa-sign-in-alt"></i> MASUK</button>
            </form>
        </div>
        <a href="<?php echo SITE_URL; ?>" class="back-link"><i class="fas fa-arrow-left"></i> Kembali ke beranda</a>
    </div>
</body>
</html>
