<?php
// POST api/signature/save.php
// { time_entry_id, caregiver_signature, policyholder_signature }
require_once __DIR__ . '/../../config/bootstrap.php';
require_method('POST');

$user = authenticate('caregiver');
$b    = body();

$entryId = (int) input($b, 'time_entry_id', 0);
$careSig = (string) input($b, 'caregiver_signature', '');
$phSig   = (string) input($b, 'policyholder_signature', '');

if ($entryId <= 0) {
    fail('time_entry_id is required', 422);
}

$own = db()->prepare('SELECT id FROM time_entries WHERE id = ? AND caregiver_id = ?');
$own->execute([$entryId, $user['id']]);
if (!$own->fetch()) {
    fail('Time entry not found', 404);
}

$now = date('Y-m-d H:i:s');
$sql = 'INSERT INTO signatures
          (time_entry_id, caregiver_signature, policyholder_signature,
           caregiver_signed_date, policyholder_signed_date)
        VALUES (?, ?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE
           caregiver_signature      = VALUES(caregiver_signature),
           policyholder_signature   = VALUES(policyholder_signature),
           caregiver_signed_date    = VALUES(caregiver_signed_date),
           policyholder_signed_date = VALUES(policyholder_signed_date)';
db()->prepare($sql)->execute([$entryId, $careSig, $phSig, $now, $now]);

ok('Signatures saved successfully', ['time_entry_id' => $entryId]);
