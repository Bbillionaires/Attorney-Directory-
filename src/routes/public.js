const express = require('express');
const { pool } = require('../db');
const { getSignedDownloadUrl } = require('../lib/r2');

const router = express.Router();

const US_STATES = [
  'AL', 'AK', 'AZ', 'AR', 'CA', 'CO', 'CT', 'DE', 'DC', 'FL', 'GA', 'HI', 'ID', 'IL', 'IN', 'IA',
  'KS', 'KY', 'LA', 'ME', 'MD', 'MA', 'MI', 'MN', 'MS', 'MO', 'MT', 'NE', 'NV', 'NH', 'NJ', 'NM',
  'NY', 'NC', 'ND', 'OH', 'OK', 'OR', 'PA', 'RI', 'SC', 'SD', 'TN', 'TX', 'UT', 'VT', 'VA', 'WA',
  'WV', 'WI', 'WY',
];

async function withAvatarUrl(row) {
  if (!row.avatar_key) return { ...row, avatar_url: null };
  return { ...row, avatar_url: await getSignedDownloadUrl(row.avatar_key, 3600) };
}

router.get('/directory', async (req, res, next) => {
  try {
    const q = (req.query.q || '').trim();
    const category = (req.query.category || '').trim();
    const city = (req.query.city || '').trim();
    const state = (req.query.state || '').trim();

    const conditions = ['listings.active = true'];
    const params = [];

    if (q) {
      params.push(`%${q}%`);
      conditions.push(`(listings.name ILIKE $${params.length} OR listings.description ILIKE $${params.length})`);
    }
    if (category) {
      params.push(category);
      conditions.push(`listings.category_id = $${params.length}`);
    }
    if (city) {
      params.push(`%${city}%`);
      conditions.push(`listings.city ILIKE $${params.length}`);
    }
    if (state) {
      params.push(state);
      conditions.push(`listings.state = $${params.length}`);
    }

    const { rows: listingRows } = await pool.query(
      `SELECT listings.*, categories.name AS category_name, users.avatar_key
       FROM listings
       LEFT JOIN categories ON categories.id = listings.category_id
       LEFT JOIN users ON users.id = listings.user_id
       WHERE ${conditions.join(' AND ')}
       ORDER BY listings.name ASC`,
      params
    );
    const listings = await Promise.all(listingRows.map(withAvatarUrl));

    const { rows: categories } = await pool.query('SELECT * FROM categories ORDER BY name ASC');

    res.render('directory', { title: 'Attorney Directory', listings, categories, states: US_STATES, query: { q, category, city, state } });
  } catch (err) {
    next(err);
  }
});

router.get('/listing/:id', async (req, res, next) => {
  try {
    const id = Number(req.params.id);
    if (!Number.isInteger(id)) {
      return res.status(404).render('404');
    }
    const { rows } = await pool.query(
      `SELECT listings.*, categories.name AS category_name, users.avatar_key
       FROM listings
       LEFT JOIN categories ON categories.id = listings.category_id
       LEFT JOIN users ON users.id = listings.user_id
       WHERE listings.id = $1`,
      [id]
    );
    const listingRow = rows[0];
    if (!listingRow) {
      return res.status(404).render('404');
    }
    const listing = await withAvatarUrl(listingRow);

    const { rows: contractRows } = await pool.query(
      'SELECT * FROM contracts WHERE listing_id = $1 AND active = true', [id]
    );
    const { rows: reviewRows } = await pool.query(
      `SELECT reviews.*, users.email AS reviewer_email, users.avatar_key FROM reviews
       JOIN users ON users.id = reviews.user_id
       WHERE reviews.listing_id = $1 ORDER BY reviews.created_at DESC`,
      [id]
    );
    const reviews = await Promise.all(reviewRows.map(withAvatarUrl));
    const averageRating = reviews.length
      ? reviews.reduce((sum, r) => sum + r.rating, 0) / reviews.length
      : null;

    let isSaved = false;
    if (req.session.userId) {
      const { rows: savedRows } = await pool.query(
        'SELECT 1 FROM saved_listings WHERE user_id = $1 AND listing_id = $2',
        [req.session.userId, id]
      );
      isSaved = savedRows.length > 0;
    }

    res.render('listing', {
      title: listing.name,
      listing,
      contract: contractRows[0] || null,
      reviews,
      averageRating,
      isSaved,
    });
  } catch (err) {
    next(err);
  }
});

module.exports = router;
