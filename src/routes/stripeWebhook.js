const express = require('express');
const { pool } = require('../db');
const { getStripe } = require('../lib/stripe');

const router = express.Router();

// Mounted with express.raw() at the route level so Stripe's signature check
// gets the untouched request body — this router must be registered in
// server.js BEFORE the global express.urlencoded() middleware.
router.post('/stripe', express.raw({ type: 'application/json' }), async (req, res, next) => {
  try {
    const webhookSecret = process.env.STRIPE_WEBHOOK_SECRET;
    if (!webhookSecret) {
      console.error('STRIPE_WEBHOOK_SECRET is not set; rejecting webhook.');
      return res.status(500).send('Webhook not configured');
    }

    const stripe = getStripe();
    const signature = req.headers['stripe-signature'];
    let event;
    try {
      event = stripe.webhooks.constructEvent(req.body, signature, webhookSecret);
    } catch (err) {
      console.error('Stripe webhook signature verification failed:', err.message);
      return res.status(400).send(`Webhook Error: ${err.message}`);
    }

    if (event.type === 'checkout.session.completed') {
      const session = event.data.object;
      const meta = session.metadata || {};

      if (meta.type === 'question' && meta.question_id) {
        await pool.query(
          `UPDATE questions SET status = 'paid', stripe_payment_intent_id = $1, paid_at = now()
           WHERE id = $2 AND status = 'pending_payment'`,
          [session.payment_intent, Number(meta.question_id)]
        );
      } else if (meta.type === 'contract_purchase' && meta.purchase_id) {
        await pool.query(
          `UPDATE contract_purchases SET status = 'paid', paid_at = now()
           WHERE id = $1 AND status = 'pending_payment'`,
          [Number(meta.purchase_id)]
        );
      }
    }

    res.json({ received: true });
  } catch (err) {
    next(err);
  }
});

module.exports = router;
