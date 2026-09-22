<?php
// api/admin/policyholders.php
//   GET    -> list policyholders (with shift totals)
//   POST   -> create { name, policy_number, phone, address }
//   PUT    -> update { id, name, policy_number, phone, address }
//   DELETE -> { id }
require_once __DIR__ . '/../../config/bootstrap.php';

authenticate('admin');
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

switch ($method) {

    case 'GET': {
        $rows = db()->query(
            "SELECT p.id, p.name, p.policy_number, p.phone, p.address,
                    COUNT(te.id) AS total_shifts,
                    SUM(CASE WHEN te.status='completed' THEN 1 ELSE 0 END) AS completed_shifts
               FROM policyholders p
               LEFT JOIN time_entries te ON te.policyholder_id = p.id
           GROUP BY p.id
           ORDER BY p.name ASC"
        )->fetchAll();

        $data = array_map(fn($r) => [
            'id'               => (int) $r['id'],
            'name'             => $r['name'],
            'policy_number'    => $r['policy_number'] ?? '',
            'phone'            => $r['phone'] ?? '',
            'address'          => $r['address'],
            'total_shifts'     => (int) $r['total_shifts'],
            'completed_shifts' => (int) $r['completed_shifts'],
        ], $rows);

        ok('Policyholders fetched', $data);
    }

    case 'POST': {
        $b     = body();
        $name  = trim((string) input($b, 'name', ''));
        $policy = trim((string) input($b, 'policy_number', ''));
        $phone = trim((string) input($b, 'phone', ''));
        $addr  = trim((string) input($b, 'address', ''));

        if ($name === '') { fail('name is required', 422); }

        $stmt = db()->prepare(
            'INSERT INTO policyholders (name, policy_number, phone, address) VALUES (?, ?, ?, ?)'
        );
        $stmt->execute([$name, $policy, $phone, $addr]);

        ok('Policyholder created successfully', ['id' => (int) db()->lastInsertId()]);
    }

    case 'PUT': {
        $b  = body();
        $id = (int) input($b, 'id', 0);
        if ($id <= 0) { fail('id is required', 422); }

        $stmt = db()->prepare(
            'UPDATE policyholders SET name=?, policy_number=?, phone=?, address=? WHERE id=?'
        );
        $stmt->execute([
            trim((string) input($b, 'name', '')),
            trim((string) input($b, 'policy_number', '')),
            trim((string) input($b, 'phone', '')),
            trim((string) input($b, 'address', '')),
            $id,
        ]);

        ok('Policyholder updated successfully');
    }

    case 'DELETE': {
        $b  = body();
        $id = (int) input($b, 'id', 0);
        if ($id <= 0) { fail('id is required', 422); }

        $stmt = db()->prepare('DELETE FROM policyholders WHERE id=?');
        $stmt->execute([$id]);

        ok('Policyholder deleted successfully');
    }

    default:
        fail('Method not allowed', 405);
}
