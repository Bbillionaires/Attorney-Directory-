const express = require('express');
const crypto = require('crypto');
const { pool } = require('../db');
const { verifyCsrfToken } = require('../middleware/csrf');
const { verifyTurnstile } = require('../lib/turnstile');
const { getClientIp } = require('../lib/auditLog');

const router = express.Router();

router.post('/newsletter/subscribe', verifyCsrfToken, async (req, res, next) => {
  try {
    const email = (req.body.email || '').trim();
    const ip = getClientIp(req);

    const turnstileOk = await verifyTurnstile(req.body['cf-turnstile-response'], ip);
    if (!turnstileOk || !email) {
      return res.redirect('/?newsletter=error#stay-informed');
    }

    const prefCaseUpdates = req.body.pref_case_updates === '1';
    const prefLegalAlerts = req.body.pref_legal_alerts === '1';
    const prefHelpfulGuides = req.body.pref_helpful_guides === '1';
    const unsubscribeToken = crypto.randomBytes(16).toString('hex');

    await pool.query(
      `INSERT INTO newsletter_subscribers (email, pref_case_updates, pref_legal_alerts, pref_helpful_guides, unsubscribe_token)
       VALUES ($1, $2, $3, $4, $5)
       ON CONFLICT (email) DO UPDATE SET
         pref_case_updates = $2, pref_legal_alerts = $3, pref_helpful_guides = $4, unsubscribed_at = NULL`,
      [email, prefCaseUpdates, prefLegalAlerts, prefHelpfulGuides, unsubscribeToken]
    );

    res.redirect('/?newsletter=success#stay-informed');
  } catch (err) {
    next(err);
  }
});

router.get('/newsletter/unsubscribe', async (req, res, next) => {
  try {
    const token = (req.query.token || '').trim();
    if (!token) return res.render('newsletter-unsubscribe', { title: 'Unsubscribe', success: false });

    const { rows } = await pool.query(
      `UPDATE newsletter_subscribers SET unsubscribed_at = now() WHERE unsubscribe_token = $1 RETURNING id`,
      [token]
    );
    res.render('newsletter-unsubscribe', { title: 'Unsubscribe', success: rows.length > 0 });
  } catch (err) {
    next(err);
  }
});

module.exports = router;
