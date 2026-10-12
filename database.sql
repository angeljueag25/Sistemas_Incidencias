CREATE DATABASE IF NOT EXISTS sistema_incidencias;
USE sistema_incidencias;

-- Tabla de usuarios administrativos
CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
);

-- Insertar usuario administrador por defecto (Usuario: admin, Contraseña: admin123)
INSERT INTO usuarios (usuario, password) VALUES 
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- Tabla de incidencias
CREATE TABLE IF NOT EXISTS incidencias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre_reporta VARCHAR(100) NOT NULL,
    laboratorio VARCHAR(50) NOT NULL,
    equipo_afectado VARCHAR(50) NOT NULL,
    descripcion TEXT NOT NULL,
    prioridad ENUM('Baja', 'Media', 'Alta') NOT NULL,
    estado ENUM('Pendiente', 'En proceso', 'Resuelta') DEFAULT 'Pendiente',
    fecha_registro DATETIME DEFAULT CURRENT_TIMESTAMP
);