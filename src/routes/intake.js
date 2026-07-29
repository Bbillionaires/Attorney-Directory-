const express = require('express');
const crypto = require('crypto');
const { pool } = require('../db');
const { verifyCsrfToken } = require('../middleware/csrf');
const { verifyTurnstile } = require('../lib/turnstile');
const { getClientIp } = require('../lib/auditLog');

const router = express.Router();

const SOURCES = {
  quick_case_review: { title: 'Quick Case Review', lead: 'Answer a few questions about your situation.' },
  attorney_match: { title: 'Attorney Match', lead: 'Receive an attorney recommendation based on your legal issue, location, and service needs.' },
  legal_question: { title: 'Legal Question', lead: 'Describe what happened and identify the type of legal help you may need.' },
};

// Crockford Base32 (excludes I, L, O, U to avoid visual ambiguity with 1/0/V)
const CODE_ALPHABET = '0123456789ABCDEFGHJKMNPQRSTVWXYZ';

function generateReferenceCode() {
  const bytes = crypto.randomBytes(8);
  let code = '';
  for (let i = 0; i < 8; i++) {
    code += CODE_ALPHABET[bytes[i] % CODE_ALPHABET.length];
  }
  return `${code.slice(0, 4)}-${code.slice(4)}`;
}

function renderIntakeForm(req, res, source, extra) {
  res.render('intake-form', {
    title: SOURCES[source].title,
    source,
    sourceMeta: SOURCES[source],
    error: null,
    form: {},
    ...extra,
  });
}

router.get('/case-review', async (req, res, next) => {
  try {
    const { rows: categories } = await pool.query('SELECT * FROM categories ORDER BY name ASC');
    renderIntakeForm(req, res, 'quick_case_review', { categories });
  } catch (err) {
    next(err);
  }
});

router.get('/attorney-match', async (req, res, next) => {
  try {
    const { rows: categories } = await pool.query('SELECT * FROM categories ORDER BY name ASC');
    renderIntakeForm(req, res, 'attorney_match', { categories });
  } catch (err) {
    next(err);
  }
});

router.get('/legal-question', async (req, res, next) => {
  try {
    const { rows: categories } = await pool.query('SELECT * FROM categories ORDER BY name ASC');
    renderIntakeForm(req, res, 'legal_question', { categories });
  } catch (err) {
    next(err);
  }
});

router.post('/intake', verifyCsrfToken, async (req, res, next) => {
  try {
    const body = req.body;
    const source = SOURCES[body.source] ? body.source : null;
    const ip = getClientIp(req);

    async function fail(error) {
      const { rows: categories } = await pool.query('SELECT * FROM categories ORDER BY name ASC');
      res.status(400).render('intake-form', {
        title: source ? SOURCES[source].title : 'Get Matched',
        source: source || 'quick_case_review',
        sourceMeta: SOURCES[source || 'quick_case_review'],
        error,
        form: body,
        categories,
      });
    }

    if (!source) return fail('Please try again.');

    const turnstileOk = await verifyTurnstile(body['cf-turnstile-response'], ip);
    if (!turnstileOk) return fail('Please complete the security check and try again.');

    const name = (body.name || '').trim();
    const email = (body.email || '').trim();
    const phone = (body.phone || '').trim();
    const description = (body.description || '').trim();
    const categoryId = body.category_id ? Number(body.category_id) : null;
    const city = (body.city || '').trim();
    const state = (body.state || '').trim().toUpperCase();

    if (!name || !email || !description) {
      return fail('Please fill in your name, email, and a brief description of your situation.');
    }

    const referenceCode = generateReferenceCode();
    const userId = req.session.userId || null;

    const { rows } = await pool.query(
      `INSERT INTO case_intakes (user_id, source, name, email, phone, category_id, city, state, description, reference_code, ip)
       VALUES ($1,$2,$3,$4,$5,$6,$7,$8,$9,$10,$11) RETURNING *`,
      [userId, source, name, email, phone, categoryId, city, state, description, referenceCode, ip]
    );
    const intake = rows[0];

    const matchConditions = ['listings.active = true'];
    const params = [];
    if (categoryId) {
      params.push(categoryId);
      matchConditions.push(`listings.category_id = $${params.length}`);
    }
    if (state) {
      params.push(state);
      matchConditions.push(`listings.state = $${params.length}`);
    }
    const { rows: matches } = await pool.query(
      `SELECT listings.*, categories.name AS category_name
       FROM listings
       LEFT JOIN categories ON categories.id = listings.category_id
       WHERE ${matchConditions.join(' AND ')}
       ORDER BY listings.is_verified DESC, listings.name ASC
       LIMIT 10`,
      params
    );

    res.render('intake-result', { title: 'Your Matches', intake, matches });
  } catch (err) {
    next(err);
  }
});

router.get('/check-request', (req, res) => {
  res.render('check-request', { title: 'Check My Request', error: null, intake: null, form: {} });
});

router.post('/check-request', verifyCsrfToken, async (req, res, next) => {
  try {
    const referenceCode = (req.body.reference_code || '').trim().toUpperCase();
    const email = (req.body.email || '').trim();
    const ip = getClientIp(req);

    const turnstileOk = await verifyTurnstile(req.body['cf-turnstile-response'], ip);
    if (!turnstileOk) {
      return res.status(400).render('check-request', {
        title: 'Check My Request', error: 'Please complete the security check and try again.', intake: null, form: req.body,
      });
    }

    if (!referenceCode || !email) {
      return res.status(400).render('check-request', {
        title: 'Check My Request', error: 'Please enter both your reference code and email.', intake: null, form: req.body,
      });
    }

    const { rows } = await pool.query(
      `SELECT case_intakes.*, categories.name AS category_name FROM case_intakes
       LEFT JOIN categories ON categories.id = case_intakes.category_id
       WHERE reference_code = $1 AND email = $2`,
      [referenceCode, email]
    );

    if (!rows[0]) {
      return res.status(404).render('check-request', {
        title: 'Check My Request', error: 'No matching request found. Double-check your reference code and email.', intake: null, form: req.body,
      });
    }

    res.render('check-request', { title: 'Check My Request', error: null, intake: rows[0], form: {} });
  } catch (err) {
    next(err);
  }
});

module.exports = router;
