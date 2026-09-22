<?php
// POST api/time/time_in.php  { policyholder_id, date, time_in }
// -> data: { entry_id, time_in }
require_once __DIR__ . '/../../config/bootstrap.php';
require_method('POST');

$user = authenticate('caregiver');
$b    = body();

$policyholderId = (int) input($b, 'policyholder_id', 0);
$date           = trim((string) input($b, 'date', ''));
$timeIn         = trim((string) input($b, 'time_in', ''));

if ($policyholderId <= 0 || $date === '' || $timeIn === '') {
    fail('policyholder_id, date and time_in are required', 422);
}

// Guard 1: Caregiver cannot have another active shift while one is open
$check = db()->prepare(
    "SELECT id FROM time_entries WHERE caregiver_id = ? AND status = 'active' LIMIT 1"
);
$check->execute([$user['id']]);
if ($check->fetch()) {
    fail('You already have an active shift. Please time-out first.', 409);
}

// Guard 2: Policyholder cannot have another active shift with ANY caregiver
$checkPh = db()->prepare(
    "SELECT te.id, u.name AS caregiver_name 
       FROM time_entries te 
       JOIN users u ON u.id = te.caregiver_id 
      WHERE te.policyholder_id = ? AND te.status = 'active' LIMIT 1"
);
$checkPh->execute([$policyholderId]);
$activeShift = $checkPh->fetch();
if ($activeShift) {
    $cgName = $activeShift['caregiver_name'] ?? 'another caregiver';
    fail("This policyholder already has an active shift with {$cgName}. Please wait for it to complete.", 409);
}

// Guard 3: Maximum 2 shifts per day for this policyholder
$checkDaily = db()->prepare(
    "SELECT COUNT(id) AS daily_count FROM time_entries WHERE policyholder_id = ? AND date = ?"
);
$checkDaily->execute([$policyholderId, $date]);
$dailyCount = (int) ($checkDaily->fetch()['daily_count'] ?? 0);
if ($dailyCount >= 2) {
    fail('Maximum limit of 2 shifts per day reached for this policyholder.', 409);
}


$stmt = db()->prepare(
    "INSERT INTO time_entries (caregiver_id, policyholder_id, date, time_in, status)
     VALUES (?, ?, ?, ?, 'active')"
);
$stmt->execute([$user['id'], $policyholderId, $date, $timeIn]);
$entryId = (int) db()->lastInsertId();

ok('Time-in recorded', [
    'entry_id' => $entryId,
    'time_in'  => $timeIn,
]);
