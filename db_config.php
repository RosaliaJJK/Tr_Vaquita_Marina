<?php
// Configuración de la conexión a la base de datos
define('DB_SERVER', 'localhost');  // Generalmente 'localhost' en XAMPP/hosting

// *** LÍNEA AGREGADA: DEFINE EL USUARIO DE LA BASE DE DATOS ***
define('DB_USERNAME', 'root'); // ¡REEMPLAZAR! (Para XAMPP suele ser 'root')

define('DB_PASSWORD', '');  // ¡REEMPLAZAR! Tu contraseña de la DB (Para XAMPP suele ser vacío: '')
define('DB_NAME', 'vaquita');     // El nombre de la base de datos que creamos

// Intentar la conexión a la base de datos
// La función mysqli requiere 4 parámetros: servidor, usuario, contraseña y base de datos.
$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Verificar la conexión
if($conn === false){
    die("ERROR: No se pudo conectar a la base de datos. " . $conn->connect_error);
}

// Configurar el juego de caracteres a UTF8 para evitar problemas con acentos y ñ
$conn->set_charset("utf8mb4");
?>