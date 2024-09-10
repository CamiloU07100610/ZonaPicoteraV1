
<?php
require_once 'db.php';

function registrarUsuario($nombre, $email, $password, $imagen) {
    $conn = conectarDB();
    $nombre = $conn->quote($nombre);
    $email = $conn->quote($email);
    $password = password_hash($password, PASSWORD_BCRYPT);
    $imagenContenido = null;
    if (is_array($imagen) && $imagen['error'] == UPLOAD_ERR_OK) {
        $imagenContenido = file_get_contents($imagen['tmp_name']);
    }
    $sql = "SELECT id FROM usuarios WHERE email = $email";
    $result = $conn->query($sql);
    if ($result->rowCount() > 0) {
        error_log("Email already exists: $email");
        return false; // Email already exists
    }
    $stmt = $conn->prepare("INSERT INTO usuarios (nombre, email, password, imagen) VALUES (?, ?, ?, ?)");
    $stmt->bindParam(1, $nombre);
    $stmt->bindParam(2, $email);
    $stmt->bindParam(3, $password);
    $stmt->bindParam(4, $imagenContenido, PDO::PARAM_LOB);
    if ($stmt->execute()) {
        session_start();
        $_SESSION['usuario_id'] = $conn->lastInsertId();
        $_SESSION['nombre'] = $nombre;
        $_SESSION['email'] = $email;
        $_SESSION['es_admin'] = 0; // Default to non-admin
        return true;
    } else {
        error_log("Error executing statement: " . implode(", ", $stmt->errorInfo()));
        return false;
    }
}

function iniciarSesion($email, $password) {
    $conn = conectarDB();
    $stmt = $conn->prepare("SELECT id, password, es_admin FROM usuarios WHERE email = ?");
    $stmt->bindParam(1, $email);
    $stmt->execute();
    $stmt->bindColumn(1, $id);
    $stmt->bindColumn(2, $passwordHash);
    $stmt->bindColumn(3, $es_admin);
    $stmt->fetch(PDO::FETCH_BOUND);
    $stmt->closeCursor();
    $conn = null;
    if (password_verify($password, $passwordHash)) {
        session_start();
        $_SESSION['usuario_id'] = $id;
        $_SESSION['email'] = $email;
        $_SESSION['es_admin'] = $es_admin ? 1 : 0;
        return true;
    } else {
        return false;
    }
}

function es_admin() {
    return isset($_SESSION['es_admin']) && $_SESSION['es_admin'] == 1;
}

function subirContenido($titulo, $tipo, $archivo) {
    $conn = conectarDB();
    $stmt = $conn->prepare("INSERT INTO contenidos (titulo, tipo, archivo) VALUES (?, ?, ?)");
    $stmt->bindParam(1, $titulo);
    $stmt->bindParam(2, $tipo);
    $stmt->bindParam(3, $archivo, PDO::PARAM_LOB);
    $resultado = $stmt->execute();
    $stmt->close();
    $conn->close();
    return $resultado;
}

function obtenerContenidos($tipo, $orden, $pagina) {
    $conn = conectarDB();
    $offset = ($pagina - 1) * ITEMS_PER_PAGE;
    $itemsPerPage = ITEMS_PER_PAGE;
    $sql = "SELECT * FROM contenidos WHERE tipo = ? ORDER BY $orden LIMIT ?, ?";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(1, $tipo);
    $stmt->bindParam(2, $offset, PDO::PARAM_INT);
    $stmt->bindParam(3, $itemsPerPage, PDO::PARAM_INT);
    $stmt->execute();
    $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $stmt->close();
    $conn->close();
    return $resultado;
}