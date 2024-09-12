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

function escapeSpecialCharacters($string) {
    if (!mb_check_encoding($string, 'UTF-8')) {
        throw new Exception("Invalid UTF-8 string");
    }
    $specialChars = array_flip(unescapeSpecialCharacters([]));
    return strtr($string, $specialChars);
}

function registrarUsuario($nombre, $email, $password) {
    global $conn;
    if ($conn === null) {
        $conn = conectarDB();
    }
    $nombre = mb_convert_encoding($nombre, 'UTF-8', 'auto');
    $email = mb_convert_encoding($email, 'UTF-8', 'auto');
    $password = base64_encode(password_hash($password, PASSWORD_BCRYPT));

    try {
        $conn->beginTransaction();
        $sql = "SELECT id FROM usuarios WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(1, $email, PDO::PARAM_STR);
        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            $conn->rollBack();
            error_log("Email already exists: $email");
            return "El email ya está registrado.";
        }
        $stmt->closeCursor();

        $stmt = $conn->prepare("INSERT INTO usuarios (nombre, email, password) VALUES (?, ?, ?)");
        $stmt->bindParam(1, $nombre, PDO::PARAM_STR);
        $stmt->bindParam(2, $email, PDO::PARAM_STR);
        $stmt->bindParam(3, $password, PDO::PARAM_STR);
        if ($stmt->execute()) {
            $conn->commit();
            return true;
        } else {
            $conn->rollBack();
            return "Error al insertar el usuario en la base de datos.";
        }
    } catch (PDOException $e) {
        $conn->rollBack();
        error_log("Database error: " . $e->getMessage());
        return "Error en la base de datos: " . $e->getMessage();
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
    $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $stmt = null;
    return $resultado;
}



function contarContenidos($tipo) {
    $conn = conectarDB();
    $sql = "SELECT COUNT(*) as total FROM contenidos WHERE tipo = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(1, $tipo, PDO::PARAM_STR);
    $stmt->execute();
    $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
    $stmt->closeCursor();
    $conn = null;
    return $resultado['total'];
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

function es_admin() {
    global $conn;
    if ($conn === null) {
        $conn = conectarDB();
    }
    if (!isset($_SESSION['usuario_id'])) {
        return false;
    }
    $usuario_id = $_SESSION['usuario_id'];
    $sql = "SELECT es_admin FROM usuarios WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id', $usuario_id, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result && $result['es_admin'] == 1;
}

function subirContenido($titulo, $tipo, $archivo)
{
    global $conn;
    if ($conn === null) {
        $conn = conectarDB();
    }

    $titulo = mb_convert_encoding($titulo, 'UTF-8', 'auto');
    $tipo = mb_convert_encoding($tipo, 'UTF-8', 'auto');
    $archivo = base64_encode($archivo);

    $sql = "INSERT INTO contenidos (titulo, tipo, archivo) VALUES (:titulo, :tipo, :archivo)";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':titulo', $titulo, PDO::PARAM_STR);
    $stmt->bindParam(':tipo', $tipo, PDO::PARAM_STR);
    $stmt->bindParam(':archivo', $archivo, PDO::PARAM_LOB);

    try {
        $stmt->execute();
        return true;
    } catch (PDOException $e) {
        error_log("Database error: " . $e->getMessage());
        return false;
    }
}