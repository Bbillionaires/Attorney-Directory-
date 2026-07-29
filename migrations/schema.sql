CREATE TABLE IF NOT EXISTS categories (
  id SERIAL PRIMARY KEY,
  name TEXT NOT NULL UNIQUE
);

CREATE TABLE IF NOT EXISTS listings (
  id SERIAL PRIMARY KEY,
  name TEXT NOT NULL,
  description TEXT NOT NULL DEFAULT '',
  category_id INTEGER REFERENCES categories(id) ON DELETE SET NULL,
  phone TEXT NOT NULL DEFAULT '',
  email TEXT NOT NULL DEFAULT '',
  website TEXT NOT NULL DEFAULT '',
  address TEXT NOT NULL DEFAULT '',
  active BOOLEAN NOT NULL DEFAULT true,
  created_at TIMESTAMPTZ NOT NULL DEFAULT now()
);

CREATE INDEX IF NOT EXISTS listings_category_id_idx ON listings (category_id);
CREATE INDEX IF NOT EXISTS listings_active_idx ON listings (active);
ALTER TABLE listings ADD COLUMN IF NOT EXISTS city TEXT NOT NULL DEFAULT '';
ALTER TABLE listings ADD COLUMN IF NOT EXISTS state TEXT NOT NULL DEFAULT '';
CREATE INDEX IF NOT EXISTS listings_city_idx ON listings (city);
CREATE INDEX IF NOT EXISTS listings_state_idx ON listings (state);

-- Accounts
CREATE TABLE IF NOT EXISTS users (
  id SERIAL PRIMARY KEY,
  role TEXT NOT NULL CHECK (role IN ('client', 'attorney')),
  email TEXT NOT NULL UNIQUE,
  password_hash TEXT NOT NULL,
  wins INTEGER,
  losses INTEGER,
  bar_number TEXT,
  created_at TIMESTAMPTZ NOT NULL DEFAULT now(),
  CONSTRAINT attorney_requires_bar_number CHECK (role <> 'attorney' OR bar_number IS NOT NULL)
);
CREATE INDEX IF NOT EXISTS users_role_idx ON users (role);
ALTER TABLE users ADD COLUMN IF NOT EXISTS avatar_key TEXT;

-- Session store (connect-pg-simple reads/writes this table)
CREATE TABLE IF NOT EXISTS "session" (
  "sid" varchar NOT NULL COLLATE "default" PRIMARY KEY,
  "sess" json NOT NULL,
  "expire" timestamp(6) NOT NULL
);
CREATE INDEX IF NOT EXISTS "IDX_session_expire" ON "session" ("expire");

-- Security/audit log
CREATE TABLE IF NOT EXISTS auth_events (
  id SERIAL PRIMARY KEY,
  user_id INTEGER REFERENCES users(id) ON DELETE SET NULL,
  event_type TEXT NOT NULL,
  ip TEXT NOT NULL,
  user_agent TEXT NOT NULL DEFAULT '',
  created_at TIMESTAMPTZ NOT NULL DEFAULT now()
);
CREATE INDEX IF NOT EXISTS auth_events_user_id_idx ON auth_events (user_id);

-- listings becomes attorney-owned
ALTER TABLE listings ADD COLUMN IF NOT EXISTS user_id INTEGER REFERENCES users(id) ON DELETE CASCADE;
ALTER TABLE listings ADD COLUMN IF NOT EXISTS question_price_cents INTEGER NOT NULL DEFAULT 0;
ALTER TABLE listings ADD COLUMN IF NOT EXISTS is_verified BOOLEAN NOT NULL DEFAULT false;
CREATE UNIQUE INDEX IF NOT EXISTS listings_user_id_uidx ON listings (user_id) WHERE user_id IS NOT NULL;

-- Paid Q&A
CREATE TABLE IF NOT EXISTS questions (
  id SERIAL PRIMARY KEY,
  user_id INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
  listing_id INTEGER NOT NULL REFERENCES listings(id) ON DELETE CASCADE,
  price_cents INTEGER NOT NULL,
  stripe_checkout_session_id TEXT,
  stripe_payment_intent_id TEXT,
  status TEXT NOT NULL DEFAULT 'pending_payment' CHECK (status IN ('pending_payment', 'paid', 'answered')),
  question_text TEXT NOT NULL,
  answer_text TEXT,
  created_at TIMESTAMPTZ NOT NULL DEFAULT now(),
  paid_at TIMESTAMPTZ,
  answered_at TIMESTAMPTZ
);
CREATE INDEX IF NOT EXISTS questions_user_id_idx ON questions (user_id);
CREATE INDEX IF NOT EXISTS questions_listing_id_idx ON questions (listing_id);
CREATE UNIQUE INDEX IF NOT EXISTS questions_stripe_session_uidx ON questions (stripe_checkout_session_id) WHERE stripe_checkout_session_id IS NOT NULL;

-- Contracts-for-sale: exactly one per attorney for now
CREATE TABLE IF NOT EXISTS contracts (
  id SERIAL PRIMARY KEY,
  listing_id INTEGER NOT NULL REFERENCES listings(id) ON DELETE CASCADE,
  title TEXT NOT NULL,
  description TEXT NOT NULL DEFAULT '',
  price_cents INTEGER NOT NULL,
  file_key TEXT NOT NULL,
  file_name TEXT NOT NULL,
  file_content_type TEXT NOT NULL,
  active BOOLEAN NOT NULL DEFAULT true,
  created_at TIMESTAMPTZ NOT NULL DEFAULT now()
);
CREATE UNIQUE INDEX IF NOT EXISTS contracts_listing_id_uidx ON contracts (listing_id);

CREATE TABLE IF NOT EXISTS contract_purchases (
  id SERIAL PRIMARY KEY,
  contract_id INTEGER NOT NULL REFERENCES contracts(id) ON DELETE CASCADE,
  user_id INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
  price_cents_paid INTEGER NOT NULL,
  stripe_checkout_session_id TEXT,
  status TEXT NOT NULL DEFAULT 'pending_payment' CHECK (status IN ('pending_payment', 'paid')),
  created_at TIMESTAMPTZ NOT NULL DEFAULT now(),
  paid_at TIMESTAMPTZ
);
CREATE INDEX IF NOT EXISTS contract_purchases_user_id_idx ON contract_purchases (user_id);
CREATE UNIQUE INDEX IF NOT EXISTS contract_purchases_stripe_session_uidx ON contract_purchases (stripe_checkout_session_id) WHERE stripe_checkout_session_id IS NOT NULL;

-- Case verification (review eligibility path #2)
CREATE TABLE IF NOT EXISTS case_verifications (
  id SERIAL PRIMARY KEY,
  user_id INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
  listing_id INTEGER NOT NULL REFERENCES listings(id) ON DELETE CASCADE,
  case_number TEXT NOT NULL,
  case_date DATE NOT NULL,
  county TEXT NOT NULL,
  case_type TEXT NOT NULL CHECK (case_type IN ('civil', 'criminal')),
  id_document_key TEXT NOT NULL,
  status TEXT NOT NULL DEFAULT 'pending' CHECK (status IN ('pending', 'approved', 'rejected')),
  reviewed_by TEXT,
  reviewed_at TIMESTAMPTZ,
  created_at TIMESTAMPTZ NOT NULL DEFAULT now()
);
CREATE INDEX IF NOT EXISTS case_verifications_status_idx ON case_verifications (status);

-- Reviews: gated by a paid+answered question OR an approved case verification
CREATE TABLE IF NOT EXISTS reviews (
  id SERIAL PRIMARY KEY,
  listing_id INTEGER NOT NULL REFERENCES listings(id) ON DELETE CASCADE,
  user_id INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
  rating INTEGER NOT NULL CHECK (rating BETWEEN 1 AND 5),
  body TEXT NOT NULL DEFAULT '',
  source_type TEXT NOT NULL CHECK (source_type IN ('question', 'case_verification')),
  source_id INTEGER NOT NULL,
  created_at TIMESTAMPTZ NOT NULL DEFAULT now()
);
CREATE INDEX IF NOT EXISTS reviews_listing_id_idx ON reviews (listing_id);
CREATE UNIQUE INDEX IF NOT EXISTS reviews_source_question_uidx ON reviews (source_id) WHERE source_type = 'question';
CREATE UNIQUE INDEX IF NOT EXISTS reviews_source_case_uidx ON reviews (source_id) WHERE source_type = 'case_verification';

-- Lead-capture (no payment/processing logic at all)
CREATE TABLE IF NOT EXISTS leads (
  id SERIAL PRIMARY KEY,
  type TEXT NOT NULL CHECK (type IN ('litigation_support', 'settlement_loan')),
  name TEXT NOT NULL,
  email TEXT NOT NULL,
  phone TEXT NOT NULL DEFAULT '',
  case_description TEXT NOT NULL,
  amount_sought_cents INTEGER,
  created_at TIMESTAMPTZ NOT NULL DEFAULT now()
);
CREATE INDEX IF NOT EXISTS leads_type_idx ON leads (type);

-- Category renames to match the homepage's practice-area menu (in-place, preserves listings.category_id FKs)
UPDATE categories SET name = 'Real Estate Law' WHERE name = 'Real Estate';
UPDATE categories SET name = 'Business Law' WHERE name = 'Corporate & Business';
UPDATE categories SET name = 'Estate Planning' WHERE name = 'Wills, Trusts & Estates';
UPDATE categories SET name = 'Immigration Law' WHERE name = 'Immigration';

-- Unified case-intake: Quick Case Review / Attorney Match / Legal Question homepage cards
CREATE TABLE IF NOT EXISTS case_intakes (
  id SERIAL PRIMARY KEY,
  user_id INTEGER REFERENCES users(id) ON DELETE SET NULL,
  source TEXT NOT NULL CHECK (source IN ('quick_case_review', 'attorney_match', 'legal_question')),
  name TEXT NOT NULL,
  email TEXT NOT NULL,
  phone TEXT NOT NULL DEFAULT '',
  category_id INTEGER REFERENCES categories(id) ON DELETE SET NULL,
  city TEXT NOT NULL DEFAULT '',
  state TEXT NOT NULL DEFAULT '',
  description TEXT NOT NULL,
  reference_code TEXT NOT NULL,
  status TEXT NOT NULL DEFAULT 'submitted' CHECK (status IN ('submitted', 'in_review', 'matched', 'closed')),
  ip TEXT NOT NULL DEFAULT '',
  created_at TIMESTAMPTZ NOT NULL DEFAULT now()
);
CREATE UNIQUE INDEX IF NOT EXISTS case_intakes_reference_code_uidx ON case_intakes (reference_code);
CREATE INDEX IF NOT EXISTS case_intakes_user_id_idx ON case_intakes (user_id);
CREATE INDEX IF NOT EXISTS case_intakes_status_idx ON case_intakes (status);

-- Saved / shortlisted attorneys
CREATE TABLE IF NOT EXISTS saved_listings (
  id SERIAL PRIMARY KEY,
  user_id INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
  listing_id INTEGER NOT NULL REFERENCES listings(id) ON DELETE CASCADE,
  created_at TIMESTAMPTZ NOT NULL DEFAULT now()
);
CREATE UNIQUE INDEX IF NOT EXISTS saved_listings_user_listing_uidx ON saved_listings (user_id, listing_id);
CREATE INDEX IF NOT EXISTS saved_listings_listing_id_idx ON saved_listings (listing_id);

-- Newsletter signup (capture-only; no ESP configured yet, so no emails actually send)
CREATE TABLE IF NOT EXISTS newsletter_subscribers (
  id SERIAL PRIMARY KEY,
  email TEXT NOT NULL UNIQUE,
  pref_case_updates BOOLEAN NOT NULL DEFAULT false,
  pref_legal_alerts BOOLEAN NOT NULL DEFAULT false,
  pref_helpful_guides BOOLEAN NOT NULL DEFAULT false,
  unsubscribe_token TEXT NOT NULL,
  unsubscribed_at TIMESTAMPTZ,
  created_at TIMESTAMPTZ NOT NULL DEFAULT now()
);
CREATE UNIQUE INDEX IF NOT EXISTS newsletter_subscribers_token_uidx ON newsletter_subscribers (unsubscribe_token);
