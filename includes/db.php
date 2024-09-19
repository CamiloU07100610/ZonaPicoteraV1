<?php
function conectarDB() {
$host = 'db-mysql-nyc3-24465-do-user-16530720-0.k.db.ondigitalocean.com';
$port = 25060;
$dbname = 'defaultdb';
$username = 'doadmin';
$password = 'AVNS_bK7cdD4ohV9Rfs2vyYO';
$sslmode = 'REQUIRED';

$dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4";
$options = [
PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
PDO::MYSQL_ATTR_SSL_CA => '/path/to/ca-cert.pem', // Update with the correct path to the CA certificate
];

try {
$conn = new PDO($dsn, $username, $password, $options);
crearTablas($conn); // Call the function to create tables
return $conn;
} catch (PDOException $e) {
error_log("Database connection error: " . $e->getMessage());
return null;
}
}

function crearTablas($conn) {
$sql = "
CREATE TABLE IF NOT EXISTS usuarios (
id INT AUTO_INCREMENT PRIMARY KEY,
nombre VARCHAR(255) NOT NULL,
email VARCHAR(255) NOT NULL UNIQUE,
password VARCHAR(255) NOT NULL,
es_admin TINYINT(1) DEFAULT 0,
created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS contenidos (
id INT AUTO_INCREMENT PRIMARY KEY,
titulo VARCHAR(255) NOT NULL,
tipo ENUM('video', 'imagen') NOT NULL,
file_path VARCHAR(255) NOT NULL,
vistas INT DEFAULT 0,
fecha_subida TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS comentarios (
id INT AUTO_INCREMENT PRIMARY KEY,
contenido_id INT NOT NULL,
usuario_id INT NOT NULL,
comentario TEXT NOT NULL,
fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
FOREIGN KEY (contenido_id) REFERENCES contenidos(id) ON DELETE CASCADE,
FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
);
";

try {
$conn->exec($sql);
} catch (PDOException $e) {
error_log("Error creating tables: " . $e->getMessage());
}
<<<<<<< HEAD
}
=======
}
>>>>>>> master/master
