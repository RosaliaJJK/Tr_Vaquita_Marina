<?php
// Iniciar sesión para poder usar variables de sesión (mensajes de error/éxito)
session_start();

// Incluir el archivo de configuración de la DB
require_once 'db_config.php';

// Variables para almacenar los datos
$username = $password = "";

// 1. Procesar datos del formulario de registro
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $username = trim($_POST["usuario"]);
    $password = $_POST["contrasena"];
    $error = "";

    // 2. Validación de Contraseña Segura (Mínimo 6 caracteres y un símbolo)
    // El patrón revisa: 
    // ^                   Inicio de cadena
    // (?=.*[A-Za-z])      Al menos una letra (mayúscula o minúscula)
    // (?=.*\d)            Al menos un dígito
    // (?=.*[!@#$%^&*])    Al menos un símbolo
    // [A-Za-z\d!@#$%^&*]{6,} Longitud mínima de 6 caracteres
    // $                   Fin de cadena
    if (!preg_match('/^(?=.*[A-Za-z])(?=.*\d)?(?=.*[!@#$%^&*])[\s\S]{6,}$/', $password)) {
        $error = "La contraseña debe tener al menos 6 caracteres y contener al menos un símbolo (!@#$%^&*).";
    }

    // Si no hay errores en la contraseña, procedemos a verificar el usuario
    if (empty($error)) {
        // 3. Verificar si el nombre de usuario ya existe
        $sql = "SELECT id FROM usuarios WHERE nombre_usuario = ?";
        
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("s", $param_username);
            $param_username = $username;

            if ($stmt->execute()) {
                $stmt->store_result();

                if ($stmt->num_rows == 1) {
                    $error = "El nombre de usuario **$username** ya está registrado. Intente con otro.";
                } else {
                    // El usuario es único, podemos continuar con la inserción.
                    $sql_insert = "INSERT INTO usuarios (nombre_usuario, contrasena_hash) VALUES (?, ?)";
                    
                    if ($stmt_insert = $conn->prepare($sql_insert)) {
                        
                        // 4. Crear el HASH seguro de la contraseña
                        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                        
                        $stmt_insert->bind_param("ss", $param_username, $param_hash);
                        $param_hash = $hashed_password;
                        
                        // 5. Ejecutar la inserción
                        if ($stmt_insert->execute()) {
                            // Registro exitoso, redirigir al login
                            $_SESSION['success_message'] = "¡Registro exitoso! Ya puedes iniciar sesión.";
                            header("location: login.html");
                            exit();
                        } else {
                            $error = "Error al intentar registrar el usuario: " . $conn->error;
                        }
                        $stmt_insert->close();
                    }
                }
            } else {
                $error = "¡Ups! Algo salió mal. Por favor, inténtelo más tarde.";
            }
            $stmt->close();
        }
    }
    
    // Si hay un error, lo guardamos en sesión y volvemos al login
    if (!empty($error)) {
        $_SESSION['error_message'] = $error;
        header("location: login.html");
        exit();
    }
}

// Cerrar conexión
$conn->close();
?>