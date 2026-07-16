# Attorney Directory

A directory of attorneys, browsable by practice area and searchable by name/description, with a simple admin panel for managing listings.

Built with Node.js, Express, EJS templates, and PostgreSQL.

## Local development

```
cp .env.example .env   # then edit DATABASE_URL for your local Postgres
npm install
npm run migrate        # creates tables and seeds practice-area categories
npm start
```

The app listens on `PORT` (default `3000`). Visit `/` for the public directory and `/admin` to manage listings.

## Deployment

The included `Dockerfile` runs migrations then starts the app, reading `DATABASE_URL` from the environment (set this to your Postgres connection string, e.g. from Render).
