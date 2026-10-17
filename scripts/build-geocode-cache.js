// One-time/resumable prep step for bulk imports (e.g. a full state bar
// roster): geocode each UNIQUE (city, state) pair once instead of once per
// row. Nominatim's usage policy caps lookups at ~1/sec, so geocoding every
// individual listing directly would take days for a six-figure dataset;
// geocoding only the distinct cities (usually a few thousand) finishes in
// under a couple of hours and every listing in that city reuses the result.
//
// Usage: node scripts/build-geocode-cache.js <output.json> <input.csv> [<input.csv> ...]
// Re-running with the same output path resumes — already-cached pairs are skipped.
require('dotenv').config();
const fs = require('fs');
const path = require('path');
const { parse } = require('csv-parse');
const { geocodeLocation } = require('../src/lib/geocode');

const DELAY_MS = 1100; // stay under Nominatim's 1 req/sec policy

function sleep(ms) {
  return new Promise((resolve) => setTimeout(resolve, ms));
}

async function collectUniquePairs(csvPaths) {
  const pairs = new Map(); // key: "CITY|STATE" (upper) -> { city, state } (original case)
  for (const csvPath of csvPaths) {
    await new Promise((resolve, reject) => {
      fs.createReadStream(csvPath)
        .pipe(parse({ columns: true, relax_quotes: true, skip_empty_lines: true }))
        .on('data', (row) => {
          const city = (row['Mailing City'] || '').trim();
          const state = (row['Mailing State/Province'] || '').trim();
          if (!city) return;
          const key = `${city.toUpperCase()}|${state.toUpperCase()}`;
          if (!pairs.has(key)) pairs.set(key, { city, state });
        })
        .on('end', resolve)
        .on('error', reject);
    });
  }
  return pairs;
}

async function main() {
  const [outPath, ...csvPaths] = process.argv.slice(2);
  if (!outPath || csvPaths.length === 0) {
    console.error('Usage: node scripts/build-geocode-cache.js <output.json> <input.csv> [<input.csv> ...]');
    process.exit(1);
  }

  let cache = {};
  if (fs.existsSync(outPath)) {
    cache = JSON.parse(fs.readFileSync(outPath, 'utf8'));
    console.log(`Resuming — ${Object.keys(cache).length} pairs already cached.`);
  }

  console.log('Scanning CSVs for unique (city, state) pairs...');
  const pairs = await collectUniquePairs(csvPaths);
  console.log(`Found ${pairs.size} unique pairs.`);

  const todo = [...pairs.entries()].filter(([key]) => !(key in cache));
  console.log(`${todo.length} pairs still need geocoding.`);

  let done = 0;
  for (const [key, { city, state }] of todo) {
    const result = await geocodeLocation({ city, state, country: 'United States' });
    cache[key] = result; // null is a valid cached "no match" — avoids retrying it every resume
    done += 1;

    if (done % 25 === 0 || done === todo.length) {
      fs.writeFileSync(outPath, JSON.stringify(cache, null, 0));
      console.log(`${done}/${todo.length} geocoded (${Object.values(cache).filter(Boolean).length} successful so far)`);
    }
    await sleep(DELAY_MS);
  }

  fs.writeFileSync(outPath, JSON.stringify(cache, null, 0));
  const successCount = Object.values(cache).filter(Boolean).length;
  console.log(`Done. ${successCount}/${Object.keys(cache).length} pairs have coordinates. Cache written to ${outPath}`);
}

main().catch((err) => {
  console.error(err);
  process.exit(1);
});
