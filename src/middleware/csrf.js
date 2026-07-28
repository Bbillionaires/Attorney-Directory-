const crypto = require('crypto');

const MUTATING_METHODS = new Set(['POST', 'PUT', 'PATCH', 'DELETE']);

function attachCsrfToken(req, res, next) {
  if (!req.session.csrfToken) {
    req.session.csrfToken = crypto.randomBytes(32).toString('hex');
  }
  res.locals.csrfToken = req.session.csrfToken;
  next();
}

function verifyCsrfToken(req, res, next) {
  if (!MUTATING_METHODS.has(req.method)) {
    return next();
  }
  const submitted = req.body && req.body._csrf;
  if (!submitted || !req.session.csrfToken || submitted !== req.session.csrfToken) {
    return res.status(403).send('Invalid or missing security token. Please go back and try again.');
  }
  next();
}

module.exports = { attachCsrfToken, verifyCsrfToken };
