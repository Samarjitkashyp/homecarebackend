<?php
// POST api/auth/logout.php  (Bearer token) -> deletes the token
require_once __DIR__ . '/../../config/bootstrap.php';
require_method('POST');

$token = bearer_token();
if ($token) {
    $stmt = db()->prepare('DELETE FROM auth_tokens WHERE token = ?');
    $stmt->execute([$token]);
}

ok('Logged out successfully');
