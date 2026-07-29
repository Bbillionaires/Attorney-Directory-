const express = require('express');
const { pool } = require('../db');
const { PRACTICE_AREAS, FEATURED_SERVICES, POPULAR_CHOICES, LEGAL_RESOURCES } = require('../config/practiceAreas');

const router = express.Router();

router.get('/', async (req, res, next) => {
  try {
    const { rows: categories } = await pool.query(
      `SELECT categories.*, COUNT(listings.id) FILTER (WHERE listings.active) AS listing_count
       FROM categories
       LEFT JOIN listings ON listings.category_id = categories.id
       GROUP BY categories.id`
    );
    const byName = new Map(categories.map((c) => [c.name, c]));

    const practiceAreas = PRACTICE_AREAS.map((area) => ({
      ...area,
      category: byName.get(area.categoryName) || null,
    }));
    const featuredServices = FEATURED_SERVICES.map((svc) => ({
      ...svc,
      category: byName.get(svc.categoryName) || null,
    }));

    res.render('index', {
      title: 'Attorney Menu — Legal help, served clearly.',
      practiceAreas,
      featuredServices,
      popularChoices: POPULAR_CHOICES,
      legalResources: LEGAL_RESOURCES,
      newsletterStatus: req.query.newsletter || null,
    });
  } catch (err) {
    next(err);
  }
});

module.exports = router;
