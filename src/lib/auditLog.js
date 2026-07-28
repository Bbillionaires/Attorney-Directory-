function getClientIp(req) {
  return req.ip || req.socket.remoteAddress || 'unknown';
}

async function logAuthEvent(pool, { userId = null, eventType, ip, userAgent = '' }) {
  await pool.query(
    'INSERT INTO auth_events (user_id, event_type, ip, user_agent) VALUES ($1, $2, $3, $4)',
    [userId, eventType, ip, userAgent]
  );
}

module.exports = { getClientIp, logAuthEvent };
