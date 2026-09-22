<?php
// setup.php - one-time installer. Open http://localhost/homecare/setup.php
// Creates the database + tables and seeds real bcrypt passwords.
// Delete this file once setup is done.

require_once __DIR__ . '/config/database.php';

header('Content-Type: text/plain; charset=utf-8');

// 1) Connect WITHOUT selecting a db first, so we can create it.
try {
    $root = new PDO('mysql:host=' . DB_HOST . ';charset=' . DB_CHARSET, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);
} catch (PDOException $e) {
    exit('MySQL connection failed: ' . $e->getMessage() . "\nIs MySQL running in XAMPP?");
}

echo "== Absolute Choice Homecare :: Setup ==\n\n";

// 2) Run schema (strip the fake-hash seed block - we seed below with real hashes)
$sql = file_get_contents(__DIR__ . '/database.sql');

// Remove the INSERT ... users seed with placeholder hashes (we redo it properly)
$sql = preg_replace('/INSERT INTO users.*?email = email;/s', '', $sql);

try {
    $root->exec($sql);
    echo "[OK] Database + tables created.\n";
} catch (PDOException $e) {
    echo "[WARN] Schema run: " . $e->getMessage() . "\n";
}

// 3) Seed users with REAL bcrypt hashes
$pdo = db();
$hash = password_hash('password123', PASSWORD_BCRYPT);

$seedUsers = [
    ['Admin User',     'admin@homecare.com',     '9999999999', 'Head Office',     'admin'],
    ['John Caregiver', 'caregiver@homecare.com', '8888888888', '123 Care Street', 'caregiver'],
];

$up = $pdo->prepare(
    'INSERT INTO users (name, email, password, phone, address, role)
     VALUES (?, ?, ?, ?, ?, ?)
     ON DUPLICATE KEY UPDATE name = VALUES(name), password = VALUES(password),
        phone = VALUES(phone), address = VALUES(address), role = VALUES(role)'
);
foreach ($seedUsers as $u) {
    $up->execute([$u[0], $u[1], $hash, $u[2], $u[3], $u[4]]);
}
echo "[OK] Seed users ready (password for all: password123)\n";

echo "\nLogins:\n";
echo "  Admin     -> admin@homecare.com / password123\n";
echo "  Caregiver -> caregiver@homecare.com / password123\n";
echo "\nCustom Admin Panel: http://localhost/homecare/admin/\n";
echo "API base URL:       http://localhost/homecare/\n";
echo "\nDONE. Please delete setup.php now.\n";
