<?php
require_once __DIR__ . '/includes/layout.php';
require_admin();

$totalCaregivers    = (int) db()->query("SELECT COUNT(*) c FROM users WHERE role='caregiver'")->fetch()['c'];
$totalPolicyholders = (int) db()->query("SELECT COUNT(*) c FROM policyholders")->fetch()['c'];
$totalShifts        = (int) db()->query("SELECT COUNT(*) c FROM time_entries")->fetch()['c'];
$totalRevenue       = (float) db()->query("SELECT COALESCE(SUM(total_charge),0) s FROM time_entries")->fetch()['s'];
$pendingShifts      = (int) db()->query("SELECT COUNT(*) c FROM time_entries WHERE admin_status='pending'")->fetch()['c'];

$recent = db()->query(
    "SELECT te.id, te.date, te.total_hours, te.total_charge, te.status, te.admin_status,
            u.name AS caregiver, p.name AS policyholder
       FROM time_entries te
       JOIN users u ON u.id=te.caregiver_id
       JOIN policyholders p ON p.id=te.policyholder_id
   ORDER BY te.id DESC LIMIT 8"
)->fetchAll();

$page_title = 'Dashboard';
$active = 'dashboard';
layout_header();
?>
<div class="stat-grid">
  <div class="stat brand"><div class="label">Total Caregivers</div><div class="value"><?= $totalCaregivers ?></div></div>
  <div class="stat"><div class="label">Policyholders</div><div class="value"><?= $totalPolicyholders ?></div></div>
  <div class="stat"><div class="label">Total Shifts</div><div class="value"><?= $totalShifts ?></div></div>
  <div class="stat green"><div class="label">Total Revenue</div><div class="value">$<?= number_format($totalRevenue, 2) ?></div></div>
  <div class="stat amber"><div class="label">Pending Approvals</div><div class="value"><?= $pendingShifts ?></div></div>
</div>

<div class="panel">
  <div class="panel-head"><h2>Recent Shifts</h2><a class="btn sm" href="shifts.php">View all</a></div>
  <table>
    <thead><tr><th>#</th><th>Date</th><th>Caregiver</th><th>Policyholder</th><th>Hours</th><th>Charge</th><th>Status</th><th>Approval</th></tr></thead>
    <tbody>
      <?php if (!$recent): ?>
        <tr><td colspan="8" class="empty">No shifts recorded yet.</td></tr>
      <?php else: foreach ($recent as $r): ?>
        <tr>
          <td class="num">#<?= (int) $r['id'] ?></td>
          <td><?= e($r['date']) ?></td>
          <td><?= e($r['caregiver']) ?></td>
          <td><?= e($r['policyholder']) ?></td>
          <td class="num"><?= number_format((float) $r['total_hours'], 2) ?></td>
          <td class="num">$<?= number_format((float) $r['total_charge'], 2) ?></td>
          <td><span class="badge <?= e($r['status']) ?>"><?= e(ucfirst($r['status'])) ?></span></td>
          <td><span class="badge <?= e($r['admin_status']) ?>"><?= e(ucfirst($r['admin_status'])) ?></span></td>
        </tr>
      <?php endforeach; endif; ?>
    </tbody>
  </table>
</div>
<?php layout_footer(); ?>
