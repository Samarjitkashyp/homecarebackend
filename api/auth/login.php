<?php
// POST api/auth/login.php  { email, password }
// -> data: { user: {id,name,email,phone,address,role}, token }
require_once __DIR__ . '/../../config/bootstrap.php';
require_method('POST');

$b        = body();
$email    = trim((string) input($b, 'email', ''));
$password = (string) input($b, 'password', '');

if ($email === '' || $password === '') {
    fail('Email and password are required', 422);
}

$ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
$window = date('Y-m-d H:i:s', strtotime('-15 minutes'));
$attempts = db()->prepare(
    'SELECT COUNT(*) FROM login_attempts WHERE ip = ? AND attempted_at > ?'
);
$attempts->execute([$ip, $window]);
if ((int) $attempts->fetchColumn() >= 5) {
    fail('Too many login attempts. Try again in 15 minutes.', 429);
}

db()->prepare('INSERT INTO login_attempts (ip, email) VALUES (?, ?)')->execute([$ip, $email]);

$stmt = db()->prepare('SELECT * FROM users WHERE email = ? LIMIT 1');
$stmt->execute([$email]);
$user = $stmt->fetch();

if (!$user || !password_verify($password, $user['password'])) {
    fail('Invalid email or password', 401);
}

$token = issue_token((int) $user['id'], $user['role']);

ok('Login successful', [
    'user' => [
        'id'      => (int) $user['id'],
        'name'    => $user['name'],
        'email'   => $user['email'],
        'phone'   => $user['phone'],
        'address' => $user['address'],
        'role'    => $user['role'],
    ],
    'token' => $token,
]);
