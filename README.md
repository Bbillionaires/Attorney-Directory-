# Attorney Menu

The only attorney directory you need to get served. A two-sided legal marketplace: attorneys and clients register accounts, attorneys get discoverable profiles, clients can pay to ask an attorney a question, each attorney can sell one signature contract template, and reviews are gated to clients with a verified relationship (a paid/answered question, or an admin-approved case verification). Litigation-support and lawsuit-settlement-loan requests are simple lead-capture forms routed to admin — not real underwriting.

Built with Node.js, Express, EJS templates, and PostgreSQL, with Stripe (payments), Cloudflare Turnstile (anti-spam), and Cloudflare R2 (private file storage for contract files and ID-verification uploads).

## Local development

```
cp .env.example .env   # fill in DATABASE_URL, ADMIN_USER/PASSWORD, SESSION_SECRET at minimum
npm install
npm run migrate        # creates/updates tables and seeds practice-area categories
npm start
```

The app listens on `PORT` (default `3000`).

- `/` — public directory (browse/search/filter, attorney profiles, reviews, ask-a-question, verify-case)
- `/register`, `/login` — client/attorney account creation and login (session-based, Cloudflare Turnstile required on registration)
- `/attorney/*` — attorney dashboard: edit profile, answer paid questions, manage their one contract for sale
- `/contracts`, `/my/purchases` — browse all contracts for sale; buyer's purchased contracts + downloads
- `/litigation-support`, `/settlement-loan` — lead-capture forms (Turnstile required)
- `/admin` — HTTP Basic Auth (`ADMIN_USER`/`ADMIN_PASSWORD`), moderates listings, case verifications, and leads

### Required environment variables

The app **fails closed** (refuses to start or refuses the specific feature) rather than silently running insecurely when these are missing:

| Variable | Required for | Behavior if missing |
|---|---|---|
| `DATABASE_URL` | everything | app won't start |
| `SESSION_SECRET` | all logged-in features | app won't start |
| `ADMIN_USER` / `ADMIN_PASSWORD` | `/admin` | 500 on `/admin` |
| `STRIPE_SECRET_KEY`, `STRIPE_WEBHOOK_SECRET` | asking questions, buying contracts | 500 when a payment is attempted |
| `TURNSTILE_SITE_KEY`, `TURNSTILE_SECRET_KEY` | registration, lead forms, case verification | 500 on those forms |
| `R2_ACCOUNT_ID`, `R2_BUCKET_NAME`, `R2_ACCESS_KEY_ID`, `R2_SECRET_ACCESS_KEY` | contract file upload, ID document upload | 500 on those uploads |

For local development without real Cloudflare/Stripe/R2 accounts: Cloudflare publishes [documented test keys](https://developers.cloudflare.com/turnstile/troubleshooting/testing/) for Turnstile that always pass (`TURNSTILE_SITE_KEY=1x00000000000000000000AA`, `TURNSTILE_SECRET_KEY=1x0000000000000000000000000000000AA`) — see `.env.example`. Stripe and R2 have no such stand-in; those flows will 500 locally until real (test-mode) credentials are set.

## Deployment (Railway)

The included `Dockerfile` runs migrations then starts the app; `railway.json` points Railway at it and sets up a health check against `/healthz`.

1. Add a PostgreSQL plugin to your Railway project — it provides a `DATABASE_URL` env var automatically.
2. Set every variable in the table above on the app service (`SESSION_SECRET` especially — the app crash-loops without it).
3. Set up a Stripe webhook endpoint pointing at `https://<your-domain>/webhooks/stripe` for the `checkout.session.completed` event, and use its signing secret as `STRIPE_WEBHOOK_SECRET`.
4. Deploy from this repo — Railway will build the Dockerfile, run `npm run migrate`, then `npm start`.

If Railway's Postgres connection rejects SSL (common on private networking), set `PGSSL=off`.
