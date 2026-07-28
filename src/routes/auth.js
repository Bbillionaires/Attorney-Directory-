const express = require('express');
const bcrypt = require('bcrypt');
const { pool } = require('../db');
const { verifyTurnstile } = require('../lib/turnstile');
const { getClientIp, logAuthEvent } = require('../lib/auditLog');
const { verifyCsrfToken } = require('../middleware/csrf');

const router = express.Router();

const EMAIL_RE = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

function toIntOrNull(value) {
  if (value === undefined || value === null || value === '') return null;
  const n = Number(value);
  return Number.isInteger(n) ? n : null;
}

router.get('/register', (req, res) => {
  res.render('register', { title: 'Register', error: null, form: {} });
});

router.post('/register', verifyCsrfToken, async (req, res, next) => {
  try {
    const ip = getClientIp(req);
    const body = req.body;
    const role = body.role === 'attorney' ? 'attorney' : 'client';
    const email = (body.email || '').trim().toLowerCase();
    const password = body.password || '';
    const barNumber = (body.bar_number || '').trim();
    const wins = toIntOrNull(body.wins);
    const losses = toIntOrNull(body.losses);

    const rerender = (error) =>
      res.status(400).render('register', { title: 'Register', error, form: body });

    const turnstileOk = await verifyTurnstile(body['cf-turnstile-response'], ip);
    if (!turnstileOk) {
      await logAuthEvent(pool, { eventType: 'register_failure', ip, userAgent: req.get('user-agent') || '' });
      return rerender('Please complete the security check and try again.');
    }

    if (!EMAIL_RE.test(email)) {
      await logAuthEvent(pool, { eventType: 'register_failure', ip, userAgent: req.get('user-agent') || '' });
      return rerender('Please enter a valid email address.');
    }
    if (password.length < 8) {
      await logAuthEvent(pool, { eventType: 'register_failure', ip, userAgent: req.get('user-agent') || '' });
      return rerender('Password must be at least 8 characters.');
    }
    if (role === 'attorney' && !barNumber) {
      await logAuthEvent(pool, { eventType: 'register_failure', ip, userAgent: req.get('user-agent') || '' });
      return rerender('Bar number is required to register as an attorney.');
    }

    const { rows: existing } = await pool.query('SELECT id FROM users WHERE email = $1', [email]);
    if (existing.length) {
      await logAuthEvent(pool, { eventType: 'register_failure', ip, userAgent: req.get('user-agent') || '' });
      return rerender('An account with that email already exists.');
    }

    const passwordHash = await bcrypt.hash(password, 12);
    const { rows } = await pool.query(
      `INSERT INTO users (role, email, password_hash, wins, losses, bar_number)
       VALUES ($1, $2, $3, $4, $5, $6) RETURNING id, role`,
      [role, email, passwordHash, wins, losses, role === 'attorney' ? barNumber : null]
    );
    const user = rows[0];

    await logAuthEvent(pool, { userId: user.id, eventType: 'register_success', ip, userAgent: req.get('user-agent') || '' });

    req.session.regenerate((err) => {
      if (err) return next(err);
      req.session.userId = user.id;
      req.session.role = user.role;
      req.session.save((saveErr) => {
        if (saveErr) return next(saveErr);
        res.redirect(user.role === 'attorney' ? '/attorney/profile' : '/');
      });
    });
  } catch (err) {
    next(err);
  }
});

router.get('/login', (req, res) => {
  res.render('login', { title: 'Log in', error: null, form: {} });
});

router.post('/login', verifyCsrfToken, async (req, res, next) => {
  try {
    const ip = getClientIp(req);
    const email = (req.body.email || '').trim().toLowerCase();
    const password = req.body.password || '';

    const { rows } = await pool.query('SELECT id, role, password_hash FROM users WHERE email = $1', [email]);
    const user = rows[0];
    const valid = user ? await bcrypt.compare(password, user.password_hash) : false;

    if (!valid) {
      await logAuthEvent(pool, {
        userId: user ? user.id : null,
        eventType: 'login_failure',
        ip,
        userAgent: req.get('user-agent') || '',
      });
      return res.status(400).render('login', { title: 'Log in', error: 'Invalid email or password.', form: { email } });
    }

    await logAuthEvent(pool, { userId: user.id, eventType: 'login_success', ip, userAgent: req.get('user-agent') || '' });

    req.session.regenerate((err) => {
      if (err) return next(err);
      req.session.userId = user.id;
      req.session.role = user.role;
      req.session.save((saveErr) => {
        if (saveErr) return next(saveErr);
        res.redirect(user.role === 'attorney' ? '/attorney/dashboard' : '/');
      });
    });
  } catch (err) {
    next(err);
  }
});

router.post('/logout', verifyCsrfToken, async (req, res, next) => {
  try {
    if (req.session.userId) {
      await logAuthEvent(pool, {
        userId: req.session.userId,
        eventType: 'logout',
        ip: getClientIp(req),
        userAgent: req.get('user-agent') || '',
      });
    }
    req.session.destroy(() => {
      res.clearCookie('connect.sid');
      res.redirect('/');
    });
  } catch (err) {
    next(err);
  }
});

module.exports = router;
