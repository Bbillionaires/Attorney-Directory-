const express = require('express');
const { pool } = require('../db');
const { requireLogin } = require('../middleware/auth');
const { verifyCsrfToken } = require('../middleware/csrf');
const { getStripe } = require('../lib/stripe');

const router = express.Router();

router.get('/listing/:id/ask', requireLogin, async (req, res, next) => {
  try {
    const id = Number(req.params.id);
    if (!Number.isInteger(id)) return res.status(404).render('404');

    const { rows } = await pool.query('SELECT * FROM listings WHERE id = $1 AND active = true', [id]);
    const listing = rows[0];
    if (!listing || !listing.question_price_cents) return res.status(404).render('404');
    if (listing.user_id === req.session.userId) return res.status(403).render('403');

    res.render('ask-question', { title: 'Ask a question', listing, error: null });
  } catch (err) {
    next(err);
  }
});

router.post('/listing/:id/ask', requireLogin, verifyCsrfToken, async (req, res, next) => {
  try {
    const id = Number(req.params.id);
    if (!Number.isInteger(id)) return res.status(404).render('404');

    const { rows } = await pool.query('SELECT * FROM listings WHERE id = $1 AND active = true', [id]);
    const listing = rows[0];
    if (!listing || !listing.question_price_cents) return res.status(404).render('404');
    if (listing.user_id === req.session.userId) return res.status(403).render('403');

    const questionText = (req.body.question_text || '').trim();
    if (!questionText) {
      return res.status(400).render('ask-question', { title: 'Ask a question', listing, error: 'Please enter your question.' });
    }

    const { rows: inserted } = await pool.query(
      `INSERT INTO questions (user_id, listing_id, price_cents, question_text)
       VALUES ($1, $2, $3, $4) RETURNING id`,
      [req.session.userId, listing.id, listing.question_price_cents, questionText]
    );
    const questionId = inserted[0].id;

    const stripe = getStripe();
    const baseUrl = `${req.protocol}://${req.get('host')}`;
    const session = await stripe.checkout.sessions.create({
      mode: 'payment',
      line_items: [{
        price_data: {
          currency: 'usd',
          product_data: { name: `Question for ${listing.name}` },
          unit_amount: listing.question_price_cents,
        },
        quantity: 1,
      }],
      metadata: { type: 'question', question_id: String(questionId) },
      success_url: `${baseUrl}/questions/${questionId}/success?session_id={CHECKOUT_SESSION_ID}`,
      cancel_url: `${baseUrl}/listing/${listing.id}`,
    });

    await pool.query('UPDATE questions SET stripe_checkout_session_id = $1 WHERE id = $2', [session.id, questionId]);
    res.redirect(303, session.url);
  } catch (err) {
    next(err);
  }
});

router.get('/questions/:id/success', requireLogin, async (req, res, next) => {
  try {
    const id = Number(req.params.id);
    if (!Number.isInteger(id)) return res.status(404).render('404');

    const { rows } = await pool.query('SELECT * FROM questions WHERE id = $1 AND user_id = $2', [id, req.session.userId]);
    const question = rows[0];
    if (!question) return res.status(404).render('404');

    if (question.status === 'pending_payment' && req.query.session_id) {
      const stripe = getStripe();
      const session = await stripe.checkout.sessions.retrieve(String(req.query.session_id));
      if (session.payment_status === 'paid') {
        await pool.query(
          `UPDATE questions SET status = 'paid', stripe_payment_intent_id = $1, paid_at = now()
           WHERE id = $2 AND status = 'pending_payment'`,
          [session.payment_intent, id]
        );
      }
    }
    res.redirect(`/questions/${id}`);
  } catch (err) {
    next(err);
  }
});

router.get('/questions/:id', requireLogin, async (req, res, next) => {
  try {
    const id = Number(req.params.id);
    if (!Number.isInteger(id)) return res.status(404).render('404');

    const { rows } = await pool.query(
      `SELECT questions.*, listings.name AS listing_name FROM questions
       JOIN listings ON listings.id = questions.listing_id
       WHERE questions.id = $1 AND questions.user_id = $2`,
      [id, req.session.userId]
    );
    const question = rows[0];
    if (!question) return res.status(404).render('404');

    res.render('question', { title: 'My question', question });
  } catch (err) {
    next(err);
  }
});

module.exports = router;
