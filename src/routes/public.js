const express = require('express');
const { pool } = require('../db');

const router = express.Router();

router.get('/', async (req, res, next) => {
  try {
    const q = (req.query.q || '').trim();
    const category = (req.query.category || '').trim();

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

    const { rows: listings } = await pool.query(
      `SELECT listings.*, categories.name AS category_name
       FROM listings
       LEFT JOIN categories ON categories.id = listings.category_id
       WHERE ${conditions.join(' AND ')}
       ORDER BY listings.name ASC`,
      params
    );

    const { rows: categories } = await pool.query('SELECT * FROM categories ORDER BY name ASC');

    res.render('index', { title: 'Browse', listings, categories, query: { q, category } });
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
      `SELECT listings.*, categories.name AS category_name
       FROM listings
       LEFT JOIN categories ON categories.id = listings.category_id
       WHERE listings.id = $1`,
      [id]
    );
    const listing = rows[0];
    if (!listing) {
      return res.status(404).render('404');
    }
    res.render('listing', { title: listing.name, listing });
  } catch (err) {
    next(err);
  }
});

module.exports = router;
