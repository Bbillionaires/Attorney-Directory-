require('dotenv').config();
const fs = require('fs');
const path = require('path');
const { pool } = require('../src/db');

const DEFAULT_CATEGORIES = [
  'General Practice',
  'Real Estate Law',
  'Business Law',
  'Financial & Tax',
  'Employment Law',
  'Estate Planning',
  'Family Law',
  'Criminal Defense',
  'Personal Injury',
  'Immigration Law',
  'Bankruptcy and Debt',
];

async function main() {
  const schema = fs.readFileSync(path.join(__dirname, '..', 'migrations', 'schema.sql'), 'utf8');
  await pool.query(schema);

  for (const name of DEFAULT_CATEGORIES) {
    await pool.query(
      'INSERT INTO categories (name) VALUES ($1) ON CONFLICT (name) DO NOTHING',
      [name]
    );
  }

  console.log('Migration complete.');
  await pool.end();
}

main().catch((err) => {
  console.error(err);
  process.exit(1);
});
