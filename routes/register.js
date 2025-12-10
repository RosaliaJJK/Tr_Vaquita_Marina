const express = require("express");
const router = express.Router();
const { db } = require("../config/db");

// Página registro
router.get("/", (req,res)=>{
    res.render("register");
});

// Peticion registro
router.post("/", async (req,res)=>{

    const {usuario, contrasena} = req.body;

    // Validación simple (opcional)
    if(contrasena.length < 4){
        return res.send("La contraseña debe tener mínimo 4 caracteres");
    }

    // Verificar si ya existe
    const [rows] = await db.query(
        "SELECT * FROM usuarios WHERE nombre_usuario=?",
        [usuario]
    );

    if(rows.length > 0){
        return res.send("El nombre ya está registrado");
    }

    // ← GUARDAMOS LA CONTRASEÑA TAL CUAL SIN BCRYPT
    await db.query(
        "INSERT INTO usuarios (nombre_usuario, contrasena_hash) VALUES (?, ?)",
        [usuario, contrasena]
    )

    res.redirect("/login");
});

module.exports = router;
