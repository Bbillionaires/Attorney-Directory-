<?php
require __DIR__ . '/db.php';

try {
    $pdo = getPDO();
    $rows = $pdo->query("
        SELECT id, stripe_session_id, stripe_payment_intent_id,
               template_id, amount_cents, currency, status,
               customer_email, created_at
        FROM payments
        ORDER BY created_at DESC
        LIMIT 50
    ")->fetchAll();
    $error = null;
} catch (Throwable $e) {
    $rows = [];
    $error = $e->getMessage();
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Payments Admin</title>
  <style>
    body { font-family: system-ui, -apple-system, sans-serif; margin: 40px; }
    table { border-collapse: collapse; width: 100%; max-width: 1100px; }
    th, td { border: 1px solid #ddd; padding: 6px 8px; font-size: 13px; }
    th { background: #f5f5f5; text-align: left; }
    tr:nth-child(even) { background: #fafafa; }
    .status-paid { color: #0a0; font-weight: 600; }
    .status-other { color: #a60; }
  </style>
</head>
<body>
  <h1>Payments</h1>

  <?php if ($error): ?>
    <p style="color:#c00;">DB error: <?= htmlspecialchars($error) ?></p>
  <?php endif; ?>

  <?php if (!$rows): ?>
    <p>No payments found yet.</p>
  <?php else: ?>
    <table>
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
      <?php foreach ($rows as $r): ?>
        <tr>
          <td><?= (int)$r['id'] ?></td>
          <td><?= htmlspecialchars(substr($r['stripe_session_id'], 0, 18)) ?>…</td>
          <td><?= htmlspecialchars((string)$r['stripe_payment_intent_id']) ?></td>
          <td><?= htmlspecialchars((string)$r['template_id']) ?></td>
          <td>
            <?php if ($r['amount_cents'] !== null): ?>
              $<?= number_format($r['amount_cents'] / 100, 2) ?>
            <?php endif; ?>
          </td>
          <td class="<?= $r['status'] === 'paid' ? 'status-paid' : 'status-other' ?>">
            <?= htmlspecialchars((string)$r['status']) ?>
          </td>
          <td><?= htmlspecialchars((string)$r['customer_email']) ?></td>
          <td><?= htmlspecialchars((string)$r['created_at']) ?></td>
        </tr>
      <?php endforeach; ?>
    </table>
  <?php endif; ?>
</body>
</html>
