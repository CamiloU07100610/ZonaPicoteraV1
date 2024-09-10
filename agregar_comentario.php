
<?php
require_once 'includes/functions.php';

header('Content-Type: application/json');

session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['usuario_id'])) {
    $contenido_id = (int)$_POST['contenido_id'];
    $usuario_id = $_SESSION['usuario_id'];
    $comentario = $_POST['comentario'];

    if (agregarComentario($contenido_id, $usuario_id, $comentario)) {
        echo json_encode(['success' => true, 'usuario_id' => $usuario_id]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Error al agregar el comentario.']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'No autorizado o datos incompletos.']);
}
