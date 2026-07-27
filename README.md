# Attorney Directory

A directory of attorneys, browsable by practice area and searchable by name/description, with a simple admin panel for managing listings.

Built with Node.js, Express, EJS templates, and PostgreSQL.

## Local development

```
cp .env.example .env   # then edit DATABASE_URL and set ADMIN_USER/ADMIN_PASSWORD
npm install
npm run migrate        # creates tables and seeds practice-area categories
npm start
```

The app listens on `PORT` (default `3000`). Visit `/` for the public directory. `/admin` manages listings and is protected by HTTP Basic Auth using the `ADMIN_USER`/`ADMIN_PASSWORD` environment variables — the app refuses to serve `/admin` if either is unset.

## Deployment (Railway)

The included `Dockerfile` runs migrations then starts the app; `railway.json` points Railway at it and sets up a health check against `/healthz`.

1. Add a PostgreSQL plugin to your Railway project — it provides a `DATABASE_URL` env var automatically.
2. On the app service, set `ADMIN_USER` and `ADMIN_PASSWORD` (the admin panel refuses to serve without them).
3. Deploy from this repo — Railway will build the Dockerfile, run `npm run migrate`, then `npm start`.

If Railway's Postgres connection rejects SSL (common on private networking), set `PGSSL=off`.
