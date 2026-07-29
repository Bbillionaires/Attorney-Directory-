const express = require('express');
const { pool } = require('../db');

const router = express.Router();

router.get('/map', async (req, res, next) => {
  try {
    const { rows: listings } = await pool.query(
      `SELECT listings.id, listings.name, listings.city, listings.state, listings.country,
              listings.latitude, listings.longitude, listings.is_verified, listings.user_id,
              categories.name AS category_name
       FROM listings
       LEFT JOIN categories ON categories.id = listings.category_id
       WHERE listings.active = true AND listings.latitude IS NOT NULL AND listings.longitude IS NOT NULL
       ORDER BY listings.name ASC`
    );

    const markers = listings.map((l) => ({
      ...l,
      // Equirectangular projection onto the 360x180 world-map.svg viewBox.
      leftPct: ((Number(l.longitude) + 180) / 360) * 100,
      topPct: ((90 - Number(l.latitude)) / 180) * 100,
    }));

    const { rows: countRows } = await pool.query(
      `SELECT COUNT(*) FILTER (WHERE latitude IS NOT NULL) AS placed, COUNT(*) AS total
       FROM listings WHERE active = true`
    );

    res.render('map', {
      title: 'Attorneys Worldwide',
      markers,
      placed: Number(countRows[0].placed),
      total: Number(countRows[0].total),
    });
  } catch (err) {
    next(err);
  }
});

module.exports = router;
