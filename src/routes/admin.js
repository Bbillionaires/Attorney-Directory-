const express = require('express');
const { pool } = require('../db');

const router = express.Router();

const EMPTY_LISTING = {
  name: '',
  description: '',
  category_id: '',
  phone: '',
  email: '',
  website: '',
  address: '',
  active: true,
};

function parseId(req, res) {
  const id = Number(req.params.id);
  if (!Number.isInteger(id)) {
    res.status(404).render('404');
    return null;
  }
  return id;
}

function listingFromBody(body) {
  return {
    name: (body.name || '').trim(),
    description: (body.description || '').trim(),
    category_id: body.category_id ? Number(body.category_id) : null,
    phone: (body.phone || '').trim(),
    email: (body.email || '').trim(),
    website: (body.website || '').trim(),
    address: (body.address || '').trim(),
    active: body.active === '1',
  };
}

router.get('/', async (req, res, next) => {
  try {
    const { rows: listings } = await pool.query(
      `SELECT listings.*, categories.name AS category_name
       FROM listings
       LEFT JOIN categories ON categories.id = listings.category_id
       ORDER BY listings.id DESC`
    );
    res.render('admin/index', { title: 'Admin', listings });
  } catch (err) {
    next(err);
  }
});

router.get('/new', async (req, res, next) => {
  try {
    const { rows: categories } = await pool.query('SELECT * FROM categories ORDER BY name ASC');
    res.render('admin/form', {
      title: 'New listing',
      listing: EMPTY_LISTING,
      categories,
      isEdit: false,
      formAction: '/admin',
    });
  } catch (err) {
    next(err);
  }
});

router.post('/', async (req, res, next) => {
  try {
    const listing = listingFromBody(req.body);
    if (!listing.name) {
      return res.status(400).send('Name is required');
    }
    await pool.query(
      `INSERT INTO listings (name, description, category_id, phone, email, website, address, active)
       VALUES ($1, $2, $3, $4, $5, $6, $7, $8)`,
      [listing.name, listing.description, listing.category_id, listing.phone, listing.email, listing.website, listing.address, listing.active]
    );
    res.redirect('/admin');
  } catch (err) {
    next(err);
  }
});

router.get('/:id/edit', async (req, res, next) => {
  try {
    const id = parseId(req, res);
    if (id === null) return;

    const { rows } = await pool.query('SELECT * FROM listings WHERE id = $1', [id]);
    const listing = rows[0];
    if (!listing) {
      return res.status(404).render('404');
    }

    const { rows: categories } = await pool.query('SELECT * FROM categories ORDER BY name ASC');
    res.render('admin/form', {
      title: 'Edit listing',
      listing,
      categories,
      isEdit: true,
      formAction: `/admin/${id}`,
    });
  } catch (err) {
    next(err);
  }
});

router.put('/:id', async (req, res, next) => {
  try {
    const id = parseId(req, res);
    if (id === null) return;

    const listing = listingFromBody(req.body);
    if (!listing.name) {
      return res.status(400).send('Name is required');
    }
    await pool.query(
      `UPDATE listings
       SET name = $1, description = $2, category_id = $3, phone = $4, email = $5, website = $6, address = $7, active = $8
       WHERE id = $9`,
      [listing.name, listing.description, listing.category_id, listing.phone, listing.email, listing.website, listing.address, listing.active, id]
    );
    res.redirect('/admin');
  } catch (err) {
    next(err);
  }
});

router.put('/:id/toggle-active', async (req, res, next) => {
  try {
    const id = parseId(req, res);
    if (id === null) return;

    await pool.query('UPDATE listings SET active = NOT active WHERE id = $1', [id]);
    res.redirect('/admin');
  } catch (err) {
    next(err);
  }
});

router.delete('/:id', async (req, res, next) => {
  try {
    const id = parseId(req, res);
    if (id === null) return;

    await pool.query('DELETE FROM listings WHERE id = $1', [id]);
    res.redirect('/admin');
  } catch (err) {
    next(err);
  }
});

module.exports = router;
