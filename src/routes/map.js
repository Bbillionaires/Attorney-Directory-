const fs = require('fs');
const path = require('path');
const express = require('express');
const { pool } = require('../db');
const { US_STATE_NAMES } = require('../config/usStates');

const router = express.Router();

// Read once at startup — the clickable <a href="/map/state/XX"> / <a href="/map/country/...">
// links wrapping each region need the SVGs inlined into the page (an <img> can't carry live links).
const US_MAP_SVG = fs.readFileSync(path.join(__dirname, '..', '..', 'public', 'us-map.svg'), 'utf8');
const WORLD_MAP_SVG = fs.readFileSync(path.join(__dirname, '..', '..', 'public', 'world-map.svg'), 'utf8');

function groupByCityThenCategory(rows) {
  const cityMap = new Map();
  for (const l of rows) {
    const city = l.city || 'Other';
    if (!cityMap.has(city)) cityMap.set(city, new Map());
    const catMap = cityMap.get(city);
    const category = l.category_name || 'General Practice';
    if (!catMap.has(category)) catMap.set(category, []);
    catMap.get(category).push(l);
  }
  return Array.from(cityMap.entries())
    .sort((a, b) => a[0].localeCompare(b[0]))
    .map(([city, catMap]) => ({
      city,
      categories: Array.from(catMap.entries()).map(([category, firms]) => ({ category, firms })),
    }));
}

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
      usMapSvg: US_MAP_SVG,
      worldMapSvg: WORLD_MAP_SVG,
    });
  } catch (err) {
    next(err);
  }
});

router.get('/map/state/:code', async (req, res, next) => {
  try {
    const code = (req.params.code || '').trim().toUpperCase();
    const stateName = US_STATE_NAMES[code];
    if (!stateName) {
      return res.status(404).render('404');
    }

    const { rows } = await pool.query(
      `SELECT listings.id, listings.name, listings.city, listings.description, listings.is_verified, listings.user_id,
              categories.name AS category_name
       FROM listings
       LEFT JOIN categories ON categories.id = listings.category_id
       WHERE listings.active = true AND listings.state = $1
       ORDER BY listings.city ASC, categories.name ASC NULLS LAST, listings.name ASC`,
      [code]
    );

    res.render('map-region', {
      title: `Attorneys in ${stateName}`,
      regionName: stateName,
      cities: groupByCityThenCategory(rows),
      total: rows.length,
    });
  } catch (err) {
    next(err);
  }
});

router.get('/map/country/:name', async (req, res, next) => {
  try {
    const countryName = (req.params.name || '').trim();
    if (!countryName) {
      return res.status(404).render('404');
    }

    const { rows } = await pool.query(
      `SELECT listings.id, listings.name, listings.city, listings.description, listings.is_verified, listings.user_id,
              categories.name AS category_name
       FROM listings
       LEFT JOIN categories ON categories.id = listings.category_id
       WHERE listings.active = true AND listings.country = $1
       ORDER BY listings.city ASC, categories.name ASC NULLS LAST, listings.name ASC`,
      [countryName]
    );

    res.render('map-region', {
      title: `Attorneys in ${countryName}`,
      regionName: countryName,
      cities: groupByCityThenCategory(rows),
      total: rows.length,
    });
  } catch (err) {
    next(err);
  }
});

module.exports = router;
