require('dotenv').config();

const path = require('path');
const express = require('express');
const methodOverride = require('method-override');
const session = require('express-session');
const pgSession = require('connect-pg-simple')(session);

const { pool } = require('./src/db');
const homeRoutes = require('./src/routes/home');
const publicRoutes = require('./src/routes/public');
const adminRoutes = require('./src/routes/admin');
const authRoutes = require('./src/routes/auth');
const attorneyRoutes = require('./src/routes/attorney');
const questionRoutes = require('./src/routes/questions');
const contractRoutes = require('./src/routes/contracts');
const reviewRoutes = require('./src/routes/reviews');
const leadRoutes = require('./src/routes/leads');
const accountRoutes = require('./src/routes/account');
const intakeRoutes = require('./src/routes/intake');
const savedListingsRoutes = require('./src/routes/savedListings');
const newsletterRoutes = require('./src/routes/newsletter');
const pagesRoutes = require('./src/routes/pages');
const mapRoutes = require('./src/routes/map');
const stripeWebhookRoutes = require('./src/routes/stripeWebhook');
const { adminAuth } = require('./src/adminAuth');
const { attachCurrentUser } = require('./src/middleware/auth');
const { attachCsrfToken } = require('./src/middleware/csrf');

const sessionSecret = process.env.SESSION_SECRET;
if (!sessionSecret) {
  console.error('SESSION_SECRET is not set; refusing to start.');
  process.exit(1);
}

const app = express();
const BUILD_ID = Date.now();

app.set('trust proxy', 1);
app.set('view engine', 'ejs');
app.set('views', path.join(__dirname, 'views'));

app.get('/healthz', (req, res) => res.json({ ok: true }));

// Stripe webhook needs the untouched raw body for signature verification,
// so this must be registered before express.urlencoded() consumes the stream.
app.use('/webhooks', stripeWebhookRoutes);

app.use(express.urlencoded({ extended: true }));
app.use(methodOverride('_method'));
app.use(express.static(path.join(__dirname, 'public')));

app.use(session({
  store: new pgSession({ pool, tableName: 'session' }),
  secret: sessionSecret,
  resave: false,
  saveUninitialized: false,
  cookie: {
    httpOnly: true,
    sameSite: 'lax',
    secure: process.env.NODE_ENV === 'production',
    maxAge: 30 * 24 * 60 * 60 * 1000,
  },
}));

app.use((req, res, next) => {
  res.locals.turnstileSiteKey = process.env.TURNSTILE_SITE_KEY || '';
  res.locals.buildId = BUILD_ID;
  next();
});
app.use(attachCurrentUser);
app.use(attachCsrfToken);

app.use('/', homeRoutes);
app.use('/', publicRoutes);
app.use('/', authRoutes);
app.use('/', questionRoutes);
app.use('/', contractRoutes);
app.use('/', reviewRoutes);
app.use('/', leadRoutes);
app.use('/', accountRoutes);
app.use('/', intakeRoutes);
app.use('/', savedListingsRoutes);
app.use('/', newsletterRoutes);
app.use('/', pagesRoutes);
app.use('/', mapRoutes);
app.use('/attorney', attorneyRoutes);
app.use('/admin', adminAuth, adminRoutes);

app.use((req, res) => res.status(404).render('404'));

app.use((err, req, res, next) => {
  console.error(err);
  res.status(500).send('Something went wrong.');
});

const port = process.env.PORT || 3000;
app.listen(port, () => {
  console.log(`Attorney Menu listening on port ${port}`);
});
