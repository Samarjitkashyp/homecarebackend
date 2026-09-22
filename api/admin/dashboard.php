<?php
// GET api/admin/dashboard.php
// -> data: { stats:{...}, recent_shifts:[...], weekly_data:[...] }
require_once __DIR__ . '/../../config/bootstrap.php';
require_method('GET');

authenticate('admin');

$totalCaregivers    = (int) db()->query("SELECT COUNT(*) c FROM users WHERE role='caregiver'")->fetch()['c'];
$totalPolicyholders = (int) db()->query("SELECT COUNT(*) c FROM policyholders")->fetch()['c'];
$totalShifts        = (int) db()->query("SELECT COUNT(*) c FROM time_entries")->fetch()['c'];
$totalRevenue       = (float) db()->query("SELECT COALESCE(SUM(total_charge),0) s FROM time_entries")->fetch()['s'];
$pendingShifts      = (int) db()->query("SELECT COUNT(*) c FROM time_entries WHERE admin_status='pending'")->fetch()['c'];

// Recent shifts (last 10)
$recent = db()->query(
    "SELECT te.id, te.date, te.time_in, te.time_out, te.total_hours, te.rate,
            te.total_charge, te.status, te.admin_status,
            u.name AS caregiver_name, p.name AS policyholder_name
       FROM time_entries te
       JOIN users u        ON u.id = te.caregiver_id
       JOIN policyholders p ON p.id = te.policyholder_id
   ORDER BY te.id DESC LIMIT 10"
)->fetchAll();

// Weekly revenue (last 7 days)
$weekly = db()->query(
    "SELECT te.date AS day,
            COALESCE(SUM(te.total_charge),0) AS revenue,
            COUNT(*) AS shifts
       FROM time_entries te
      WHERE te.date >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)
   GROUP BY te.date
   ORDER BY te.date ASC"
)->fetchAll();

ok('Dashboard loaded', [
    'stats' => [
        'total_caregivers'    => $totalCaregivers,
        'total_policyholders' => $totalPolicyholders,
        'total_shifts'        => $totalShifts,
        'total_revenue'       => $totalRevenue,
        'pending_shifts'      => $pendingShifts,
    ],
    'recent_shifts' => $recent,
    'weekly_data'   => $weekly,
]);
