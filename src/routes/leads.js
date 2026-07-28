const express = require('express');
const { pool } = require('../db');
const { verifyCsrfToken } = require('../middleware/csrf');
const { verifyTurnstile } = require('../lib/turnstile');
const { getClientIp } = require('../lib/auditLog');

const router = express.Router();

function validateCommon(body) {
  const name = (body.name || '').trim();
  const email = (body.email || '').trim();
  const phone = (body.phone || '').trim();
  const caseDescription = (body.case_description || '').trim();
  if (!name || !email || !caseDescription) return null;
  return { name, email, phone, caseDescription };
}

router.get('/litigation-support', (req, res) => {
  res.render('litigation-support', { title: 'Litigation Support', error: null, submitted: false, form: {} });
});

router.post('/litigation-support', verifyCsrfToken, async (req, res, next) => {
  try {
    const ip = getClientIp(req);
    const turnstileOk = await verifyTurnstile(req.body['cf-turnstile-response'], ip);
    if (!turnstileOk) {
      return res.status(400).render('litigation-support', {
        title: 'Litigation Support', error: 'Please complete the security check and try again.', submitted: false, form: req.body,
      });
    }

    const fields = validateCommon(req.body);
    if (!fields) {
      return res.status(400).render('litigation-support', {
        title: 'Litigation Support', error: 'Please fill in your name, email, and a brief case description.', submitted: false, form: req.body,
      });
    }

    await pool.query(
      `INSERT INTO leads (type, name, email, phone, case_description) VALUES ('litigation_support', $1, $2, $3, $4)`,
      [fields.name, fields.email, fields.phone, fields.caseDescription]
    );

    res.render('litigation-support', { title: 'Litigation Support', error: null, submitted: true, form: {} });
  } catch (err) {
    next(err);
  }
});

router.get('/settlement-loan', (req, res) => {
  res.render('settlement-loan', { title: 'Lawsuit Settlement Loans', error: null, submitted: false, form: {} });
});

router.post('/settlement-loan', verifyCsrfToken, async (req, res, next) => {
  try {
    const ip = getClientIp(req);
    const turnstileOk = await verifyTurnstile(req.body['cf-turnstile-response'], ip);
    if (!turnstileOk) {
      return res.status(400).render('settlement-loan', {
        title: 'Lawsuit Settlement Loans', error: 'Please complete the security check and try again.', submitted: false, form: req.body,
      });
    }

    const fields = validateCommon(req.body);
    if (!fields) {
      return res.status(400).render('settlement-loan', {
        title: 'Lawsuit Settlement Loans', error: 'Please fill in your name, email, and a brief case description.', submitted: false, form: req.body,
      });
    }
    const amountSoughtCents = req.body.amount_sought_dollars
      ? Math.round(Number(req.body.amount_sought_dollars) * 100)
      : null;

    await pool.query(
      `INSERT INTO leads (type, name, email, phone, case_description, amount_sought_cents) VALUES ('settlement_loan', $1, $2, $3, $4, $5)`,
      [fields.name, fields.email, fields.phone, fields.caseDescription, amountSoughtCents]
    );

    res.render('settlement-loan', { title: 'Lawsuit Settlement Loans', error: null, submitted: true, form: {} });
  } catch (err) {
    next(err);
  }
});

module.exports = router;
