<?php
// POST api/admin/login.php  { email, password }
// -> data: { user: {id,name,email,role}, token }   (role must be admin)
require_once __DIR__ . '/../../config/bootstrap.php';
require_method('POST');

$b        = body();
$email    = trim((string) input($b, 'email', ''));
$password = (string) input($b, 'password', '');

if ($email === '' || $password === '') {
    fail('Email and password are required', 422);
}

$stmt = db()->prepare('SELECT * FROM users WHERE email = ? LIMIT 1');
$stmt->execute([$email]);
$user = $stmt->fetch();

if (!$user || !verify_user_password($user, $password)) {
    fail('Invalid email or password', 401);
}

if ($user['role'] !== 'admin') {
    fail('Access denied. Not an admin account.', 403);
}

$token = issue_token((int) $user['id'], 'admin');

ok('Admin login successful', [
    'user' => [
        'id'    => (int) $user['id'],
        'name'  => $user['name'],
        'email' => $user['email'],
        'role'  => $user['role'],
    ],
    'token' => $token,
]);
