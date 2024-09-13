<?php
require_once 'db.php';
header('Content-Type: text/html; charset=UTF-8');

if (!defined('ITEMS_PER_PAGE')) {
    define('ITEMS_PER_PAGE', 10); // Set the value as needed
}

function unescapeSpecialCharacters($string) {
    $specialChars = [
        '/aacute' => 'á', '/eacute' => 'é', '/iacute' => 'í', '/oacute' => 'ó', '/uacute' => 'ú',
        '/ntilde' => 'ñ', '/Aacute' => 'Á', '/Eacute' => 'É', '/Iacute' => 'Í', '/Oacute' => 'Ó',
        '/Uacute' => 'Ú', '/Ntilde' => 'Ñ', '/uuml' => 'ü', '/Uuml' => 'Ü'
    ];
    return strtr($string, $specialChars);
}

function determinarTipoContenido($archivo) {
    $extension = pathinfo($archivo['name'], PATHINFO_EXTENSION);
    $extension = strtolower($extension);
    $tipos_imagen = ['jpg', 'jpeg', 'png', 'gif'];
    $tipos_video = ['mp4', 'avi', 'mov', 'wmv'];

    if (in_array($extension, $tipos_imagen)) {
        return 'imagen';
    } elseif (in_array($extension, $tipos_video)) {
        return 'video';
    } else {
        return 'desconocido';
    }
}

function registrarUsuario($nombre, $email, $password) {
    global $conn;
    if ($conn === null) {
        $conn = conectarDB();
    }

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $sql = "INSERT INTO usuarios (nombre, email, password, es_admin) VALUES (:nombre, :email, :password, 0)";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':nombre', $nombre, PDO::PARAM_STR);
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->bindParam(':password', $hashedPassword, PDO::PARAM_STR);

    try {
        $stmt->execute();
        $usuario_id = $conn->lastInsertId();
        session_start();
        $_SESSION['usuario_id'] = $usuario_id;
        $_SESSION['nombre'] = $nombre;
        $_SESSION['es_admin'] = 0;
        return true;
    } catch (PDOException $e) {
        error_log("Database error [Code: " . $e->getCode() . "]: " . $e->getMessage());
        return "Error [Code: " . $e->getCode() . "]: " . $e->getMessage();
    }
}

function iniciarSesion($email, $password) {
    global $conn;
    if ($conn === null) {
        $conn = conectarDB();
    }
    $email = mb_convert_encoding($email, 'UTF-8', 'auto');
    $sql = "SELECT id, password FROM usuarios WHERE email = :email";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    try {
        $stmt->execute();
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($usuario && password_verify($password, base64_decode($usuario['password']))) {
            $_SESSION['usuario_id'] = $usuario['id'];
            return true;
        } else {
            return false;
        }
    } catch (PDOException $e) {
        error_log("Database error: " . $e->getMessage());
        return false;
    } finally {
        $stmt = null;
    }
}

function obtenerContenidos($tipo, $orden, $pagina) {
    global $conn;
    if ($conn === null) {
        $conn = conectarDB();
    }
    $offset = ($pagina - 1) * ITEMS_PER_PAGE;
    $limit = ITEMS_PER_PAGE;
    $sql = "SELECT * FROM contenidos WHERE tipo = :tipo ORDER BY $orden OFFSET :offset ROWS FETCH NEXT :limit ROWS ONLY";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':tipo', $tipo, PDO::PARAM_STR);
    $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
    $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}



function obtenerContenidoPorId($id) {
    global $conn;
    if ($conn === null) {
        $conn = conectarDB();
    }
    $sql = "SELECT * FROM contenidos WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function obtenerOtrosContenidos($tipo, $excluir_id) {
    global $conn;
    if ($conn === null) {
        $conn = conectarDB();
    }
    $sql = "SELECT * FROM contenidos WHERE tipo = :tipo AND id != :excluir_id ORDER BY fecha_subida DESC OFFSET 0 ROWS FETCH NEXT 5 ROWS ONLY";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':tipo', $tipo, PDO::PARAM_STR);
    $stmt->bindParam(':excluir_id', $excluir_id, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function obtenerUsuarioPorId($id) {
    global $conn;
    if ($conn === null) {
        $conn = conectarDB();
    }
    $sql = "SELECT id, nombre, email FROM usuarios WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    try {
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Database error: " . $e->getMessage());
        return false;
    }
}


function subirContenido($titulo, $tipo, $archivo)
{
    global $conn;
    if ($conn === null) {
        $conn = conectarDB();
    }

    $titulo = mb_convert_encoding($titulo, 'UTF-8', 'auto');
    $tipo = mb_convert_encoding($tipo, 'UTF-8', 'auto');
    $fileName = basename($archivo['name']);
    $targetDir = "../uploads/";
    $targetFilePath = $targetDir . $fileName;

    // Check if the target directory exists, if not, create it
    if (!is_dir($targetDir) && !mkdir($targetDir, 0777, true) && !is_dir($targetDir)) {
        throw new \RuntimeException(sprintf('Directory "%s" was not created', $targetDir));
    }

    // Move the file to the target directory
    if (move_uploaded_file($archivo['tmp_name'], $targetFilePath)) {
        $sql = "INSERT INTO contenidos (titulo, tipo, file_path) VALUES (:titulo, :tipo, :file_path)";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':titulo', $titulo, PDO::PARAM_STR);
        $stmt->bindParam(':tipo', $tipo, PDO::PARAM_STR);
        $stmt->bindParam(':file_path', $targetFilePath, PDO::PARAM_STR);

        try {
            $stmt->execute();
            return ['success' => true, 'id' => $conn->lastInsertId()];
        } catch (PDOException $e) {
            error_log("Database error [Code: " . $e->getCode() . "]: " . $e->getMessage());
            return ['success' => false, 'error' => "Error [Code: " . $e->getCode() . "]: " . $e->getMessage()];
        }
    } else {
        return ['success' => false, 'error' => "Error al mover el archivo al directorio de destino."];
    }
}
function contarContenidos($tipo) {
    global $conn;
    if ($conn === null) {
        $conn = conectarDB();
    }
    $sql = "SELECT COUNT(*) as total FROM contenidos WHERE tipo = :tipo";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':tipo', $tipo, PDO::PARAM_STR);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result['total'];
}
function obtenerComentarios($contenido_id) {
    global $conn;
    if ($conn === null) {
        $conn = conectarDB();
    }
    $sql = "SELECT c.*, u.nombre AS usuario_nombre FROM comentarios c 
            JOIN usuarios u ON c.usuario_id = u.id 
            WHERE c.contenido_id = :contenido_id 
            ORDER BY c.fecha DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':contenido_id', $contenido_id, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
function agregarComentario($contenido_id, $usuario_id, $comentario) {
    global $conn;
    if ($conn === null) {
        $conn = conectarDB();
    }
    $sql = "INSERT INTO comentarios (contenido_id, usuario_id, comentario, fecha) VALUES (:contenido_id, :usuario_id, :comentario, GETDATE())";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':contenido_id', $contenido_id, PDO::PARAM_INT);
    $stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
    $stmt->bindParam(':comentario', $comentario, PDO::PARAM_STR);
    return $stmt->execute();
}
function esAdmin($usuario_id) {
    global $conn;
    if ($conn === null) {
        $conn = conectarDB();
    }
    $sql = "SELECT es_admin, email FROM usuarios WHERE id = :usuario_id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result['es_admin'] == 1;
}
function nombreRegistrado($nombre, $usuario_id = null) {
    global $conn;
    if ($conn === null) {
        $conn = conectarDB();
    }
    $sql = "SELECT COUNT(*) FROM usuarios WHERE nombre = :nombre";
    if ($usuario_id !== null) {
        $sql .= " AND id != :usuario_id";
    }
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':nombre', $nombre, PDO::PARAM_STR);
    if ($usuario_id !== null) {
        $stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
    }
    $stmt->execute();
    return $stmt->fetchColumn() > 0;
}
function eliminarContenido($id) {
    global $conn;
    if ($conn === null) {
        $conn = conectarDB();
    }
    $sql = "DELETE FROM contenidos WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    try {
        $stmt->execute();
        return true;
    } catch (PDOException $e) {
        error_log("Database error: " . $e->getMessage());
        return false;
    }
}