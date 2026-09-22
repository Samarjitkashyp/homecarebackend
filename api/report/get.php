<?php
// GET api/report/get.php?entry_id=NN
require_once __DIR__ . '/../../config/bootstrap.php';
require_once __DIR__ . '/_build.php';
require_method('GET');

$user    = authenticate('caregiver');
$entryId = (int) ($_GET['entry_id'] ?? $_GET['time_entry_id'] ?? 0);

if ($entryId <= 0) {
    fail('entry_id query parameter is required', 422);
}

$report = build_report($entryId, (int) $user['id']);
if ($report === null) {
    fail('Report not found', 404);
}

ok('Report fetched', $report);
