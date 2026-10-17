// Bulk-imports a state bar's full membership export (e.g. The Florida Bar's
// "Active and Inactive" attorney roster, and its separate registered-paralegal
// roster) as listings. Idempotent: re-running upserts on bar_number, so a
// corrected file or a later refresh is safe to run again.
//
// Geocoding is NOT done per-row here — see scripts/build-geocode-cache.js.
// That must be run first; this script just looks up (city, state) in its
// output and leaves latitude/longitude null for anything not found there.
//
// Usage:
//   node scripts/import-bar-roster.js <geocode-cache.json> attorney <attorneys.csv>
//   node scripts/import-bar-roster.js <geocode-cache.json> paralegal <paralegals.csv>
require('dotenv').config();
const fs = require('fs');
const { parse } = require('csv-parse');
const { pool } = require('../src/db');

const BATCH_SIZE = 500;

// Best-effort mapping from The Florida Bar's "Practice Areas" free-text labels
// to this site's existing category taxonomy. Practice Areas is a comma-
// separated list per attorney; the first token with a mapping wins. Anything
// unmatched (or blank) gets no category rather than being dumped into
// "General Practice" — that category would otherwise balloon to ~115k rows
// of attorneys who simply didn't self-report a specialty.
const PRACTICE_AREA_MAP = {
  'Criminal': 'Criminal Defense',
  'Criminal Appellate': 'Criminal Defense',
  'Family': 'Family Law',
  'Immigration and Nationality': 'Immigration Law',
  'Personal Injury': 'Personal Injury',
  'Real Estate': 'Real Estate Law',
  'Labor and Employment': 'Employment Law',
  'Tax': 'Financial & Tax',
  'Bankruptcy': 'Bankruptcy and Debt',
  'Business Litigation': 'Business Law',
  'Business/Corporate': 'Business Law',
  'International Law: Business': 'Business Law',
  'Elder': 'Estate Planning',
  'Wills, Trusts & Estates': 'Estate Planning',
  'Probate': 'Estate Planning',
};

const ACCOUNT_TYPE_TO_STATUS = {
  Member: 'member',
  Inactive: 'inactive',
  'Authorized House Counsel': 'house_counsel',
};

const PHONE_PLACEHOLDER_VALUES = new Set(['0', '0000000000', '(000) 000-0000']);

function cleanPhone(raw) {
  const value = (raw || '').toString().trim();
  return PHONE_PLACEHOLDER_VALUES.has(value) ? '' : value;
}

function cleanName(parts) {
  return parts.filter(Boolean).map((p) => String(p).trim()).filter(Boolean).join(' ').replace(/\s+/g, ' ').trim();
}

function firstMappedCategory(practiceAreasRaw, categoryNameToId) {
  if (!practiceAreasRaw) return null;
  const tokens = String(practiceAreasRaw).split(',').map((t) => t.trim()).filter(Boolean);
  for (const token of tokens) {
    const mapped = PRACTICE_AREA_MAP[token];
    if (mapped && categoryNameToId[mapped]) return categoryNameToId[mapped];
  }
  return null;
}

function geoFor(city, state, cache) {
  const key = `${(city || '').trim().toUpperCase()}|${(state || '').trim().toUpperCase()}`;
  const hit = cache[key];
  return hit ? { latitude: hit.latitude, longitude: hit.longitude } : { latitude: null, longitude: null };
}

async function fetchCategoryMap() {
  const { rows } = await pool.query('SELECT id, name FROM categories');
  const map = {};
  for (const r of rows) map[r.name] = r.id;
  return map;
}

function buildAttorneyRecord(row, geocodeCache, categoryNameToId) {
  const barNumber = (row['Customer Number'] || '').toString().trim();
  if (!barNumber) return null;

  const name = cleanName([row['First Name'], row['Middle Name'], row['Last Name'], row['Suffix']]);
  if (!name) return null;

  const accountType = (row['Account Type'] || '').trim();
  const { latitude, longitude } = geoFor(row['Mailing City'], row['Mailing State/Province'], geocodeCache);

  return {
    name,
    description: (row['Firm Name'] || '').trim(),
    category_id: firstMappedCategory(row['Practice Areas'], categoryNameToId),
    phone: cleanPhone(row['Phone']),
    email: (row['Work Email'] || '').trim(),
    website: '',
    address: (row['Mailing Street'] || '').trim(),
    city: (row['Mailing City'] || '').trim(),
    state: (row['Mailing State/Province'] || '').trim(),
    country: 'United States',
    latitude,
    longitude,
    bar_number: barNumber,
    membership_status: ACCOUNT_TYPE_TO_STATUS[accountType] || 'member',
    eligible_to_practice: (row['Eligibility'] || '').trim() === 'Eligible to Practice Law in Florida',
    professional_type: 'attorney',
    source: 'roster_import',
  };
}

function buildParalegalRecord(row, geocodeCache) {
  const barNumber = (row['Customer Number'] || '').toString().trim();
  const name = (row['Full Name'] || '').trim();
  if (!barNumber || !name) return null;

  const { latitude, longitude } = geoFor(row['Mailing City'], row['Mailing State/Province'], geocodeCache);

  return {
    name,
    description: (row['Firm Name'] || '').trim(),
    category_id: null,
    phone: cleanPhone(row['Phone']),
    email: (row['Work Email'] || '').trim(),
    website: '',
    address: (row['Mailing Street'] || '').trim(),
    city: (row['Mailing City'] || '').trim(),
    state: (row['Mailing State/Province'] || '').trim(),
    country: 'United States',
    latitude,
    longitude,
    bar_number: barNumber,
    membership_status: 'member',
    eligible_to_practice: true,
    professional_type: 'paralegal',
    source: 'roster_import',
  };
}

const COLUMNS = [
  'name', 'description', 'category_id', 'phone', 'email', 'website', 'address',
  'city', 'state', 'country', 'latitude', 'longitude', 'bar_number',
  'membership_status', 'eligible_to_practice', 'professional_type', 'source', 'active',
];

async function insertBatch(batch) {
  if (!batch.length) return;
  const values = [];
  const placeholders = batch.map((record, i) => {
    const base = i * COLUMNS.length;
    values.push(
      record.name, record.description, record.category_id, record.phone, record.email,
      record.website, record.address, record.city, record.state, record.country,
      record.latitude, record.longitude, record.bar_number, record.membership_status,
      record.eligible_to_practice, record.professional_type, record.source, true
    );
    return `(${COLUMNS.map((_, j) => `$${base + j + 1}`).join(', ')})`;
  });

  await pool.query(
    `INSERT INTO listings (${COLUMNS.join(', ')})
     VALUES ${placeholders.join(', ')}
     ON CONFLICT (lower(bar_number)) WHERE bar_number IS NOT NULL
     DO UPDATE SET
       name = EXCLUDED.name,
       description = EXCLUDED.description,
       category_id = EXCLUDED.category_id,
       phone = EXCLUDED.phone,
       email = EXCLUDED.email,
       address = EXCLUDED.address,
       city = EXCLUDED.city,
       state = EXCLUDED.state,
       country = EXCLUDED.country,
       latitude = EXCLUDED.latitude,
       longitude = EXCLUDED.longitude,
       membership_status = EXCLUDED.membership_status,
       eligible_to_practice = EXCLUDED.eligible_to_practice,
       professional_type = EXCLUDED.professional_type,
       source = EXCLUDED.source`,
    values
  );
}

// The Bar's export has one row per member per "section"/certification, so the
// same bar_number repeats (confirmed: ~11k repeated numbers in the attorney
// file alone). A single multi-row INSERT can't ON CONFLICT against itself, so
// rows are deduped by bar_number in memory first — later rows fill in any
// field the earlier one left blank, rather than just keeping the first seen.
function mergeRecord(existing, incoming) {
  if (!existing) return incoming;
  const merged = { ...existing };
  for (const key of COLUMNS) {
    if (key === 'bar_number') continue;
    const val = incoming[key];
    const isBlank = val === null || val === undefined || val === '';
    if (!isBlank && (merged[key] === null || merged[key] === undefined || merged[key] === '')) {
      merged[key] = val;
    }
  }
  return merged;
}

async function run(csvPath, kind, geocodeCache, categoryNameToId) {
  const parser = fs.createReadStream(csvPath)
    .pipe(parse({ columns: true, relax_quotes: true, skip_empty_lines: true }));

  const byBarNumber = new Map();
  let skipped = 0;
  let rowsSeen = 0;

  for await (const row of parser) {
    rowsSeen += 1;
    const record = kind === 'attorney'
      ? buildAttorneyRecord(row, geocodeCache, categoryNameToId)
      : buildParalegalRecord(row, geocodeCache);

    if (!record) {
      skipped += 1;
      continue;
    }
    byBarNumber.set(record.bar_number, mergeRecord(byBarNumber.get(record.bar_number), record));
  }

  console.log(`${kind}: read ${rowsSeen} rows -> ${byBarNumber.size} unique bar numbers (${skipped} skipped, no bar number/name).`);

  let batch = [];
  let processed = 0;
  let geocoded = 0;
  for (const record of byBarNumber.values()) {
    if (record.latitude !== null) geocoded += 1;
    batch.push(record);
    if (batch.length >= BATCH_SIZE) {
      await insertBatch(batch);
      processed += batch.length;
      batch = [];
      if (processed % 5000 === 0) console.log(`${kind}: ${processed} upserted so far...`);
    }
  }
  if (batch.length) {
    await insertBatch(batch);
    processed += batch.length;
  }

  console.log(`${kind}: done. ${processed} upserted, ${geocoded} had coordinates.`);
}

async function main() {
  const [geocodeCachePath, kind, csvPath] = process.argv.slice(2);
  if (!geocodeCachePath || !kind || !csvPath || !['attorney', 'paralegal'].includes(kind)) {
    console.error('Usage: node scripts/import-bar-roster.js <geocode-cache.json> <attorney|paralegal> <input.csv>');
    process.exit(1);
  }

  const geocodeCache = JSON.parse(fs.readFileSync(geocodeCachePath, 'utf8'));
  const categoryNameToId = await fetchCategoryMap();

  await run(csvPath, kind, geocodeCache, categoryNameToId);
  await pool.end();
}

main().catch((err) => {
  console.error(err);
  process.exit(1);
});
