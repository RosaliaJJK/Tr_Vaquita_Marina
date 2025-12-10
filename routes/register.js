const express = require("express");
const router = express.Router();
const db = require("../config/db"); // Importa correctamente tu wrapper db.query()

// Página registro / login combinada
router.get("/", (req, res) => {
  res.render("login", {
    error: null,
    success: null
  });
});

// Petición registro
router.post("/", async (req, res) => {
  try {
    const { usuario, contrasena } = req.body;

    // Validación simple
    if (!usuario || !contrasena || contrasena.length < 4) {
      return res.render("login", {
        error: "El usuario y la contraseña deben tener mínimo 4 caracteres",
        success: null
      });
    }

    // Verificar si ya existe el usuario
    const [rows] = await db.query(
      "SELECT * FROM usuarios WHERE nombre_usuario=?",
      [usuario]
    );

    if (rows.length > 0) {
      return res.render("login", {
        error: "El nombre de usuario ya está registrado",
        success: null
      });
    }

    // Guardar la contraseña tal cual (solo para práctica)
    await db.query(
      "INSERT INTO usuarios (nombre_usuario, contrasena_hash) VALUES (?, ?)",
      [usuario, contrasena]
    );

    // Mostrar mensaje de éxito en la misma página
    return res.render("login", {
      error: null,
      success: "Registro exitoso. Ahora inicia sesión"
    });

  } catch (err) {
    console.error("Error al registrar usuario:", err);
    return res.render("login", {
      error: "Error en el servidor, intenta de nuevo",
      success: null
    });
  }
});

module.exports = router;
