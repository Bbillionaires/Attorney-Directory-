const express = require('express');
const { pool } = require('../db');
const { requireLogin } = require('../middleware/auth');
const { verifyCsrfToken } = require('../middleware/csrf');
const { getSignedDownloadUrl } = require('../lib/r2');

const router = express.Router();

router.post('/listing/:id/save', requireLogin, verifyCsrfToken, async (req, res, next) => {
  try {
    const id = Number(req.params.id);
    if (!Number.isInteger(id)) return res.status(404).render('404');

    await pool.query(
      `INSERT INTO saved_listings (user_id, listing_id) VALUES ($1, $2)
       ON CONFLICT (user_id, listing_id) DO NOTHING`,
      [req.session.userId, id]
    );
    res.redirect(`/listing/${id}`);
  } catch (err) {
    next(err);
  }
});

router.post('/listing/:id/unsave', requireLogin, verifyCsrfToken, async (req, res, next) => {
  try {
    const id = Number(req.params.id);
    if (!Number.isInteger(id)) return res.status(404).render('404');

    await pool.query('DELETE FROM saved_listings WHERE user_id = $1 AND listing_id = $2', [req.session.userId, id]);
    res.redirect(`/listing/${id}`);
  } catch (err) {
    next(err);
  }
});

router.get('/account/saved', requireLogin, async (req, res, next) => {
  try {
    const { rows } = await pool.query(
      `SELECT listings.*, categories.name AS category_name, users.avatar_key
       FROM saved_listings
       JOIN listings ON listings.id = saved_listings.listing_id
       LEFT JOIN categories ON categories.id = listings.category_id
       LEFT JOIN users ON users.id = listings.user_id
       WHERE saved_listings.user_id = $1
       ORDER BY saved_listings.created_at DESC`,
      [req.session.userId]
    );
    const listings = await Promise.all(rows.map(async (row) => ({
      ...row,
      avatar_url: row.avatar_key ? await getSignedDownloadUrl(row.avatar_key, 3600) : null,
    })));
    res.render('account/saved', { title: 'My Saved Attorneys', listings });
  } catch (err) {
    next(err);
  }
});

module.exports = router;
