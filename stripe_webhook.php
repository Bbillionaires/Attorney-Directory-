<?php
// stripe_webhook.php (local test-friendly version)
// - If STRIPE_WEBHOOK_NO_VERIFY=1 it will skip signature verification (local only).
// - Designed to be minimal and avoid PHP parse issues.

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/db.php';

error_reporting(E_ALL);
ini_set('display_errors', '1');

$payload = @file_get_contents('php://input');
$no_verify = getenv('STRIPE_WEBHOOK_NO_VERIFY') === '1';
$sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'] ?? '';
$endpoint_secret = getenv('STRIPE_WEBHOOK_SECRET');

if (!$no_verify) {
    if (!$endpoint_secret) {
        http_response_code(500);
        echo "Missing STRIPE_WEBHOOK_SECRET\n";
        exit;
    }
    try {
        $event = \Stripe\Webhook::constructEvent($payload, $sig_header, $endpoint_secret);
    } catch (\UnexpectedValueException $e) {
        http_response_code(400);
        echo 'Invalid payload';
        exit;
    } catch (\Stripe\Exception\SignatureVerificationException $e) {
        http_response_code(400);
        echo 'Invalid signature';
        exit;
    }
} else {
    // Local test mode: decode payload without signature verification
    $event = json_decode($payload, false);
    if (!$event) {
        http_response_code(400);
        echo 'Invalid JSON payload';
        exit;
    }
}

// Normalize session object (handle CLI-triggered event vs direct session JSON)
$session = $event->data->object ?? $event;

// Only process when it looks like a checkout.session.completed
$is_session_completed = (isset($event->type) && $event->type === 'checkout.session.completed')
    || (isset($session->object) && $session->object === 'checkout.session')
    || (isset($session->payment_intent) && isset($session->id));

if ($is_session_completed) {
    $session_id = $session->id ?? null;
    $payment_intent = $session->payment_intent ?? null;
    $amount_total = $session->amount_total ?? ($session->amount_subtotal ?? null);
    $currency = $session->currency ?? null;
    $customer_email = $session->customer_details->email ?? ($session->customer_email ?? null);
    $metadata_obj = $session->metadata ?? null;
    $metadata = json_encode($metadata_obj ?? new stdClass());

    try {
        $pdo = getPDO();
        $stmt = $pdo->prepare(
            'INSERT INTO payments (stripe_session_id, stripe_payment_intent_id, template_id, amount_cents, currency, status, customer_email, metadata)
             VALUES (:session_id, :pi, :template_id, :amount, :currency, :status, :email, :metadata)
             ON CONFLICT (stripe_session_id) DO NOTHING'
        );
        $template_id_val = null;
        if (is_object($metadata_obj) && property_exists($metadata_obj, 'template_id')) {
            $template_id_val = $metadata_obj->template_id;
        } elseif (is_array($metadata_obj) && isset($metadata_obj['template_id'])) {
            $template_id_val = $metadata_obj['template_id'];
        }
        $status_val = $session->payment_status ?? ($session->status ?? 'unknown');

        $stmt->execute([
            ':session_id' => $session_id,
            ':pi' => $payment_intent,
            ':template_id' => $template_id_val,
            ':amount' => $amount_total,
            ':currency' => $currency,
            ':status' => $status_val,
            ':email' => $customer_email,
            ':metadata' => $metadata,
        ]);
    } catch (Throwable $e) {
        http_response_code(500);
        echo "DB ERROR: " . $e->getMessage();
        exit;
    }
}

http_response_code(200);
echo json_encode(['received' => true]);
