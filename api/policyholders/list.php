<?php
// GET api/policyholders/list.php?date=YYYY-MM-DD   (caregiver)
// -> data: [ {id,name,policy_number,phone,address,total_shifts,completed_shifts,today_shifts,completed_today,has_active_shift,active_caregiver_name} ]
require_once __DIR__ . '/../../config/bootstrap.php';
require_method('GET');

authenticate('caregiver');

$selectedDate = trim((string) ($_GET['date'] ?? date('Y-m-d')));
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $selectedDate)) {
    $selectedDate = date('Y-m-d');
}

$sql = "SELECT p.id, p.name, p.policy_number, p.phone, p.address,
               COUNT(te.id) AS total_shifts,
               SUM(CASE WHEN te.status = 'completed' THEN 1 ELSE 0 END) AS completed_shifts,
               SUM(CASE WHEN te.date = ? THEN 1 ELSE 0 END) AS today_shifts,
               SUM(CASE WHEN te.date = ? AND te.status = 'completed' THEN 1 ELSE 0 END) AS completed_today,
               SUM(CASE WHEN te.status = 'active' THEN 1 ELSE 0 END) AS active_shifts_count,
               (SELECT u.name 
                  FROM time_entries te_act 
                  JOIN users u ON u.id = te_act.caregiver_id 
                 WHERE te_act.policyholder_id = p.id AND te_act.status = 'active' 
                 ORDER BY te_act.id DESC LIMIT 1) AS active_caregiver_name
          FROM policyholders p
          LEFT JOIN time_entries te ON te.policyholder_id = p.id
      GROUP BY p.id
      ORDER BY p.name ASC";

$stmt = db()->prepare($sql);
$stmt->execute([$selectedDate, $selectedDate]);
$rows = $stmt->fetchAll();

$data = array_map(fn($r) => [
    'id'                    => (int) $r['id'],
    'name'                  => $r['name'],
    'policy_number'         => $r['policy_number'] ?? '',
    'phone'                 => $r['phone'] ?? '',
    'address'               => $r['address'],
    'total_shifts'          => (int) $r['total_shifts'],
    'completed_shifts'      => (int) $r['completed_shifts'],
    'today_shifts'          => (int) ($r['today_shifts'] ?? 0),
    'completed_today'       => (int) ($r['completed_today'] ?? 0),
    'has_active_shift'      => ((int) ($r['active_shifts_count'] ?? 0)) > 0,
    'active_caregiver_name' => $r['active_caregiver_name'],
], $rows);

ok('Policyholders fetched', $data);

