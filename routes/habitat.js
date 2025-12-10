const express = require('express');
const router = express.Router();

// protege si no hay sesión
function verificarLogin(req,res,next){
    if(!req.session.usuario){
        return res.redirect('/');
    }
    next();
}

router.get('/', verificarLogin , (req,res)=>{
    res.render('habitat');
});

module.exports = router;
