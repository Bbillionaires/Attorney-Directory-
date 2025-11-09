<?php
require __DIR__ . '/db.php';
$pdo = getPDO();
$sql = <<<SQL
CREATE TABLE IF NOT EXISTS payments (
  id SERIAL PRIMARY KEY,
  stripe_session_id TEXT UNIQUE,
  stripe_payment_intent_id TEXT,
  template_id TEXT,
  amount_cents INT,
  currency TEXT,
  status TEXT,
  customer_email TEXT,
  metadata JSONB,
  created_at TIMESTAMP WITH TIME ZONE DEFAULT now()
);
SQL;
$pdo->exec($sql);
echo "payments table created or already exists\n";
