const express = require('express');
const { pool } = require('../db');
const { requireLogin } = require('../middleware/auth');
const { verifyCsrfToken } = require('../middleware/csrf');
const { getStripe } = require('../lib/stripe');
const { getSignedDownloadUrl } = require('../lib/r2');

const router = express.Router();

router.get('/contracts', async (req, res, next) => {
  try {
    const { rows: contracts } = await pool.query(
      `SELECT contracts.*, listings.id AS listing_id, listings.name AS listing_name
       FROM contracts
       JOIN listings ON listings.id = contracts.listing_id
       WHERE contracts.active = true AND listings.active = true
       ORDER BY contracts.created_at DESC`
    );
    res.render('contracts', { title: 'Contracts for Sale', contracts });
  } catch (err) {
    next(err);
  }
});

router.post('/contracts/:id/buy', requireLogin, verifyCsrfToken, async (req, res, next) => {
  try {
    const id = Number(req.params.id);
    if (!Number.isInteger(id)) return res.status(404).render('404');

    const { rows } = await pool.query(
      `SELECT contracts.*, listings.user_id AS seller_user_id, listings.name AS listing_name
       FROM contracts JOIN listings ON listings.id = contracts.listing_id
       WHERE contracts.id = $1 AND contracts.active = true`,
      [id]
    );
    const contract = rows[0];
    if (!contract) return res.status(404).render('404');
    if (contract.seller_user_id === req.session.userId) return res.status(403).render('403');

    const { rows: inserted } = await pool.query(
      `INSERT INTO contract_purchases (contract_id, user_id, price_cents_paid)
       VALUES ($1, $2, $3) RETURNING id`,
      [contract.id, req.session.userId, contract.price_cents]
    );
    const purchaseId = inserted[0].id;

    const stripe = getStripe();
    const baseUrl = `${req.protocol}://${req.get('host')}`;
    const session = await stripe.checkout.sessions.create({
      mode: 'payment',
      line_items: [{
        price_data: {
          currency: 'usd',
          product_data: { name: `${contract.title} (${contract.listing_name})` },
          unit_amount: contract.price_cents,
        },
        quantity: 1,
      }],
      metadata: { type: 'contract_purchase', purchase_id: String(purchaseId) },
      success_url: `${baseUrl}/contracts/purchases/${purchaseId}/success?session_id={CHECKOUT_SESSION_ID}`,
      cancel_url: `${baseUrl}/contracts`,
    });

    await pool.query('UPDATE contract_purchases SET stripe_checkout_session_id = $1 WHERE id = $2', [session.id, purchaseId]);
    res.redirect(303, session.url);
  } catch (err) {
    next(err);
  }
});

router.get('/contracts/purchases/:id/success', requireLogin, async (req, res, next) => {
  try {
    const id = Number(req.params.id);
    if (!Number.isInteger(id)) return res.status(404).render('404');

    const { rows } = await pool.query(
      'SELECT * FROM contract_purchases WHERE id = $1 AND user_id = $2', [id, req.session.userId]
    );
    const purchase = rows[0];
    if (!purchase) return res.status(404).render('404');

    if (purchase.status === 'pending_payment' && req.query.session_id) {
      const stripe = getStripe();
      const session = await stripe.checkout.sessions.retrieve(String(req.query.session_id));
      if (session.payment_status === 'paid') {
        await pool.query(
          `UPDATE contract_purchases SET status = 'paid', paid_at = now()
           WHERE id = $1 AND status = 'pending_payment'`,
          [id]
        );
      }
    }
    res.redirect('/my/purchases');
  } catch (err) {
    next(err);
  }
});

router.get('/my/purchases', requireLogin, async (req, res, next) => {
  try {
    const { rows: purchases } = await pool.query(
      `SELECT contract_purchases.*, contracts.title, contracts.id AS contract_id, listings.name AS listing_name
       FROM contract_purchases
       JOIN contracts ON contracts.id = contract_purchases.contract_id
       JOIN listings ON listings.id = contracts.listing_id
       WHERE contract_purchases.user_id = $1
       ORDER BY contract_purchases.created_at DESC`,
      [req.session.userId]
    );
    res.render('my-purchases', { title: 'My purchases', purchases });
  } catch (err) {
    next(err);
  }
});

router.get('/contracts/purchases/:id/download', requireLogin, async (req, res, next) => {
  try {
    const id = Number(req.params.id);
    if (!Number.isInteger(id)) return res.status(404).render('404');

    const { rows } = await pool.query(
      `SELECT contract_purchases.*, contracts.file_key FROM contract_purchases
       JOIN contracts ON contracts.id = contract_purchases.contract_id
       WHERE contract_purchases.id = $1 AND contract_purchases.user_id = $2 AND contract_purchases.status = 'paid'`,
      [id, req.session.userId]
    );
    const purchase = rows[0];
    if (!purchase) return res.status(404).render('404');

    const url = await getSignedDownloadUrl(purchase.file_key, 300);
    res.redirect(url);
  } catch (err) {
    next(err);
  }
});

module.exports = router;
