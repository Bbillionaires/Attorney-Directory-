<?php
// Single paid template (uses Stripe)
$paidTemplate = [
    'id'          => 'money-agreement-v1',
    'name'        => 'Money Agreement',
    'price_cents' => 1000, // $10.00
];
?>
<!doctype html>
<html>
  <body>
    <h1>Template Checkout Demo</h1>

    <h2>Paid Template (via Stripe)</h2>
    <div>
      <?= htmlspecialchars($paidTemplate['name']) ?>
      - $<?= number_format($paidTemplate['price_cents'] / 100, 2) ?>
      <a href="/create_checkout_session.php?template_id=<?= urlencode($paidTemplate['id']) ?>">
        💳 Pay with Stripe – Money Agreement
      </a>
    </div>

    <hr>

    <h2>Free Template (no Stripe)</h2>
    <p>This NDA template is completely free and does NOT use Stripe.</p>
    <a href="/free_nda.php">
      📄 Download NDA Template
    </a>
  </body>
</html>
