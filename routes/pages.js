// routes/pages.js
const express = require('express');
const router = express.Router();

// middleware protección
function onlyAuth(req, res, next) {
  if (!req.session || !req.session.user) {
    return res.redirect('/login');
  }
  next();
}

// GET /login - render login.ejs (usa mensajes en sesión)
router.get('/login', (req, res) => {
  const error = req.session.error_message || null;
  const success = req.session.success_message || null;
  req.session.error_message = null;
  req.session.success_message = null;
  res.render('login', { error, success });
});

// GET / (redirige a login)
router.get('/', (req, res) => res.redirect('/login'));

// GET /habitat protegido
router.get('/habitat', onlyAuth, (req, res) => {
  res.render('habitat', { user: req.session.user });
});

// GET /caracteristicas protegido
router.get('/caracteristicas', onlyAuth, (req, res) => {
  res.render('caracteristicas', { user: req.session.user });
});

module.exports = router;
