<?php
require_once __DIR__ . '/includes/layout.php';
require_admin();

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    csrf_check();
    $action = $_POST['action'] ?? '';
    try {
        if ($action === 'create') {
            $stmt = db()->prepare('INSERT INTO policyholders (name,policy_number,phone,address) VALUES (?,?,?,?)');
            $stmt->execute([trim($_POST['name']??''),trim($_POST['policy_number']??''),trim($_POST['phone']??''),trim($_POST['address']??'')]);
            set_flash('success', 'Policyholder created.');
        } elseif ($action === 'update') {
            $stmt = db()->prepare('UPDATE policyholders SET name=?,policy_number=?,phone=?,address=? WHERE id=?');
            $stmt->execute([trim($_POST['name']??''),trim($_POST['policy_number']??''),trim($_POST['phone']??''),trim($_POST['address']??''),(int)($_POST['id']??0)]);
            set_flash('success', 'Policyholder updated.');
        } elseif ($action === 'delete') {
            $stmt = db()->prepare('DELETE FROM policyholders WHERE id=?');
            $stmt->execute([(int)($_POST['id']??0)]);
            set_flash('success', 'Policyholder deleted.');
        }
    } catch (Throwable $ex) {
        set_flash('error', 'Error: ' . $ex->getMessage());
    }
    header('Location: policyholders.php');
    exit;
}

$rows = db()->query(
    "SELECT p.id,p.name,p.policy_number,p.phone,p.address,
            COUNT(te.id) total_shifts,
            SUM(CASE WHEN te.status='completed' THEN 1 ELSE 0 END) completed_shifts
       FROM policyholders p LEFT JOIN time_entries te ON te.policyholder_id=p.id
   GROUP BY p.id ORDER BY p.name ASC"
)->fetchAll();

$page_title = 'Policyholders';
$active = 'policyholders';
layout_header();
?>
<details class="form-card">
  <summary>+ Add new policyholder</summary>
  <form method="post">
    <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
    <input type="hidden" name="action" value="create">
    <div class="grid2">
      <div><label>Name</label><input name="name" required></div>
      <div><label>Policy number</label><input name="policy_number"></div>
      <div><label>Phone</label><input name="phone"></div>
      <div><label>Address</label><input name="address"></div>
    </div>
    <div style="height:16px"></div>
    <button class="btn primary" style="width:auto;padding:11px 22px">Create policyholder</button>
  </form>
</details>

<div class="panel">
  <div class="panel-head"><h2><?= count($rows) ?> Policyholder(s)</h2></div>
  <table>
    <thead><tr><th>Name</th><th>Policy #</th><th>Phone</th><th>Total Shifts</th><th>Completed</th><th>Actions</th></tr></thead>
    <tbody>
    <?php if (!$rows): ?>
      <tr><td colspan="6" class="empty">No policyholders yet.</td></tr>
    <?php else: foreach ($rows as $r): ?>
      <tr>
        <td><?= e($r['name']) ?></td>
        <td><?= e($r['policy_number'] ?: '—') ?></td>
        <td><?= e($r['phone'] ?: '—') ?></td>
        <td class="num"><?= (int) $r['total_shifts'] ?></td>
        <td class="num"><?= (int) $r['completed_shifts'] ?></td>
        <td>
          <div class="actions">
            <details class="form-card" style="margin:0;padding:0;border:none;background:none">
              <summary style="padding:0"><span class="btn sm">Edit</span></summary>
              <form method="post" style="position:absolute;background:var(--panel);border:1px solid var(--line);padding:16px;border-radius:12px;z-index:9;min-width:280px;margin-top:6px">
                <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="id" value="<?= (int) $r['id'] ?>">
                <label>Name</label><input name="name" value="<?= e($r['name']) ?>" required>
                <label>Policy number</label><input name="policy_number" value="<?= e($r['policy_number']) ?>">
                <label>Phone</label><input name="phone" value="<?= e($r['phone']) ?>">
                <label>Address</label><input name="address" value="<?= e($r['address']) ?>">
                <div style="height:12px"></div>
                <button class="btn primary" style="padding:9px">Save changes</button>
              </form>
            </details>
            <form class="inline" method="post" onsubmit="return confirm('Delete this policyholder?')">
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
