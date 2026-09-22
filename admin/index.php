<?php
// admin/index.php - login
require_once __DIR__ . '/includes/auth.php';

if (current_admin()) {
    header('Location: dashboard.php');
    exit;
}

$error = '';
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    csrf_check();
    $email = trim($_POST['email'] ?? '');
    $pass  = $_POST['password'] ?? '';
    if (admin_login($email, $pass)) {
        header('Location: dashboard.php');
        exit;
    }
    $error = 'Invalid admin credentials.';
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Login — Homecare Admin</title>
  <link rel="stylesheet" href="assets/style.css">
</head>
<body>
  <div class="login-wrap">
    <form class="login-card" method="post" autocomplete="off">
      <div class="logo">AC</div>
      <h1>Welcome back</h1>
      <p>Sign in to the Homecare admin panel</p>

      <?php if ($error): ?>
        <div class="flash error"><?= e($error) ?></div>
      <?php endif; ?>

      <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
      <label>Email</label>
      <input type="email" name="email" required autofocus placeholder="admin@homecare.com">
      <label>Password</label>
      <input type="password" name="password" required placeholder="••••••••">
      <div style="height:20px"></div>
      <button class="btn primary" type="submit">Sign in</button>
    </form>
  </div>
</body>
</html>
