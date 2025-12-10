<?php
session_start();
require_once 'db_config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $username = trim($_POST["usuario"]);
    $password = $_POST["contrasena"];
    $login_error = "";

    // 1. Preparar la consulta SQL para buscar el usuario
    $sql = "SELECT id, nombre_usuario, contrasena_hash FROM usuarios WHERE nombre_usuario = ?";
    
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("s", $param_username);
        $param_username = $username;

        if ($stmt->execute()) {
            $stmt->store_result();

            // 2. Verificar si el usuario existe (debe haber exactamente 1 fila)
            if ($stmt->num_rows == 1) {
                $stmt->bind_result($id, $username_db, $hashed_password);
                $stmt->fetch();
                
                // 3. Verificar la contraseña usando el hash almacenado
                if (password_verify($password, $hashed_password)) {
                    // Contraseña correcta. Iniciar sesión.
                    $_SESSION["loggedin"] = true;
                    $_SESSION["id"] = $id;
                    $_SESSION["username"] = $username_db;
                    
                    // Redirección al éxito
                    header("location: habitat.html");
                    exit();
                } else {
                    // Contraseña incorrecta
                    $login_error = "Contraseña incorrecta. Por favor, revise bien su contraseña.";
                }
            } else {
                // Usuario no existe
                $login_error = "El usuario **$username** no se encuentra registrado. Revise bien si colocó bien su nombre de usuario o regístrese.";
            }
        } else {
            $login_error = "¡Ups! Algo salió mal en el servidor. Inténtelo más tarde.";
        }
        $stmt->close();
    }

    // Si hay un error, guardarlo en sesión y volver al login
    if (!empty($login_error)) {
        $_SESSION['error_message'] = $login_error;
        header("location: login.html");
        exit();
    }
}

$conn->close();
?>