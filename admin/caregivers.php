<?php
require_once __DIR__ . '/includes/layout.php';
require_admin();

// ---- Handle actions (PRG) ----
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    csrf_check();
    $action = $_POST['action'] ?? '';

    try {
        if ($action === 'create') {
            $email = trim($_POST['email'] ?? '');
            $dup = db()->prepare('SELECT id FROM users WHERE email=?');
            $dup->execute([$email]);
            if ($dup->fetch()) {
                set_flash('error', 'A user with this email already exists.');
            } else {
                $stmt = db()->prepare(
                    "INSERT INTO users (name,email,password,phone,address,role)
                     VALUES (?,?,?,?,?,'caregiver')"
                );
                $stmt->execute([
                    trim($_POST['name'] ?? ''), $email,
                    password_hash($_POST['password'] ?? 'password123', PASSWORD_BCRYPT),
                    trim($_POST['phone'] ?? ''), trim($_POST['address'] ?? ''),
                ]);
                set_flash('success', 'Caregiver created.');
            }
        } elseif ($action === 'update') {
            $id = (int) ($_POST['id'] ?? 0);
            $pass = $_POST['password'] ?? '';
            if ($pass !== '') {
                $stmt = db()->prepare("UPDATE users SET name=?,email=?,phone=?,address=?,password=? WHERE id=? AND role='caregiver'");
                $stmt->execute([trim($_POST['name']??''),trim($_POST['email']??''),trim($_POST['phone']??''),trim($_POST['address']??''),password_hash($pass,PASSWORD_BCRYPT),$id]);
            } else {
                $stmt = db()->prepare("UPDATE users SET name=?,email=?,phone=?,address=? WHERE id=? AND role='caregiver'");
                $stmt->execute([trim($_POST['name']??''),trim($_POST['email']??''),trim($_POST['phone']??''),trim($_POST['address']??''),$id]);
            }
            set_flash('success', 'Caregiver updated.');
        } elseif ($action === 'delete') {
            $stmt = db()->prepare("DELETE FROM users WHERE id=? AND role='caregiver'");
            $stmt->execute([(int) ($_POST['id'] ?? 0)]);
            set_flash('success', 'Caregiver deleted.');
        }
    } catch (Throwable $ex) {
        set_flash('error', 'Error: ' . $ex->getMessage());
    }
    header('Location: caregivers.php');
    exit;
}

$rows = db()->query(
    "SELECT u.id,u.name,u.email,u.phone,u.address,
            COUNT(te.id) total_shifts, COALESCE(SUM(te.total_charge),0) total_revenue
       FROM users u LEFT JOIN time_entries te ON te.caregiver_id=u.id
      WHERE u.role='caregiver'
   GROUP BY u.id ORDER BY u.name ASC"
)->fetchAll();

$page_title = 'Caregivers';
$active = 'caregivers';
layout_header();
?>
<details class="form-card">
  <summary>+ Add new caregiver</summary>
  <form method="post">
    <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
    <input type="hidden" name="action" value="create">
    <div class="grid2">
      <div><label>Name</label><input name="name" required></div>
      <div><label>Email</label><input type="email" name="email" required></div>
      <div><label>Phone</label><input name="phone"></div>
      <div><label>Password</label><input name="password" placeholder="default: password123"></div>
    </div>
    <label>Address</label><input name="address">
    <div style="height:16px"></div>
    <button class="btn primary" style="width:auto;padding:11px 22px">Create caregiver</button>
  </form>
</details>

<div class="panel">
  <div class="panel-head"><h2><?= count($rows) ?> Caregiver(s)</h2></div>
  <table>
    <thead><tr><th>Name</th><th>Email</th><th>Phone</th><th>Shifts</th><th>Revenue</th><th>Actions</th></tr></thead>
    <tbody>
    <?php if (!$rows): ?>
      <tr><td colspan="6" class="empty">No caregivers yet. Add one above.</td></tr>
    <?php else: foreach ($rows as $r): ?>
      <tr>
        <td><?= e($r['name']) ?></td>
        <td><?= e($r['email']) ?></td>
        <td><?= e($r['phone'] ?: '—') ?></td>
        <td class="num"><?= (int) $r['total_shifts'] ?></td>
        <td class="num">$<?= number_format((float) $r['total_revenue'], 2) ?></td>
        <td>
          <div class="actions">
            <details class="form-card" style="margin:0;padding:0;border:none;background:none">
              <summary style="padding:0"><span class="btn sm">Edit</span></summary>
              <form method="post" style="position:absolute;background:var(--panel);border:1px solid var(--line);padding:16px;border-radius:12px;z-index:9;min-width:280px;margin-top:6px">
                <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="id" value="<?= (int) $r['id'] ?>">
                <label>Name</label><input name="name" value="<?= e($r['name']) ?>" required>
                <label>Email</label><input type="email" name="email" value="<?= e($r['email']) ?>" required>
                <label>Phone</label><input name="phone" value="<?= e($r['phone']) ?>">
                <label>Address</label><input name="address" value="<?= e($r['address']) ?>">
                <label>New password (optional)</label><input name="password" placeholder="leave blank to keep">
                <div style="height:12px"></div>
                <button class="btn primary" style="padding:9px">Save changes</button>
              </form>
            </details>
            <form class="inline" method="post" onsubmit="return confirm('Delete this caregiver? This removes their shifts too.')">
              <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
              <input type="hidden" name="action" value="delete">
              <input type="hidden" name="id" value="<?= (int) $r['id'] ?>">
              <button class="btn sm red">Delete</button>
            </form>
          </div>
        </td>
      </tr>
    <?php endforeach; endif; ?>
    </tbody>
  </table>
</div>
<?php layout_footer(); ?>
