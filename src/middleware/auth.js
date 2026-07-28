const { pool } = require('../db');

async function attachCurrentUser(req, res, next) {
  res.locals.currentUser = null;
  if (req.session.userId) {
    const { rows } = await pool.query('SELECT id, role, email FROM users WHERE id = $1', [req.session.userId]);
    if (rows[0]) {
      res.locals.currentUser = rows[0];
    } else {
      req.session.userId = null;
      req.session.role = null;
    }
  }
  next();
}

function requireLogin(req, res, next) {
  if (!req.session.userId) {
    return res.redirect('/login');
  }
  next();
}

function requireRole(role) {
  return (req, res, next) => {
    if (!req.session.userId || req.session.role !== role) {
      return res.status(403).render('403');
    }
    next();
  };
}

module.exports = { attachCurrentUser, requireLogin, requireRole };
