<?php
require __DIR__ . '/includes.php';
require_admin();

try {
    $pdo = getPDO();
    $rows = $pdo->query("
        SELECT id,
               stripe_session_id,
               stripe_payment_intent_id,
               template_id,
               amount_cents,
               currency,
               status,
               customer_email,
               metadata,
               created_at
        FROM payments
        ORDER BY created_at DESC
        LIMIT 100
    ")->fetchAll();
} catch (Throwable $e) {
    $rows = [];
    $error = $e->getMessage();
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Payments – Attorney Directory Admin</title>
  <link rel="stylesheet" href="/styles.css">
  <style>
    body { font-family: system-ui, -apple-system, sans-serif; margin: 40px; background:#020617; color:#e5e7eb; }
    table { border-collapse:collapse; width:100%; margin-top:20px; }
    th, td { border:1px solid #1e293b; padding:8px 10px; font-size:0.85rem; }
    th { background:#020617; }
    a { color:#38bdf8; text-decoration:none; }
    a:hover { text-decoration:underline; }
  </style>
</head>
<body>
  <h1>Payments</h1>

  <?php if (!empty($error)): ?>
    <p style="color:#fecaca;">Error: <?= h($error) ?></p>
  <?php endif; ?>

  <?php if (!$rows): ?>
    <p>No payments found.</p>
  <?php else: ?>
    <table>
      <thead>
      <tr>
        <th>ID</th>
        <th>Session</th>
        <th>Intent</th>
        <th>Template</th>
        <th>Amount</th>
        <th>Status</th>
        <th>Email</th>
        <th>Created</th>
      </tr>
      </thead>
      <tbody>
      <?php foreach ($rows as $r): ?>
        <tr>
          <td><?= (int)$r['id'] ?></td>
          <td><?= h(substr((string)$r['stripe_session_id'], 0, 24)) ?></td>
          <td><?= h(substr((string)$r['stripe_payment_intent_id'], 0, 24)) ?></td>
          <td><?= h((string)$r['template_id']) ?></td>
          <td>
            <?php
              $amt = isset($r['amount_cents']) ? (int)$r['amount_cents'] : 0;
              $cur = strtoupper((string)($r['currency'] ?? 'usd'));
              printf('$%.2f %s', $amt / 100, $cur);
            ?>
          </td>
          <td><?= h((string)$r['status']) ?></td>
          <td><?= h((string)$r['customer_email']) ?></td>
          <td><?= h((string)$r['created_at']) ?></td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>

  <p style="margin-top:16px;"><a href="/admin_panel.php">← Back to Admin Panel</a></p>
</body>
</html>
