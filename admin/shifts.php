<?php
require_once __DIR__ . '/includes/layout.php';
require_admin();

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    csrf_check();
    if (($_POST['action'] ?? '') === 'approve') {
        $status = in_array($_POST['status'] ?? '', ['approved','rejected','pending'], true) ? $_POST['status'] : 'pending';
        $stmt = db()->prepare('UPDATE time_entries SET admin_status=? WHERE id=?');
        $stmt->execute([$status, (int) ($_POST['id'] ?? 0)]);
        set_flash('success', "Shift marked {$status}.");
    }
    header('Location: shifts.php' . (!empty($_POST['q']) ? '?' . $_POST['q'] : ''));
    exit;
}

// Filters
$start = trim($_GET['start_date'] ?? '');
$end   = trim($_GET['end_date'] ?? '');
$cg    = (int) ($_GET['caregiver_id'] ?? 0);

$where = []; $params = [];
if ($start !== '') { $where[] = 'te.date >= ?'; $params[] = $start; }
if ($end   !== '') { $where[] = 'te.date <= ?'; $params[] = $end; }
if ($cg > 0)       { $where[] = 'te.caregiver_id = ?'; $params[] = $cg; }

$sql = "SELECT te.*, u.name caregiver_name, p.name policyholder_name
          FROM time_entries te
          JOIN users u ON u.id=te.caregiver_id
          JOIN policyholders p ON p.id=te.policyholder_id"
     . ($where ? ' WHERE ' . implode(' AND ', $where) : '')
     . ' ORDER BY te.date DESC, te.id DESC';
$stmt = db()->prepare($sql);
$stmt->execute($params);
$rows = $stmt->fetchAll();

$caregivers = db()->query("SELECT id,name FROM users WHERE role='caregiver' ORDER BY name")->fetchAll();
$qs = http_build_query(array_filter(['start_date'=>$start,'end_date'=>$end,'caregiver_id'=>$cg ?: null]));

$page_title = 'Shifts';
$active = 'shifts';
layout_header();
?>
<div class="panel">
  <div class="panel-head"><h2>Filter shifts</h2></div>
  <form method="get" style="display:flex;gap:14px;flex-wrap:wrap;align-items:flex-end;padding:16px 20px">
    <div style="flex:1;min-width:150px"><label>From</label><input type="date" name="start_date" value="<?= e($start) ?>"></div>
    <div style="flex:1;min-width:150px"><label>To</label><input type="date" name="end_date" value="<?= e($end) ?>"></div>
    <div style="flex:1;min-width:180px">
      <label>Caregiver</label>
      <select name="caregiver_id">
        <option value="0">All caregivers</option>
        <?php foreach ($caregivers as $c): ?>
          <option value="<?= (int) $c['id'] ?>" <?= $cg === (int) $c['id'] ? 'selected' : '' ?>><?= e($c['name']) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <button class="btn primary" style="width:auto;padding:11px 22px">Apply</button>
    <?php if ($qs): ?><a class="btn" href="shifts.php">Clear</a><?php endif; ?>
  </form>
</div>

<div class="panel">
  <div class="panel-head"><h2><?= count($rows) ?> Shift(s)</h2></div>
  <table>
    <thead><tr><th>#</th><th>Date</th><th>Caregiver</th><th>Policyholder</th><th>In/Out</th><th>Hours</th><th>Rate</th><th>Charge</th><th>Status</th><th>Approval</th><th>Action</th></tr></thead>
    <tbody>
    <?php if (!$rows): ?>
      <tr><td colspan="11" class="empty">No shifts match this filter.</td></tr>
    <?php else: foreach ($rows as $r): ?>
      <tr>
        <td class="num">#<?= (int) $r['id'] ?></td>
        <td><?= e($r['date']) ?></td>
        <td><?= e($r['caregiver_name']) ?></td>
        <td><?= e($r['policyholder_name']) ?></td>
        <td><?= e($r['time_in']) ?> – <?= e($r['time_out'] ?: '…') ?></td>
        <td class="num"><?= number_format((float) $r['total_hours'], 2) ?></td>
        <td class="num">$<?= number_format((float) $r['rate'], 2) ?></td>
        <td class="num">$<?= number_format((float) $r['total_charge'], 2) ?></td>
        <td><span class="badge <?= e($r['status']) ?>"><?= e(ucfirst($r['status'])) ?></span></td>
        <td><span class="badge <?= e($r['admin_status']) ?>"><?= e(ucfirst($r['admin_status'])) ?></span></td>
        <td>
          <div class="actions">
            <?php if ($r['admin_status'] !== 'approved'): ?>
            <form class="inline" method="post">
              <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
              <input type="hidden" name="action" value="approve">
              <input type="hidden" name="status" value="approved">
              <input type="hidden" name="id" value="<?= (int) $r['id'] ?>">
              <input type="hidden" name="q" value="<?= e($qs) ?>">
              <button class="btn sm green">Approve</button>
            </form>
            <?php endif; ?>
            <?php if ($r['admin_status'] !== 'rejected'): ?>
            <form class="inline" method="post">
              <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
              <input type="hidden" name="action" value="approve">
              <input type="hidden" name="status" value="rejected">
              <input type="hidden" name="id" value="<?= (int) $r['id'] ?>">
              <input type="hidden" name="q" value="<?= e($qs) ?>">
              <button class="btn sm red">Reject</button>
            </form>
            <?php endif; ?>
          </div>
        </td>
      </tr>
    <?php endforeach; endif; ?>
    </tbody>
  </table>
</div>
<?php layout_footer(); ?>
