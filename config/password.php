<?php
// config/password.php - login password check shared by the app API and the web admin panel.

if (!function_exists('verify_user_password')) {
    /**
     * Checks a login password against users.password.
     * - A normal bcrypt / argon2 hash (what the app writes) is checked with password_verify().
     * - A 32-character MD5 hash (what phpMyAdmin writes when "MD5" is chosen in its Function column) also works, and is
     *   replaced by a bcrypt hash on the first good login, so the database ends up safe without anyone doing anything.
     * Plain text is never accepted.
     */
    function verify_user_password(array $user, string $plain): bool
    {
        $stored = (string) ($user['password'] ?? '');
        if ($stored === '') {
            return false;
        }
        if (preg_match('/^\$(2[aby]|argon2)/', $stored)) {
            return password_verify($plain, $stored);
        }
        if (preg_match('/^[a-f0-9]{32}$/i', $stored) && hash_equals(strtolower($stored), md5($plain))) {
            try {
                db()->prepare('UPDATE users SET password = ? WHERE id = ?')
                    ->execute([password_hash($plain, PASSWORD_BCRYPT), (int) $user['id']]);
            } catch (Throwable $e) {
                // the login still works; the upgrade is tried again next time
            }
            return true;
        }
        return false;
    }
}
