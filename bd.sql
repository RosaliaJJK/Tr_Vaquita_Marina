CREATE DATABASE vaquita;
-- 1. SELECCIÓN DE LA BASE DE DATOS
USE vaquita;

-- 2. ELIMINAR LA TABLA (Y REINICIAR AUTO_INCREMENT si se vuelve a crear)
-- Si la tabla 'usuarios' existe, la borra. Al crearla de nuevo después,
-- el AUTO_INCREMENT comenzará automáticamente en 1.
DROP TABLE IF EXISTS usuarios;

-- 3. CREACIÓN DE LA TABLA DE USUARIOS
CREATE TABLE usuarios (
    -- ID único y auto-incrementable que queremos que inicie en 1
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    
    nombre_usuario VARCHAR(50) UNIQUE NOT NULL,
    contrasena_hash VARCHAR(255) NOT NULL,
    email VARCHAR(100) UNIQUE NULL
);