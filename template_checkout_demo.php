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
    <style>
      body { font-family: system-ui, -apple-system, sans-serif; margin: 40px; }
      .item { margin-bottom: 12px; }
      a.btn {
        display: inline-block;
        padding: 6px 10px;
        border-radius: 4px;
        background: #635bff;
        color: #fff;
        text-decoration: none;
        font-size: 14px;
      }
      a.btn:hover { background: #5046ff; }
    </style>
  </head>
  <body>
    <h1>Template Checkout Demo</h1>

    <?php foreach ($templates as $t): ?>
      <div class="item">
        <strong><?= htmlspecialchars($t['name']) ?></strong>
        — $<?= number_format($t['price_cents'] / 100, 2) ?>
        <a class="btn"
           href="/create_checkout_session.php?template_id=<?= urlencode($t['id']) ?>">
          💳 Pay with Stripe
        </a>
      </div>
    <?php endforeach; ?>

    <p>
      Admin: <a href="/admin_panel.php">Go to admin panel</a>
    </p>
  </body>
</html>
