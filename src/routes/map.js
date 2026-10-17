const fs = require('fs');
const path = require('path');
const express = require('express');
const { pool } = require('../db');
const { US_STATE_NAMES } = require('../config/usStates');

const router = express.Router();

// A bulk-imported bar roster can put tens of thousands of listings in a single
// state/country. Rendering that many absolutely-positioned map markers (or
// that many listing cards on a drill-down page) would be a browser-crashing
// amount of DOM. MARKER_LIMIT caps the world map to a representative sample;
// the state/country drill-down pages never render individual listings at
// all — they show per-city counts that link into the already-paginated
// /directory with the right filters pre-applied.
const MARKER_LIMIT = 2000;

// Read once at startup — the clickable <a href="/map/state/XX"> / <a href="/map/country/...">
// links wrapping each region need the SVGs inlined into the page (an <img> can't carry live links).
const US_MAP_SVG = fs.readFileSync(path.join(__dirname, '..', '..', 'public', 'us-map.svg'), 'utf8');
const WORLD_MAP_SVG = fs.readFileSync(path.join(__dirname, '..', '..', 'public', 'world-map.svg'), 'utf8');

function cityGroupsFromCounts(rows) {
  const cityMap = new Map();
  for (const r of rows) {
    const city = r.city || 'Other';
    if (!cityMap.has(city)) cityMap.set(city, { city, total: 0, categories: [] });
    const group = cityMap.get(city);
    group.total += Number(r.count);
    group.categories.push({
      categoryId: r.category_id,
      category: r.category_name || 'General Practice',
      count: Number(r.count),
    });
  }
  return Array.from(cityMap.values()).sort((a, b) => b.total - a.total || a.city.localeCompare(b.city));
}

router.get('/map', async (req, res, next) => {
  try {
    const { rows: listings } = await pool.query(
      `SELECT id, name, city, state, country, latitude, longitude, is_verified
       FROM listings
       WHERE active = true AND latitude IS NOT NULL AND longitude IS NOT NULL
       ORDER BY RANDOM()
       LIMIT $1`,
      [MARKER_LIMIT]
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
      markerLimit: MARKER_LIMIT,
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
      `SELECT listings.city, listings.category_id, categories.name AS category_name, COUNT(*) AS count
       FROM listings
       LEFT JOIN categories ON categories.id = listings.category_id
       WHERE listings.active = true AND listings.state = $1
       GROUP BY listings.city, listings.category_id, categories.name
       ORDER BY listings.city ASC`,
      [code]
    );
    const total = rows.reduce((sum, r) => sum + Number(r.count), 0);

    res.render('map-region', {
      title: `Attorneys in ${stateName}`,
      regionName: stateName,
      regionQuery: { state: code },
      cities: cityGroupsFromCounts(rows),
      total,
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
      `SELECT listings.city, listings.category_id, categories.name AS category_name, COUNT(*) AS count
       FROM listings
       LEFT JOIN categories ON categories.id = listings.category_id
       WHERE listings.active = true AND listings.country = $1
       GROUP BY listings.city, listings.category_id, categories.name
       ORDER BY listings.city ASC`,
      [countryName]
    );
    const total = rows.reduce((sum, r) => sum + Number(r.count), 0);

    res.render('map-region', {
      title: `Attorneys in ${countryName}`,
      regionName: countryName,
      regionQuery: { country: countryName },
      cities: cityGroupsFromCounts(rows),
      total,
    });
  } catch (err) {
    next(err);
  }
});

module.exports = router;
