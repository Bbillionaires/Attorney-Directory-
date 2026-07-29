// Best-effort geocoding for the worldwide attorney map. Uses OpenStreetMap's free
// Nominatim search API (no account/API key required). This is a "nice to have" —
// unlike Stripe/Turnstile/R2, a geocoding failure must never block a listing save,
// it just means that listing won't have coordinates to plot on the map yet.
const NOMINATIM_URL = 'https://nominatim.openstreetmap.org/search';
const USER_AGENT = 'AttorneyMenu/1.0 (https://attorneymenu.com; support@attorneymenu.com)';

async function geocodeLocation({ city, state, country }) {
  const parts = [city, state, country].map((p) => (p || '').trim()).filter(Boolean);
  if (!parts.length) return null;

  const query = parts.join(', ');
  const url = `${NOMINATIM_URL}?format=json&limit=1&q=${encodeURIComponent(query)}`;

  const controller = new AbortController();
  const timeout = setTimeout(() => controller.abort(), 5000);

  try {
    const res = await fetch(url, {
      headers: { 'User-Agent': USER_AGENT, 'Accept-Language': 'en' },
      signal: controller.signal,
    });
    if (!res.ok) return null;

    const results = await res.json();
    const first = results[0];
    if (!first) return null;

    const latitude = Number(first.lat);
    const longitude = Number(first.lon);
    if (!Number.isFinite(latitude) || !Number.isFinite(longitude)) return null;

    return { latitude, longitude };
  } catch (err) {
    return null;
  } finally {
    clearTimeout(timeout);
  }
}

module.exports = { geocodeLocation };
