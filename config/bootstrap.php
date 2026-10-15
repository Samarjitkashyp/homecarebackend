<?php
// config/bootstrap.php
// Shared bootstrap for every API endpoint: CORS, JSON helpers, auth.

require_once __DIR__ . '/database.php';
require_once __DIR__ . '/password.php';

// ------------------------------------------------------------
// CORS (allow the Flutter app / web to call us)
// ------------------------------------------------------------
$allowed_origins = [
    'https://homecare.digihiveassam.com',
    'http://localhost',
    'http://10.0.2.2',
];
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
if (in_array($origin, $allowed_origins, true)) {
    header('Access-Control-Allow-Origin: ' . $origin);
} else {
    header('Access-Control-Allow-Origin: https://homecare.digihiveassam.com');
}
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Content-Type: application/json; charset=utf-8');
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');
header('Referrer-Policy: strict-origin-when-cross-origin');

// Pre-flight
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// ------------------------------------------------------------
// Response helpers  ->  { status, message, data }
// ------------------------------------------------------------
function respond(bool $status, string $message, $data = null, int $code = 200): void
{
    http_response_code($code);
    echo json_encode([
        'status'  => $status,
        'message' => $message,
        'data'    => $data,
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

function ok(string $message, $data = null): void   { respond(true,  $message, $data, 200); }
function fail(string $message, int $code = 400): void { respond(false, $message, null, $code); }

// ------------------------------------------------------------
// Read JSON (or form) body
// ------------------------------------------------------------
function body(): array
{
    $raw = file_get_contents('php://input');
    if ($raw) {
        $json = json_decode($raw, true);
        if (is_array($json)) {
            return $json;
        }
    }
    // fall back to form-encoded
    return $_POST ?: [];
}

function input(array $src, string $key, $default = null)
{
    return array_key_exists($key, $src) ? $src[$key] : $default;
}

function require_method(string ...$methods): void
{
    $m = $_SERVER['REQUEST_METHOD'] ?? 'GET';
    if (!in_array($m, $methods, true)) {
        fail('Method not allowed', 405);
    }
}

// ------------------------------------------------------------
// Bearer token auth
// ------------------------------------------------------------
function bearer_token(): ?string
{
    $headers = [];
    if (function_exists('getallheaders')) {
        foreach (getallheaders() as $k => $v) {
            $headers[strtolower($k)] = $v;
        }
    }
    $auth = $headers['authorization']
        ?? $_SERVER['HTTP_AUTHORIZATION']
        ?? $_SERVER['REDIRECT_HTTP_AUTHORIZATION']
        ?? '';

    if (preg_match('/Bearer\s+(.+)/i', $auth, $m)) {
        return trim($m[1]);
    }
    return null;
}

/**
 * Validate the token and (optionally) require a role.
 * Returns the authenticated user row (users.*) or sends 401.
 */
function authenticate(?string $requiredRole = null): array
{
    $token = bearer_token();
    if (!$token) {
        fail('Unauthorized: missing token', 401);
    }

    $stmt = db()->prepare(
        'SELECT u.*, t.role AS token_role, t.expires_at
           FROM auth_tokens t
           JOIN users u ON u.id = t.user_id
          WHERE t.token = ?
          LIMIT 1'
    );
    $stmt->execute([$token]);
    $row = $stmt->fetch();

    if (!$row) {
        fail('Unauthorized: invalid token', 401);
    }
    if (strtotime($row['expires_at']) < time()) {
        fail('Unauthorized: token expired', 401);
    }
    if ($requiredRole !== null && $row['role'] !== $requiredRole) {
        fail('Forbidden: ' . $requiredRole . ' access required', 403);
    }

    return $row;
}

// ------------------------------------------------------------
// Issue a fresh token for a user
// ------------------------------------------------------------
function issue_token(int $userId, string $role, int $days = 30): string
{
    $token   = bin2hex(random_bytes(32));
    $expires = date('Y-m-d H:i:s', strtotime("+{$days} days"));
    $stmt = db()->prepare(
        'INSERT INTO auth_tokens (user_id, token, role, expires_at) VALUES (?, ?, ?, ?)'
    );
    $stmt->execute([$userId, $token, $role, $expires]);
    return $token;
}
