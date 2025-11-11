<?php
require __DIR__ . '/includes.php';
require_admin();
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Attorney Directory Admin</title>
  <link rel="stylesheet" href="/styles.css">
  <style>
    body { font-family: system-ui, -apple-system, sans-serif; margin: 40px; background:#020617; color:#e5e7eb; }
    a { color:#38bdf8; text-decoration:none; }
    a:hover { text-decoration:underline; }
    .box { border-radius:0.75rem; border:1px solid #1e293b; padding:20px; background:#020617; }
  </style>
</head>
<body>
  <h1 class="text-3xl font-bold mb-4">Attorney Directory Admin</h1>
  <div class="box">
    <ul style="list-style:none; padding-left:0; line-height:1.9;">
      <li>🛒 <a href="/template_checkout_demo.php">Template Checkout Demo</a></li>
      <li>📄 <a href="/admin_payments.php">View Payments (admin)</a></li>
      <li>🏠 <a href="/">Back to main site</a></li>
      <li>�� <a href="/admin_logout.php">Log out</a></li>
    </ul>
  </div>
</body>
</html>
