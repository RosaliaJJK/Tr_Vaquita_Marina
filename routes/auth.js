// routes/auth.js
const express = require('express');
const router = express.Router();
const db = require('../config/db'); // usa tu wrapper con .query()

// GET vistas
router.get('/login', (req, res) => {
  res.render('login', {
    error: req.session.error_message || null,
    success: req.session.success_message || null
  });
  req.session.error_message = null;
  req.session.success_message = null;
});

router.get('/register', (req, res) => {
  res.render('login', { // mostramos login.ejs que tiene registro y login juntos
    error: req.session.error_message || null,
    success: req.session.success_message || null
  });
  req.session.error_message = null;
  req.session.success_message = null;
});

// POST /auth/register
router.post('/register', async (req, res) => {
  try {
    const { usuario, contrasena } = req.body;
    if (!usuario || !contrasena) {
      req.session.error_message = 'Faltan datos';
      return res.redirect('/auth/login');
    }

    // Verificar si usuario ya existe
    const [exists] = await db.query(
      'SELECT id FROM usuarios WHERE nombre_usuario = ?', 
      [usuario]
    );
    if (exists.length > 0) {
      req.session.error_message = 'El nombre de usuario ya está registrado';
      return res.redirect('/auth/login');
    }

    // Guardar contraseña tal cual (solo práctica)
    await db.query(
      'INSERT INTO usuarios (nombre_usuario, contrasena_hash) VALUES (?, ?)', 
      [usuario, contrasena]
    );

    req.session.success_message = 'Registro exitoso. Ahora inicia sesión';
    return res.redirect('/auth/login');
  } catch (err) {
    console.error('Error register:', err);
    req.session.error_message = 'Error en servidor';
    return res.redirect('/auth/login');
  }
});

// POST /auth/login
router.post('/login', async (req, res) => {
  try {
    const { usuario, contrasena } = req.body;

    const [rows] = await db.query(
      'SELECT * FROM usuarios WHERE nombre_usuario = ?',
      [usuario]
    );

    if (rows.length === 0) {
      req.session.error_message = 'Usuario no existe';
      return res.redirect('/auth/login');
    }

    const user = rows[0];

    // Comparación simple de contraseña
    if (contrasena !== user.contrasena_hash) {
      req.session.error_message = 'Contraseña incorrecta';
      return res.redirect('/auth/login');
    }

    req.session.user = user;
    return res.redirect('/habitat');

  } catch(err){
    console.error('Error login:', err);
    req.session.error_message = 'Error en servidor';
    return res.redirect('/auth/login');
  }
});

// Logout
router.get('/logout', (req, res) => {
  req.session.destroy(() => res.redirect('/auth/login'));
});

module.exports = router;
