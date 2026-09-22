<?php
// GET api/signature/get.php?time_entry_id=NN
// -> data: { id, time_entry_id, caregiver_signature, policyholder_signature, ... }
require_once __DIR__ . '/../../config/bootstrap.php';
require_method('GET');

$user    = authenticate('caregiver');
$entryId = (int) ($_GET['time_entry_id'] ?? 0);

if ($entryId <= 0) {
    fail('time_entry_id query parameter is required', 422);
}

$stmt = db()->prepare('SELECT * FROM signatures WHERE time_entry_id = ? LIMIT 1');
$stmt->execute([$entryId]);
$row = $stmt->fetch();

if (!$row) {
    ok('No signatures found', [
        'time_entry_id'          => $entryId,
        'caregiver_signature'    => '',
        'policyholder_signature' => '',
    ]);
}

ok('Signatures fetched', $row);
