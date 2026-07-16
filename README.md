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

## Deployment

The included `Dockerfile` runs migrations then starts the app, reading `DATABASE_URL` from the environment (set this to your Postgres connection string, e.g. from Render). Also set `ADMIN_USER` and `ADMIN_PASSWORD` in your hosting environment's env vars before deploying, since `/admin` is unusable without them.
