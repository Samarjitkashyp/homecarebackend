<?php
// admin/includes/auth.php - session auth for the web admin panel
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/password.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function admin_login(string $email, string $password): bool
{
    $stmt = db()->prepare("SELECT * FROM users WHERE email = ? AND role = 'admin' LIMIT 1");
    $stmt->execute([$email]);
    $u = $stmt->fetch();

    if ($u && verify_user_password($u, $password)) {
        session_regenerate_id(true);
        $_SESSION['admin'] = [
            'id'    => (int) $u['id'],
            'name'  => $u['name'],
            'email' => $u['email'],
        ];
        return true;
    }
    return false;
}

function admin_logout(): void
{
    $_SESSION = [];
    session_destroy();
}

function current_admin(): ?array
{
    return $_SESSION['admin'] ?? null;
}

function require_admin(): array
{
    $a = current_admin();
    if (!$a) {
        header('Location: index.php');
        exit;
    }
    return $a;
}

function e(?string $s): string
{
    return htmlspecialchars((string) $s, ENT_QUOTES, 'UTF-8');
}

// CSRF
function csrf_token(): string
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(16));
    }
    return $_SESSION['csrf'];
}

function csrf_check(): void
{
    $t = $_POST['csrf'] ?? '';
    if (!hash_equals($_SESSION['csrf'] ?? '', $t)) {
        http_response_code(419);
        exit('Invalid CSRF token. Please refresh and try again.');
    }
}
