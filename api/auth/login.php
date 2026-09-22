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
