<?php
require __DIR__ . '/db.php';

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
               created_at
        FROM payments
        ORDER BY created_at DESC
        LIMIT 50
    ")->fetchAll(PDO::FETCH_ASSOC);
} catch (Throwable $e) {
    http_response_code(500);
    echo "DB ERROR: " . htmlspecialchars($e->getMessage());
    exit;
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Payments Admin</title>
  <style>
    body { font-family: system-ui, -apple-system, BlinkMacSystemFont, sans-serif; margin: 40px; }
    table { border-collapse: collapse; width: 100%; max-width: 1000px; }
    th, td { border: 1px solid #ddd; padding: 6px 8px; font-size: 13px; }
    th { background: #f5f5f5; text-align: left; }
    tr:nth-child(even) { background: #fafafa; }
    .status-paid { color: #0a0; font-weight: 600; }
    .status-other { color: #a60; }
  </style>
</head>
<body>
  <h1>Payments Admin</h1>
  <p>Showing latest payments (max 50).</p>
  <table>
    <thead>
      <tr>
        <th>ID</th>
        <th>Session</th>
        <th>Intent</th>
        <th>Template</th>
        <th>Amount</th>
        <th>Currency</th>
        <th>Status</th>
        <th>Email</th>
        <th>Created</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($rows as $r): ?>
        <tr>
          <td><?= (int)$r['id'] ?></td>
          <td><?= htmlspecialchars($r['stripe_session_id']) ?></td>
          <td><?= htmlspecialchars($r['stripe_payment_intent_id']) ?></td>
          <td><?= htmlspecialchars($r['template_id']) ?></td>
          <td>$<?= number_format(($r['amount_cents'] ?? 0) / 100, 2) ?></td>
          <td><?= htmlspecialchars(strtoupper($r['currency'] ?? '')) ?></td>
          <?php $st = $r['status'] ?? ''; ?>
          <td class="<?= $st === 'paid' ? 'status-paid' : 'status-other' ?>">
            <?= htmlspecialchars($st) ?>
          </td>
          <td><?= htmlspecialchars($r['customer_email'] ?? '') ?></td>
          <td><?= htmlspecialchars($r['created_at']) ?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</body>
</html>
