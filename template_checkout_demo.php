<?php
$templates = [
    ['id' => 'money-agreement-v1', 'name' => 'Money Agreement', 'price_cents' => 1000],
    ['id' => 'nda-template-v1',    'name' => 'NDA Template',    'price_cents' => 50],
];
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Template Checkout Demo</title>
</head>
<body>
  <h1>Template Checkout Demo</h1>

  <ul>
    <?php foreach ($templates as $t): ?>
      <li>
        <?= htmlspecialchars($t['name']) ?>
        — $<?= number_format($t['price_cents'] / 100, 2) ?>
        <a href="/create_checkout_session.php?template_id=<?= urlencode($t['id']) ?>">
          💳 Pay with Stripe
        </a>
      </li>
    <?php endforeach; ?>
  </ul>

  <p><a href="/admin_panel.php">Go to admin panel</a></p>
  <p><a href="/">Back to main site</a></p>
</body>
</html>
