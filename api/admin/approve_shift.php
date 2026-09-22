<?php
// POST api/admin/approve_shift.php  { shift_id, status }
//   status: approved | rejected | pending
require_once __DIR__ . '/../../config/bootstrap.php';
require_method('POST');

authenticate('admin');
$b = body();

$shiftId = (int) input($b, 'shift_id', 0);
$status  = strtolower(trim((string) input($b, 'status', '')));

if ($shiftId <= 0) {
    fail('shift_id is required', 422);
}
if (!in_array($status, ['approved', 'rejected', 'pending'], true)) {
    fail('status must be approved, rejected or pending', 422);
}

$stmt = db()->prepare('UPDATE time_entries SET admin_status = ? WHERE id = ?');
$stmt->execute([$status, $shiftId]);

if ($stmt->rowCount() === 0) {
    // still OK if value unchanged, but flag missing rows
    $exists = db()->prepare('SELECT id FROM time_entries WHERE id = ?');
    $exists->execute([$shiftId]);
    if (!$exists->fetch()) {
        fail('Shift not found', 404);
    }
}

ok("Shift {$status} successfully");
