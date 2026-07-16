require('dotenv').config();

const path = require('path');
const express = require('express');
const methodOverride = require('method-override');

const publicRoutes = require('./src/routes/public');
const adminRoutes = require('./src/routes/admin');

const app = express();

app.set('view engine', 'ejs');
app.set('views', path.join(__dirname, 'views'));

app.use(express.urlencoded({ extended: true }));
app.use(methodOverride('_method'));
app.use(express.static(path.join(__dirname, 'public')));

app.get('/healthz', (req, res) => res.json({ ok: true }));

app.use('/', publicRoutes);
app.use('/admin', adminRoutes);

app.use((req, res) => res.status(404).render('404'));

app.use((err, req, res, next) => {
  console.error(err);
  res.status(500).send('Something went wrong.');
});

const port = process.env.PORT || 3000;
app.listen(port, () => {
  console.log(`Attorney directory listening on port ${port}`);
});
