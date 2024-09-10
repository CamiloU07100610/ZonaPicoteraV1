<?php
require_once 'db.php';

function subirContenido($titulo, $tipo, $archivo) {
    $conn = conectarDB();
    $stmt = $conn->prepare("INSERT INTO contenidos (titulo, tipo, archivo) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $titulo, $tipo, $archivo);
    $resultado = $stmt->execute();
    $stmt->close();
    $conn->close();
    return $resultado;
}

function aumentarVistas($contenido_id) {
    session_start();
    $current_time = time();
    $session_key = 'last_viewed_' . $contenido_id;

    if (!isset($_SESSION[$session_key]) || ($current_time - $_SESSION[$session_key]) > 1800) { // 1800 seconds = 30 minutes
        $conn = conectarDB();
        $stmt = $conn->prepare("UPDATE contenidos SET vistas = vistas + 1 WHERE id = ?");
        $stmt->bind_param("i", $contenido_id);
        $stmt->execute();
        $stmt->close();
        $conn->close();

        $_SESSION[$session_key] = $current_time;
    }
}

function obtenerComentarios($contenido_id) {
    $conn = conectarDB();
    $stmt = $conn->prepare("SELECT * FROM comentarios WHERE contenido_id = ?");
    $stmt->bind_param("i", $contenido_id);
    $stmt->execute();
    $resultado = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
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
    $stmt->bind_param("sii", $tipo, $offset, $itemsPerPage);
    $stmt->execute();
    $resultado = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    $conn->close();
    return $resultado;
}

function agregarComentario($contenido_id, $usuario_id, $comentario) {
    $conn = conectarDB();
    $stmt = $conn->prepare("INSERT INTO comentarios (contenido_id, usuario_id, comentario) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $contenido_id, $usuario_id, $comentario);
    $resultado = $stmt->execute();
    $stmt->close();
    $conn->close();
    return $resultado;
}

function verificarSuscripcion($usuario_id) {
    $conn = conectarDB();
    $stmt = $conn->prepare("SELECT suscrito FROM usuarios WHERE id = ?");
    $stmt->bind_param("i", $usuario_id);
    $stmt->execute();
    $resultado = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    $conn->close();
    return $resultado['suscrito'];
}

function contarContenidos($tipo) {
    $conn = conectarDB();
    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM contenidos WHERE tipo = ?");
    $stmt->bind_param("s", $tipo);
    $stmt->execute();
    $resultado = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    $conn->close();
    return $resultado['total'];
}

function registrarUsuario($nombre, $email, $password) {
    $conn = conectarDB();
    $passwordHash = password_hash($password, PASSWORD_BCRYPT);
    $stmt = $conn->prepare("INSERT INTO usuarios (nombre, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $nombre, $email, $passwordHash);
    $resultado = $stmt->execute();
    $stmt->close();
    $conn->close();
    return $resultado;
}

function iniciarSesion($email, $password) {
    $conn = conectarDB();
    $stmt = $conn->prepare("SELECT id, password, es_admin FROM usuarios WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->bind_result($id, $passwordHash, $es_admin);
    $stmt->fetch();
    $stmt->close();
    $conn->close();

    if (password_verify($password, $passwordHash)) {
        session_start();
        $_SESSION['usuario_id'] = $id;
        $_SESSION['es_admin'] = $es_admin ? 1 : 0;
        return true;
    } else {
        return false;
    }
}

function es_admin() {
    return isset($_SESSION['es_admin']) && $_SESSION['es_admin'] == 1;
}

function obtenerUsuarioPorId($usuario_id) {
    // Ensure the database connection is established
    $conn = conectarDB();

    // Prepare the SQL statement
    $stmt = $conn->prepare("SELECT * FROM usuarios WHERE id = ?");
    $stmt->bind_param("i", $usuario_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Fetch the result and return the user data if found
    if ($result->num_rows > 0) {
        return $result->fetch_assoc();
    } else {
        return null;
    }
}


// includes/functions.php

function actualizarUsuario($usuario_id, $nombre, $email, $password, $imagen)
{
    $conn = conectarDB();
    $sql = "UPDATE usuarios SET nombre = ?, email = ?, password = ?, imagen = ? WHERE id = ?";

    // Handle password
    if (!empty($password)) {
        $passwordHash = password_hash($password, PASSWORD_BCRYPT);
    } else {
        $stmt = $conn->prepare("SELECT password FROM usuarios WHERE id = ?");
        $stmt->bind_param("i", $usuario_id);
        $stmt->execute();
        $stmt->bind_result($passwordHash);
        $stmt->fetch();
        $stmt->close();
    }

    // Handle image upload
    if (is_array($imagen) && $imagen['error'] == UPLOAD_ERR_OK) {
        $imagenContenido = file_get_contents($imagen['tmp_name']);
    } else {
        $stmt = $conn->prepare("SELECT imagen FROM usuarios WHERE id = ?");
        $stmt->bind_param("i", $usuario_id);
        $stmt->execute();
        $stmt->bind_result($imagenContenido);
        $stmt->fetch();
        $stmt->close();
    }

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssi", $nombre, $email, $passwordHash, $imagenContenido, $usuario_id);
    $resultado = $stmt->execute();
    $stmt->close();
    $conn->close();
    return $resultado;
}