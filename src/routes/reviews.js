const express = require('express');
const crypto = require('crypto');
const { pool } = require('../db');
const { requireLogin } = require('../middleware/auth');
const { verifyCsrfToken } = require('../middleware/csrf');
const { upload } = require('../lib/upload');
const { uploadObject } = require('../lib/r2');
const { verifyTurnstile } = require('../lib/turnstile');
const { getClientIp } = require('../lib/auditLog');

const router = express.Router();

const CASE_TYPES = new Set(['civil', 'criminal']);

async function findEligibleSource(userId, listingId) {
  const { rows: questionRows } = await pool.query(
    `SELECT id FROM questions
     WHERE user_id = $1 AND listing_id = $2 AND status = 'answered'
       AND id NOT IN (SELECT source_id FROM reviews WHERE source_type = 'question')
     LIMIT 1`,
    [userId, listingId]
  );
  if (questionRows[0]) return { sourceType: 'question', sourceId: questionRows[0].id };

  const { rows: caseRows } = await pool.query(
    `SELECT id FROM case_verifications
     WHERE user_id = $1 AND listing_id = $2 AND status = 'approved'
       AND id NOT IN (SELECT source_id FROM reviews WHERE source_type = 'case_verification')
     LIMIT 1`,
    [userId, listingId]
  );
  if (caseRows[0]) return { sourceType: 'case_verification', sourceId: caseRows[0].id };

  return null;
}

router.get('/listing/:id/verify-case', requireLogin, async (req, res, next) => {
  try {
    const id = Number(req.params.id);
    if (!Number.isInteger(id)) return res.status(404).render('404');
    const { rows } = await pool.query('SELECT * FROM listings WHERE id = $1', [id]);
    if (!rows[0]) return res.status(404).render('404');
    res.render('verify-case', { title: 'Verify a case', listing: rows[0], error: null, submitted: false });
  } catch (err) {
    next(err);
  }
});

router.post('/listing/:id/verify-case', requireLogin, upload.single('id_document'), verifyCsrfToken, async (req, res, next) => {
  try {
    const id = Number(req.params.id);
    if (!Number.isInteger(id)) return res.status(404).render('404');
    const { rows } = await pool.query('SELECT * FROM listings WHERE id = $1', [id]);
    const listing = rows[0];
    if (!listing) return res.status(404).render('404');

    const rerender = (error) =>
      res.status(400).render('verify-case', { title: 'Verify a case', listing, error, submitted: false });

    const ip = getClientIp(req);
    const turnstileOk = await verifyTurnstile(req.body['cf-turnstile-response'], ip);
    if (!turnstileOk) return rerender('Please complete the security check and try again.');

    const caseNumber = (req.body.case_number || '').trim();
    const caseDate = (req.body.case_date || '').trim();
    const county = (req.body.county || '').trim();
    const caseType = req.body.case_type;

    if (!caseNumber || !caseDate || !county || !CASE_TYPES.has(caseType)) {
      return rerender('Please fill in all case details.');
    }
    if (!req.file) {
      return rerender('Please upload a photo of your ID.');
    }

    const idDocumentKey = `case-verifications/${req.session.userId}/${crypto.randomUUID()}`;
    await uploadObject(idDocumentKey, req.file.buffer, req.file.mimetype);

    await pool.query(
      `INSERT INTO case_verifications (user_id, listing_id, case_number, case_date, county, case_type, id_document_key)
       VALUES ($1, $2, $3, $4, $5, $6, $7)`,
      [req.session.userId, listing.id, caseNumber, caseDate, county, caseType, idDocumentKey]
    );

    res.render('verify-case', { title: 'Verify a case', listing, error: null, submitted: true });
  } catch (err) {
    next(err);
  }
});

router.get('/listing/:id/review', requireLogin, async (req, res, next) => {
  try {
    const id = Number(req.params.id);
    if (!Number.isInteger(id)) return res.status(404).render('404');
    const { rows } = await pool.query('SELECT * FROM listings WHERE id = $1', [id]);
    const listing = rows[0];
    if (!listing) return res.status(404).render('404');

    const eligible = await findEligibleSource(req.session.userId, listing.id);
    res.render('review-form', { title: 'Leave a review', listing, eligible, error: null });
  } catch (err) {
    next(err);
  }
});

router.post('/listing/:id/review', requireLogin, verifyCsrfToken, async (req, res, next) => {
  try {
    const id = Number(req.params.id);
    if (!Number.isInteger(id)) return res.status(404).render('404');
    const { rows } = await pool.query('SELECT * FROM listings WHERE id = $1', [id]);
    const listing = rows[0];
    if (!listing) return res.status(404).render('404');

    const eligible = await findEligibleSource(req.session.userId, listing.id);
    if (!eligible) return res.status(403).render('403');

    const rating = Number(req.body.rating);
    const bodyText = (req.body.body || '').trim();
    if (!Number.isInteger(rating) || rating < 1 || rating > 5) {
      return res.status(400).render('review-form', { title: 'Leave a review', listing, eligible, error: 'Please choose a rating.' });
    }

    try {
      await pool.query(
        `INSERT INTO reviews (listing_id, user_id, rating, body, source_type, source_id)
         VALUES ($1, $2, $3, $4, $5, $6)`,
        [listing.id, req.session.userId, rating, bodyText, eligible.sourceType, eligible.sourceId]
      );
    } catch (err) {
      if (err.code === '23505') {
        return res.status(400).render('review-form', { title: 'Leave a review', listing, eligible: null, error: 'You have already reviewed this.' });
      }
      throw err;
    }

    res.redirect(`/listing/${listing.id}`);
  } catch (err) {
    next(err);
  }
});

module.exports = router;
