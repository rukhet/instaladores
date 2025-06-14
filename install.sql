CREATE DATABASE IF NOT EXISTS instaladores;
USE instaladores;

CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario VARCHAR(50) UNIQUE NOT NULL,
    clave VARCHAR(255) NOT NULL,
    rol ENUM('admin','usuario') DEFAULT 'usuario'
);

CREATE TABLE programas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(255),
    archivo VARCHAR(255),
    descripcion TEXT,
    version VARCHAR(50),
    fecha_subida DATETIME DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO usuarios (usuario, clave, rol) 
VALUES ('admin', '$2b$12$K1.F8/xh8NkFkT25DeX/f.prJglWUDnC.hoOYyWURAfiTwS6GNBcS', 'admin');
