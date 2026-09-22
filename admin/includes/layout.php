<?php
// admin/includes/layout.php
// Usage:
//   $page_title = 'Dashboard'; $active = 'dashboard';
//   require 'includes/layout.php';  layout_header();
//   ... page html ...
//   layout_footer();
require_once __DIR__ . '/auth.php';

function layout_header(): void
{
    global $page_title, $active;
    $admin  = current_admin();
    $active = $active ?? '';
    $nav = [
        'dashboard'     => ['Dashboard',     'dashboard.php'],
        'caregivers'    => ['Caregivers',    'caregivers.php'],
        'policyholders' => ['Policyholders', 'policyholders.php'],
        'shifts'        => ['Shifts',        'shifts.php'],
    ];
    ?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= e($page_title ?? 'Admin') ?> — Homecare Admin</title>
  <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<div class="shell">
  <aside class="sidebar">
    <div class="brand">
      <div class="logo">AC</div>
      <div>
        <strong>Homecare</strong>
        <span>Admin Panel</span>
      </div>
    </div>
    <nav>
      <?php foreach ($nav as $key => [$label, $href]): ?>
        <a href="<?= $href ?>" class="<?= $active === $key ? 'on' : '' ?>">
          <span class="dot"></span><?= $label ?>
        </a>
      <?php endforeach; ?>
    </nav>
    <div class="side-foot">
      <div class="who">
        <div class="avatar"><?= e(strtoupper(substr($admin['name'] ?? 'A', 0, 1))) ?></div>
        <div class="meta">
          <strong><?= e($admin['name'] ?? '') ?></strong>
          <span><?= e($admin['email'] ?? '') ?></span>
        </div>
      </div>
      <a class="logout" href="logout.php">Log out</a>
    </div>
  </aside>
  <main class="content">
    <header class="topbar">
      <h1><?= e($page_title ?? '') ?></h1>
    </header>
    <div class="page">
    <?php
    if (!empty($_SESSION['flash'])) {
        $f = $_SESSION['flash'];
        unset($_SESSION['flash']);
        echo '<div class="flash ' . e($f['type']) . '">' . e($f['msg']) . '</div>';
    }
}

function set_flash(string $type, string $msg): void
{
    $_SESSION['flash'] = ['type' => $type, 'msg' => $msg];
}

function layout_footer(): void
{
    ?>
    </div>
  </main>
</div>
</body>
</html>
    <?php
}
