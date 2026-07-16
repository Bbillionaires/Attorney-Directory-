const crypto = require('crypto');

function safeEqual(a, b) {
  const hashA = crypto.createHash('sha256').update(a).digest();
  const hashB = crypto.createHash('sha256').update(b).digest();
  return crypto.timingSafeEqual(hashA, hashB);
}

function adminAuth(req, res, next) {
  const expectedUser = process.env.ADMIN_USER;
  const expectedPassword = process.env.ADMIN_PASSWORD;

  if (!expectedUser || !expectedPassword) {
    console.error('ADMIN_USER / ADMIN_PASSWORD are not set; refusing admin access.');
    return res.status(500).send('Admin panel is not configured.');
  }

  const header = req.headers.authorization || '';
  const [scheme, encoded] = header.split(' ');

  if (scheme === 'Basic' && encoded) {
    const decoded = Buffer.from(encoded, 'base64').toString('utf8');
    const separatorIndex = decoded.indexOf(':');
    if (separatorIndex !== -1) {
      const user = decoded.slice(0, separatorIndex);
      const password = decoded.slice(separatorIndex + 1);
      if (safeEqual(user, expectedUser) && safeEqual(password, expectedPassword)) {
        return next();
      }
    }
  }

  res.set('WWW-Authenticate', 'Basic realm="Attorney Directory Admin"');
  return res.status(401).send('Authentication required.');
}

module.exports = { adminAuth };
