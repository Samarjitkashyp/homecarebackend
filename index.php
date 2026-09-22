<?php
// Simple landing / health page for the backend root.
header('Content-Type: text/html; charset=utf-8');
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Absolute Choice Homecare — Backend</title>
  <style>
    body{font-family:system-ui,Segoe UI,Roboto,sans-serif;background:#0f172a;color:#e2e8f0;margin:0;padding:40px}
    .card{max-width:640px;margin:0 auto;background:#1e293b;border:1px solid #334155;border-radius:16px;padding:32px}
    h1{margin:0 0 4px;font-size:22px}
    .muted{color:#94a3b8;font-size:14px;margin-bottom:24px}
    a{color:#38bdf8;text-decoration:none} a:hover{text-decoration:underline}
    .row{display:flex;justify-content:space-between;padding:10px 0;border-bottom:1px solid #334155}
    .ok{color:#4ade80}.warn{color:#fbbf24}
    code{background:#0f172a;padding:2px 6px;border-radius:6px}
  </style>
</head>
<body>
  <div class="card">
    <h1>Absolute Choice Homecare API</h1>
    <div class="muted">Core PHP backend running on XAMPP</div>
    <div class="row"><span>API base URL</span><code>http://localhost/homecare/</code></div>
    <div class="row"><span>Custom Admin Panel</span><a href="admin/">/admin/ &rarr;</a></div>
    <div class="row"><span>First-time setup</span><a href="setup.php">run setup.php</a></div>
    <div class="row"><span>Sample login</span><code>admin@homecare.com / password123</code></div>
    <p class="muted" style="margin-top:24px">
      For the Android emulator, set the app base URL to
      <code>http://10.0.2.2/homecare/</code>. For a physical device use your PC's LAN IP.
    </p>
  </div>
</body>
</html>
