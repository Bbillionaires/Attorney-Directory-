const express = require('express');
const crypto = require('crypto');
const { pool } = require('../db');
const { requireLogin } = require('../middleware/auth');
const { verifyCsrfToken } = require('../middleware/csrf');
const { uploadImage } = require('../lib/upload');
const { uploadObject, getSignedDownloadUrl } = require('../lib/r2');

const router = express.Router();

router.get('/account', requireLogin, async (req, res, next) => {
  try {
    const { rows } = await pool.query('SELECT id, email, role, avatar_key FROM users WHERE id = $1', [req.session.userId]);
    const user = rows[0];
    const avatarUrl = user.avatar_key ? await getSignedDownloadUrl(user.avatar_key, 3600) : null;
    res.render('account', { title: 'My account', user, avatarUrl, error: null });
  } catch (err) {
    next(err);
  }
});

router.post('/account/avatar', requireLogin, uploadImage.single('avatar'), verifyCsrfToken, async (req, res, next) => {
  try {
    if (!req.file) {
      const { rows } = await pool.query('SELECT id, email, role, avatar_key FROM users WHERE id = $1', [req.session.userId]);
      const user = rows[0];
      const avatarUrl = user.avatar_key ? await getSignedDownloadUrl(user.avatar_key, 3600) : null;
      return res.status(400).render('account', { title: 'My account', user, avatarUrl, error: 'Please choose an image to upload.' });
    }

    const key = `avatars/${req.session.userId}/${crypto.randomUUID()}`;
    await uploadObject(key, req.file.buffer, req.file.mimetype);
    await pool.query('UPDATE users SET avatar_key = $1 WHERE id = $2', [key, req.session.userId]);

    res.redirect('/account');
  } catch (err) {
    next(err);
  }
});

module.exports = router;
