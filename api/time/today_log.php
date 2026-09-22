<?php
// GET api/time/today_log.php?date=YYYY-MM-DD
// -> data: { has_entry: bool, time_entry: {...} }
require_once __DIR__ . '/../../config/bootstrap.php';
require_method('GET');

$user = authenticate('caregiver');
$date = trim((string) ($_GET['date'] ?? ''));

if ($date === '') {
    fail('date query parameter is required', 422);
}

$stmt = db()->prepare(
    'SELECT * FROM time_entries
      WHERE caregiver_id = ? AND date = ?
      ORDER BY id DESC LIMIT 1'
);
$stmt->execute([$user['id'], $date]);
$entry = $stmt->fetch();

if (!$entry) {
    ok('No entry for this date', ['has_entry' => false, 'time_entry' => null]);
}

ok('Entry found', [
    'has_entry'  => true,
    'time_entry' => [
        'id'              => (int) $entry['id'],
        'caregiver_id'    => (int) $entry['caregiver_id'],
        'policyholder_id' => (int) $entry['policyholder_id'],
        'date'            => $entry['date'],
        'time_in'         => $entry['time_in'],
        'time_out'        => $entry['time_out'],
        'total_hours'     => (float) $entry['total_hours'],
        'rate'            => (float) $entry['rate'],
        'total_charge'    => (float) $entry['total_charge'],
        'status'          => $entry['status'],
    ],
]);
