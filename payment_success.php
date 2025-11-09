<?php
require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/db.php';

$stripeSecretKey = getenv('STRIPE_SECRET_KEY');
if (!$stripeSecretKey) {
    http_response_code(500);
    echo "Missing STRIPE_SECRET_KEY\n";
    exit;
}

\Stripe\Stripe::setApiKey($stripeSecretKey);

$sessionId = $_GET['session_id'] ?? null;
if (!$sessionId) {
    http_response_code(400);
    echo "Missing session_id\n";
    exit;
}

try {
    $session = \Stripe\Checkout\Session::retrieve($sessionId);
} catch (Throwable $e) {
    http_response_code(500);
    echo "Error retrieving session: " . $e->getMessage() . "\n";
    exit;
}

$templateId      = $session->metadata->template_id ?? null;
$amountTotal     = $session->amount_total ?? null;
$currency        = $session->currency ?? null;
$customerEmail   = $session->customer_details->email ?? null;
$paymentIntentId = $session->payment_intent ?? null;
$status          = $session->payment_status ?? ($session->status ?? 'unknown');

try {
    $pdo = getPDO();
    $stmt = $pdo->prepare('
        INSERT INTO payments (
            stripe_session_id,
            stripe_payment_intent_id,
            template_id,
            amount_cents,
            currency,
            status,
            customer_email,
            metadata
        ) VALUES (
            :sid,:pi,:tid,:amt,:cur,:st,:email,:meta
        )
        ON CONFLICT (stripe_session_id) DO NOTHING
    ');
    $stmt->execute([
        ':sid'   => $session->id,
        ':pi'    => $paymentIntentId,
        ':tid'   => $templateId,
        ':amt'   => $amountTotal,
        ':cur'   => $currency,
        ':st'    => $status,
        ':email' => $customerEmail,
        ':meta'  => json_encode($session->metadata ?? new stdClass()),
    ]);
} catch (Throwable $e) {
    error_log('payment_success DB error: ' . $e->getMessage());
}
?>
<!doctype html>
<html>
  <body>
    <h1>✅ Payment Successful</h1>
    <?php if (!empty($templateId)): ?>
      <p>Template: <?= htmlspecialchars($templateId) ?></p>
    <?php endif; ?>
    <p>Thank you for your purchase.</p>
    <p><a href="/admin_payments.php">View payments</a></p>
  </body>
</html>
