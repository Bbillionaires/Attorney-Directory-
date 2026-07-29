const express = require('express');
const { pool } = require('../db');
const { getSignedDownloadUrl } = require('../lib/r2');

const router = express.Router();

const EMPTY_LISTING = {
  name: '',
  description: '',
  category_id: '',
  phone: '',
  email: '',
  website: '',
  address: '',
  city: '',
  state: '',
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
    city: (body.city || '').trim(),
    state: (body.state || '').trim().toUpperCase(),
    active: body.active === '1',
  };
}

router.get('/', async (req, res, next) => {
  try {
    const { rows: listings } = await pool.query(
      `SELECT listings.*, categories.name AS category_name, users.bar_number
       FROM listings
       LEFT JOIN categories ON categories.id = listings.category_id
       LEFT JOIN users ON users.id = listings.user_id
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
      `INSERT INTO listings (name, description, category_id, phone, email, website, address, city, state, active)
       VALUES ($1, $2, $3, $4, $5, $6, $7, $8, $9, $10)`,
      [listing.name, listing.description, listing.category_id, listing.phone, listing.email, listing.website, listing.address, listing.city, listing.state, listing.active]
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
       SET name = $1, description = $2, category_id = $3, phone = $4, email = $5, website = $6, address = $7, active = $8, city = $9, state = $10
       WHERE id = $11`,
      [listing.name, listing.description, listing.category_id, listing.phone, listing.email, listing.website, listing.address, listing.active, listing.city, listing.state, id]
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

router.put('/:id/toggle-verified', async (req, res, next) => {
  try {
    const id = parseId(req, res);
    if (id === null) return;

    await pool.query('UPDATE listings SET is_verified = NOT is_verified WHERE id = $1', [id]);
    res.redirect('/admin');
  } catch (err) {
    next(err);
  }
});

router.get('/case-verifications', async (req, res, next) => {
  try {
    const { rows: caseVerifications } = await pool.query(
      `SELECT case_verifications.*, users.email AS user_email, listings.name AS listing_name
       FROM case_verifications
       JOIN users ON users.id = case_verifications.user_id
       JOIN listings ON listings.id = case_verifications.listing_id
       ORDER BY (case_verifications.status = 'pending') DESC, case_verifications.created_at DESC`
    );
    res.render('admin/case-verifications', { title: 'Case Verifications', caseVerifications });
  } catch (err) {
    next(err);
  }
});

router.get('/case-verifications/:id/document', async (req, res, next) => {
  try {
    const id = parseId(req, res);
    if (id === null) return;

    const { rows } = await pool.query('SELECT id_document_key FROM case_verifications WHERE id = $1', [id]);
    if (!rows[0]) return res.status(404).render('404');

    const url = await getSignedDownloadUrl(rows[0].id_document_key, 300);
    res.redirect(url);
  } catch (err) {
    next(err);
  }
});

router.put('/case-verifications/:id/approve', async (req, res, next) => {
  try {
    const id = parseId(req, res);
    if (id === null) return;

    await pool.query(
      `UPDATE case_verifications SET status = 'approved', reviewed_by = $1, reviewed_at = now()
       WHERE id = $2 AND status = 'pending'`,
      [process.env.ADMIN_USER || 'admin', id]
    );
    res.redirect('/admin/case-verifications');
  } catch (err) {
    next(err);
  }
});

router.put('/case-verifications/:id/reject', async (req, res, next) => {
  try {
    const id = parseId(req, res);
    if (id === null) return;

    await pool.query(
      `UPDATE case_verifications SET status = 'rejected', reviewed_by = $1, reviewed_at = now()
       WHERE id = $2 AND status = 'pending'`,
      [process.env.ADMIN_USER || 'admin', id]
    );
    res.redirect('/admin/case-verifications');
  } catch (err) {
    next(err);
  }
});

router.get('/leads', async (req, res, next) => {
  try {
    const { rows: leads } = await pool.query('SELECT * FROM leads ORDER BY created_at DESC');
    res.render('admin/leads', { title: 'Leads', leads });
  } catch (err) {
    next(err);
  }
});

module.exports = router;
