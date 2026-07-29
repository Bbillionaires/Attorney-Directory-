const express = require('express');

const router = express.Router();

const STATIC_PAGES = [
  { path: '/how-it-works', view: 'pages/how-it-works', title: 'How It Works' },
  { path: '/resources', view: 'pages/resources', title: 'Legal Resources' },
  { path: '/for-attorneys', view: 'pages/for-attorneys', title: 'For Attorneys' },
  { path: '/contact', view: 'pages/contact', title: 'Contact' },
  { path: '/privacy', view: 'pages/privacy', title: 'Privacy Policy' },
  { path: '/terms', view: 'pages/terms', title: 'Terms of Use' },
  { path: '/advertising-disclosure', view: 'pages/advertising-disclosure', title: 'Advertising Disclosure' },
  { path: '/attorney-disclaimer', view: 'pages/attorney-disclaimer', title: 'Attorney Disclaimer' },
];

STATIC_PAGES.forEach(({ path, view, title }) => {
  router.get(path, (req, res) => res.render(view, { title }));
});

module.exports = router;
