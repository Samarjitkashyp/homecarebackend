<?php
// Shared report builder used by generate.php and get.php
// Returns the composite report structure the Flutter ReportModel expects.

function build_report(int $entryId, int $caregiverId): ?array
{
    $stmt = db()->prepare(
        'SELECT te.*,
                u.name  AS caregiver_name,  u.email AS caregiver_email,
                u.phone AS caregiver_phone, u.address AS caregiver_address,
                p.name  AS policyholder_name, p.policy_number,
                p.phone AS policyholder_phone, p.address AS policyholder_address
           FROM time_entries te
           JOIN users u        ON u.id = te.caregiver_id
           JOIN policyholders p ON p.id = te.policyholder_id
          WHERE te.id = ? AND te.caregiver_id = ?
          LIMIT 1'
    );
    $stmt->execute([$entryId, $caregiverId]);
    $e = $stmt->fetch();
    if (!$e) {
        return null;
    }

    $adl = db()->prepare('SELECT * FROM daily_progress WHERE time_entry_id = ? LIMIT 1');
    $adl->execute([$entryId]);
    $adls = $adl->fetch() ?: null;

    $iadl = db()->prepare('SELECT * FROM iadls WHERE time_entry_id = ? LIMIT 1');
    $iadl->execute([$entryId]);
    $iadls = $iadl->fetch() ?: null;

    $sig = db()->prepare('SELECT * FROM signatures WHERE time_entry_id = ? LIMIT 1');
    $sig->execute([$entryId]);
    $signatures = $sig->fetch() ?: null;

    return [
        'policyholder' => [
            'name'          => $e['policyholder_name'],
            'policy_number' => $e['policy_number'],
            'phone'         => $e['policyholder_phone'],
            'address'       => $e['policyholder_address'],
        ],
        'caregiver' => [
            'name'    => $e['caregiver_name'],
            'email'   => $e['caregiver_email'],
            'phone'   => $e['caregiver_phone'],
            'address' => $e['caregiver_address'],
        ],
        'time_entry' => [
            'id'           => (int) $e['id'],
            'date'         => $e['date'],
            'time_in'      => $e['time_in'],
            'time_out'     => $e['time_out'],
            'total_hours'  => (float) $e['total_hours'],
            'rate'         => (float) $e['rate'],
            'total_charge' => (float) $e['total_charge'],
            'status'       => $e['status'],
        ],
        'adls'          => $adls,
        'iadls'         => $iadls,
        'signatures'    => $signatures,
        'certification' => 'I certify that the above services were rendered as documented.',
    ];
}
