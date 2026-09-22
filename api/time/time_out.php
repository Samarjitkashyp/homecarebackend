<?php
// POST api/time/time_out.php  { time_out, rate }
// Operates on the caregiver's current ACTIVE entry.
// -> data: { time_out, total_hours, total_charge }
require_once __DIR__ . '/../../config/bootstrap.php';
require_method('POST');

$user = authenticate('caregiver');
$b    = body();

$timeOut = trim((string) input($b, 'time_out', ''));
$rate    = (float) input($b, 'rate', 0);

if ($timeOut === '') {
    fail('time_out is required', 422);
}

// Find the active entry for this caregiver
$stmt = db()->prepare(
    "SELECT * FROM time_entries
      WHERE caregiver_id = ? AND status = 'active'
      ORDER BY id DESC LIMIT 1"
);
$stmt->execute([$user['id']]);
$entry = $stmt->fetch();

if (!$entry) {
    fail('No active shift found to time-out', 404);
}

// Compute hours worked
$start = strtotime($entry['date'] . ' ' . $entry['time_in']);
$end   = strtotime($entry['date'] . ' ' . $timeOut);
if ($end < $start) {           // crossed midnight
    $end += 86400;
}
$totalHours  = round(($end - $start) / 3600, 2);
$totalCharge = round($totalHours * $rate, 2);

$upd = db()->prepare(
    "UPDATE time_entries
        SET time_out = ?, rate = ?, total_hours = ?, total_charge = ?, status = 'completed'
      WHERE id = ?"
);
$upd->execute([$timeOut, $rate, $totalHours, $totalCharge, $entry['id']]);

ok('Time-out recorded', [
    'time_out'     => $timeOut,
    'total_hours'  => $totalHours,
    'total_charge' => $totalCharge,
]);
