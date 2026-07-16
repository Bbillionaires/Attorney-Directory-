const { Pool } = require('pg');

const connectionString = process.env.DATABASE_URL;
if (!connectionString) {
  throw new Error('Missing DATABASE_URL environment variable');
}

const useSsl = process.env.PGSSL !== 'off';

const pool = new Pool({
  connectionString,
  ssl: useSsl ? { rejectUnauthorized: false } : false,
});

module.exports = { pool };
