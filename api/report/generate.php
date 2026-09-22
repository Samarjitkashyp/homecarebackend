<?php
// POST api/report/generate.php  { time_entry_id }
require_once __DIR__ . '/../../config/bootstrap.php';
require_once __DIR__ . '/_build.php';
require_method('POST');

$user    = authenticate('caregiver');
$b       = body();
$entryId = (int) input($b, 'time_entry_id', 0);

if ($entryId <= 0) {
    fail('time_entry_id is required', 422);
}

$report = build_report($entryId, (int) $user['id']);
if ($report === null) {
    fail('Time entry not found', 404);
}

ok('Report generated', $report);
