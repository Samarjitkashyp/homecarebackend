<?php
// GET api/progress/get_daily.php?time_entry_id=NN
// -> data: { adls: {...}|null, iadls: {...}|null }
require_once __DIR__ . '/../../config/bootstrap.php';
require_method('GET');

$user    = authenticate('caregiver');
$entryId = (int) ($_GET['time_entry_id'] ?? 0);

if ($entryId <= 0) {
    fail('time_entry_id query parameter is required', 422);
}

$adl = db()->prepare('SELECT * FROM daily_progress WHERE time_entry_id = ? LIMIT 1');
$adl->execute([$entryId]);
$adlRow = $adl->fetch() ?: null;

$iadl = db()->prepare('SELECT * FROM iadls WHERE time_entry_id = ? LIMIT 1');
$iadl->execute([$entryId]);
$iadlRow = $iadl->fetch() ?: null;

ok('Progress fetched', [
    'adls'  => $adlRow,
    'iadls' => $iadlRow,
]);
