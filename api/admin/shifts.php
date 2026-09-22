<?php
// GET api/admin/shifts.php?start_date&end_date&caregiver_id
// -> data: [ {id,caregiver_id,policyholder_id,date,time_in,time_out,
//             total_hours,rate,total_charge,status,caregiver_name,
//             policyholder_name,admin_status} ]
require_once __DIR__ . '/../../config/bootstrap.php';
require_method('GET');

authenticate('admin');

$where  = [];
$params = [];

if (!empty($_GET['start_date']))   { $where[] = 'te.date >= ?';        $params[] = $_GET['start_date']; }
if (!empty($_GET['end_date']))     { $where[] = 'te.date <= ?';        $params[] = $_GET['end_date']; }
if (!empty($_GET['caregiver_id'])) { $where[] = 'te.caregiver_id = ?'; $params[] = (int) $_GET['caregiver_id']; }

$sql = "SELECT te.id, te.caregiver_id, te.policyholder_id, te.date,
               te.time_in, te.time_out, te.total_hours, te.rate,
               te.total_charge, te.status, te.admin_status,
               u.name AS caregiver_name, p.name AS policyholder_name
          FROM time_entries te
          JOIN users u        ON u.id = te.caregiver_id
          JOIN policyholders p ON p.id = te.policyholder_id";
if ($where) {
    $sql .= ' WHERE ' . implode(' AND ', $where);
}
$sql .= ' ORDER BY te.date DESC, te.id DESC';

$stmt = db()->prepare($sql);
$stmt->execute($params);
$rows = $stmt->fetchAll();

$data = array_map(fn($r) => [
    'id'                => (int) $r['id'],
    'caregiver_id'      => (int) $r['caregiver_id'],
    'policyholder_id'   => (int) $r['policyholder_id'],
    'date'              => $r['date'],
    'time_in'           => $r['time_in'],
    'time_out'          => $r['time_out'],
    'total_hours'       => (float) $r['total_hours'],
    'rate'              => (float) $r['rate'],
    'total_charge'      => (float) $r['total_charge'],
    'status'            => $r['status'],
    'admin_status'      => $r['admin_status'],
    'caregiver_name'    => $r['caregiver_name'],
    'policyholder_name' => $r['policyholder_name'],
], $rows);

ok('Shifts fetched', $data);
