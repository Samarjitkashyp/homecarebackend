<?php
// POST api/progress/save_daily.php
// Body (flat): { time_entry_id, date, <adls...>, <iadls...> }
//   ADLs  (string I/S/A/H): bathing, mobility, bed_chair, continence,
//                           eating, toileting, dressing, medication
//   IADLs (0/1): housekeeping, meal_prep, shopping, transportation,
//                managing_medicines, laundry
require_once __DIR__ . '/../../config/bootstrap.php';
require_method('POST');

$user = authenticate('caregiver');
$b    = body();

$entryId = (int) input($b, 'time_entry_id', 0);
$date    = trim((string) input($b, 'date', ''));

if ($entryId <= 0) {
    fail('time_entry_id is required', 422);
}

// Verify the entry belongs to this caregiver
$own = db()->prepare('SELECT id FROM time_entries WHERE id = ? AND caregiver_id = ?');
$own->execute([$entryId, $user['id']]);
if (!$own->fetch()) {
    fail('Time entry not found', 404);
}

$adlKeys  = ['bathing','mobility','bed_chair','continence','eating','toileting','dressing','medication'];
$iadlKeys = ['housekeeping','meal_prep','shopping','transportation','managing_medicines','laundry'];

$hasAdl  = false;
$hasIadl = false;
foreach ($adlKeys  as $k) { if (array_key_exists($k, $b)) { $hasAdl  = true; break; } }
foreach ($iadlKeys as $k) { if (array_key_exists($k, $b)) { $hasIadl = true; break; } }

// ---- Upsert ADLs ----
if ($hasAdl) {
    $vals = [$entryId, $date ?: null];
    foreach ($adlKeys as $k) { $vals[] = ($b[$k] ?? null); }

    $sql = 'INSERT INTO daily_progress
              (time_entry_id, date, ' . implode(', ', $adlKeys) . ')
            VALUES (?, ?, ' . rtrim(str_repeat('?, ', count($adlKeys)), ', ') . ')
            ON DUPLICATE KEY UPDATE date = VALUES(date), ' .
            implode(', ', array_map(fn($k) => "$k = VALUES($k)", $adlKeys));
    db()->prepare($sql)->execute($vals);
}

// ---- Upsert IADLs ----
if ($hasIadl) {
    $vals = [$entryId, $date ?: null];
    foreach ($iadlKeys as $k) { $vals[] = (int) (($b[$k] ?? 0) ? 1 : 0); }

    $sql = 'INSERT INTO iadls
              (time_entry_id, date, ' . implode(', ', $iadlKeys) . ')
            VALUES (?, ?, ' . rtrim(str_repeat('?, ', count($iadlKeys)), ', ') . ')
            ON DUPLICATE KEY UPDATE date = VALUES(date), ' .
            implode(', ', array_map(fn($k) => "$k = VALUES($k)", $iadlKeys));
    db()->prepare($sql)->execute($vals);
}

ok('Progress saved successfully', ['time_entry_id' => $entryId]);
