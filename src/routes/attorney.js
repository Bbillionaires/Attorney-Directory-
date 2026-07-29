const express = require('express');
const crypto = require('crypto');
const { pool } = require('../db');
const { requireRole } = require('../middleware/auth');
const { verifyCsrfToken } = require('../middleware/csrf');
const { upload } = require('../lib/upload');
const { uploadObject } = require('../lib/r2');

const router = express.Router();

router.use(requireRole('attorney'));

async function getOwnListing(userId) {
  const { rows } = await pool.query('SELECT * FROM listings WHERE user_id = $1', [userId]);
  return rows[0] || null;
}

router.get('/profile', async (req, res, next) => {
  try {
    const listing = await getOwnListing(req.session.userId);
    const { rows: categories } = await pool.query('SELECT * FROM categories ORDER BY name ASC');
    res.render('attorney/complete-profile', {
      title: 'My profile',
      listing: listing || {
        name: '', description: '', category_id: '', phone: '', email: '',
        website: '', address: '', city: '', state: '', active: true, question_price_cents: 0,
      },
      categories,
      error: null,
    });
  } catch (err) {
    next(err);
  }
});

router.post('/profile', verifyCsrfToken, async (req, res, next) => {
  try {
    const body = req.body;
    const name = (body.name || '').trim();
    if (!name) {
      const { rows: categories } = await pool.query('SELECT * FROM categories ORDER BY name ASC');
      return res.status(400).render('attorney/complete-profile', {
        title: 'My profile', listing: body, categories, error: 'Name is required.',
      });
    }
    const fields = {
      name,
      description: (body.description || '').trim(),
      category_id: body.category_id ? Number(body.category_id) : null,
      phone: (body.phone || '').trim(),
      email: (body.email || '').trim(),
      website: (body.website || '').trim(),
      address: (body.address || '').trim(),
      city: (body.city || '').trim(),
      state: (body.state || '').trim().toUpperCase(),
      active: body.active === '1',
      question_price_cents: Math.max(0, Math.round(Number(body.question_price_dollars || 0) * 100)) || 0,
    };

    const existing = await getOwnListing(req.session.userId);
    if (existing) {
      await pool.query(
        `UPDATE listings SET name=$1, description=$2, category_id=$3, phone=$4, email=$5,
         website=$6, address=$7, active=$8, question_price_cents=$9, city=$10, state=$11 WHERE id=$12`,
        [fields.name, fields.description, fields.category_id, fields.phone, fields.email,
         fields.website, fields.address, fields.active, fields.question_price_cents, fields.city, fields.state, existing.id]
      );
    } else {
      await pool.query(
        `INSERT INTO listings (name, description, category_id, phone, email, website, address, active, question_price_cents, city, state, user_id)
         VALUES ($1,$2,$3,$4,$5,$6,$7,$8,$9,$10,$11,$12)`,
        [fields.name, fields.description, fields.category_id, fields.phone, fields.email,
         fields.website, fields.address, fields.active, fields.question_price_cents, fields.city, fields.state, req.session.userId]
      );
    }
    res.redirect('/attorney/dashboard');
  } catch (err) {
    next(err);
  }
});

router.get('/dashboard', async (req, res, next) => {
  try {
    const listing = await getOwnListing(req.session.userId);
    if (!listing) return res.redirect('/attorney/profile');

    const { rows: questions } = await pool.query(
      `SELECT * FROM questions WHERE listing_id = $1 AND status IN ('paid','answered') ORDER BY created_at DESC`,
      [listing.id]
    );
    const { rows: contractRows } = await pool.query('SELECT * FROM contracts WHERE listing_id = $1', [listing.id]);

    res.render('attorney/dashboard', {
      title: 'Dashboard',
      listing,
      questions,
      contract: contractRows[0] || null,
    });
  } catch (err) {
    next(err);
  }
});

router.get('/questions/:id', async (req, res, next) => {
  try {
    const id = Number(req.params.id);
    if (!Number.isInteger(id)) return res.status(404).render('404');

    const listing = await getOwnListing(req.session.userId);
    if (!listing) return res.redirect('/attorney/profile');

    const { rows } = await pool.query(
      `SELECT questions.*, users.email AS asker_email FROM questions
       JOIN users ON users.id = questions.user_id
       WHERE questions.id = $1 AND questions.listing_id = $2 AND questions.status IN ('paid','answered')`,
      [id, listing.id]
    );
    const question = rows[0];
    if (!question) return res.status(404).render('404');

    res.render('attorney/question', { title: 'Question', question });
  } catch (err) {
    next(err);
  }
});

router.post('/questions/:id/answer', verifyCsrfToken, async (req, res, next) => {
  try {
    const id = Number(req.params.id);
    if (!Number.isInteger(id)) return res.status(404).render('404');

    const listing = await getOwnListing(req.session.userId);
    if (!listing) return res.redirect('/attorney/profile');

    const answerText = (req.body.answer_text || '').trim();
    if (!answerText) return res.redirect(`/attorney/questions/${id}`);

    await pool.query(
      `UPDATE questions SET answer_text = $1, status = 'answered', answered_at = now()
       WHERE id = $2 AND listing_id = $3 AND status = 'paid'`,
      [answerText, id, listing.id]
    );
    res.redirect(`/attorney/questions/${id}`);
  } catch (err) {
    next(err);
  }
});

router.get('/contract', async (req, res, next) => {
  try {
    const listing = await getOwnListing(req.session.userId);
    if (!listing) return res.redirect('/attorney/profile');

    const { rows } = await pool.query('SELECT * FROM contracts WHERE listing_id = $1', [listing.id]);
    res.render('attorney/contract-form', {
      title: 'My contract',
      contract: rows[0] || { title: '', description: '', price_cents: 0, active: true },
      error: null,
    });
  } catch (err) {
    next(err);
  }
});

router.post('/contract', upload.single('file'), verifyCsrfToken, async (req, res, next) => {
  try {
    const listing = await getOwnListing(req.session.userId);
    if (!listing) return res.redirect('/attorney/profile');

    const title = (req.body.title || '').trim();
    const description = (req.body.description || '').trim();
    const priceCents = Math.max(0, Math.round(Number(req.body.price_dollars || 0) * 100)) || 0;
    const active = req.body.active === '1';

    const { rows: existingRows } = await pool.query('SELECT * FROM contracts WHERE listing_id = $1', [listing.id]);
    const existing = existingRows[0];

    if (!title || (!existing && !req.file)) {
      return res.status(400).render('attorney/contract-form', {
        title: 'My contract',
        contract: { ...req.body, price_cents: priceCents, active },
        error: 'Title is required, and a file is required the first time you list a contract.',
      });
    }

    let fileKey = existing ? existing.file_key : null;
    let fileName = existing ? existing.file_name : null;
    let fileContentType = existing ? existing.file_content_type : null;

    if (req.file) {
      fileKey = `contracts/${listing.id}/${crypto.randomUUID()}`;
      fileName = req.file.originalname;
      fileContentType = req.file.mimetype;
      await uploadObject(fileKey, req.file.buffer, fileContentType);
    }

    if (existing) {
      await pool.query(
        `UPDATE contracts SET title=$1, description=$2, price_cents=$3, active=$4,
         file_key=$5, file_name=$6, file_content_type=$7 WHERE id=$8`,
        [title, description, priceCents, active, fileKey, fileName, fileContentType, existing.id]
      );
    } else {
      await pool.query(
        `INSERT INTO contracts (listing_id, title, description, price_cents, file_key, file_name, file_content_type, active)
         VALUES ($1,$2,$3,$4,$5,$6,$7,$8)`,
        [listing.id, title, description, priceCents, fileKey, fileName, fileContentType, active]
      );
    }
    res.redirect('/attorney/dashboard');
  } catch (err) {
    next(err);
  }
});

module.exports = router;
