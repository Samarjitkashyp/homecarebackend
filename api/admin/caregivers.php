<?php
// api/admin/caregivers.php
//   GET    -> list caregivers (with shift + revenue totals)
//   POST   -> create { name, email, password, phone, address }
//   PUT    -> update { id, name, email, phone, address, password? }
//   DELETE -> { id }
require_once __DIR__ . '/../../config/bootstrap.php';

authenticate('admin');
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

switch ($method) {

    case 'GET': {
        $rows = db()->query(
            "SELECT u.id, u.name, u.email, u.phone, u.address,
                    COUNT(te.id) AS total_shifts,
                    COALESCE(SUM(te.total_charge),0) AS total_revenue
               FROM users u
               LEFT JOIN time_entries te ON te.caregiver_id = u.id
              WHERE u.role = 'caregiver'
           GROUP BY u.id
           ORDER BY u.name ASC"
        )->fetchAll();

        $data = array_map(fn($r) => [
            'id'            => (int) $r['id'],
            'name'          => $r['name'],
            'email'         => $r['email'],
            'phone'         => $r['phone'] ?? '',
            'address'       => $r['address'],
            'total_shifts'  => (int) $r['total_shifts'],
            'total_revenue' => (float) $r['total_revenue'],
        ], $rows);

        ok('Caregivers fetched', $data);
    }

    case 'POST': {
        $b = body();
        $name  = trim((string) input($b, 'name', ''));
        $email = trim((string) input($b, 'email', ''));
        $pass  = (string) input($b, 'password', '');
        $phone = trim((string) input($b, 'phone', ''));
        $addr  = trim((string) input($b, 'address', ''));

        if ($name === '' || $email === '' || $pass === '') {
            fail('name, email and password are required', 422);
        }

        $dup = db()->prepare('SELECT id FROM users WHERE email = ?');
        $dup->execute([$email]);
        if ($dup->fetch()) {
            fail('A user with this email already exists', 409);
        }

        $stmt = db()->prepare(
            "INSERT INTO users (name, email, password, phone, address, role)
             VALUES (?, ?, ?, ?, ?, 'caregiver')"
        );
        $stmt->execute([$name, $email, password_hash($pass, PASSWORD_BCRYPT), $phone, $addr]);

        ok('Caregiver created successfully', ['id' => (int) db()->lastInsertId()]);
    }

    case 'PUT': {
        $b  = body();
        $id = (int) input($b, 'id', 0);
        if ($id <= 0) { fail('id is required', 422); }

        $name  = trim((string) input($b, 'name', ''));
        $email = trim((string) input($b, 'email', ''));
        $phone = trim((string) input($b, 'phone', ''));
        $addr  = trim((string) input($b, 'address', ''));
        $pass  = (string) input($b, 'password', '');

        if ($pass !== '') {
            $stmt = db()->prepare(
                "UPDATE users SET name=?, email=?, phone=?, address=?, password=?
                  WHERE id=? AND role='caregiver'"
            );
            $stmt->execute([$name, $email, $phone, $addr, password_hash($pass, PASSWORD_BCRYPT), $id]);
        } else {
            $stmt = db()->prepare(
                "UPDATE users SET name=?, email=?, phone=?, address=?
                  WHERE id=? AND role='caregiver'"
            );
            $stmt->execute([$name, $email, $phone, $addr, $id]);
        }

        ok('Caregiver updated successfully');
    }

    case 'DELETE': {
        $b  = body();
        $id = (int) input($b, 'id', 0);
        if ($id <= 0) { fail('id is required', 422); }

        $stmt = db()->prepare("DELETE FROM users WHERE id=? AND role='caregiver'");
        $stmt->execute([$id]);

        ok('Caregiver deleted successfully');
    }

    default:
        fail('Method not allowed', 405);
}
