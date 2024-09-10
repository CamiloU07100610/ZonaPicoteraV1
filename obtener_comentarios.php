
<?php
require_once 'includes/functions.php';

header('Content-Type: application/json');

if (isset($_GET['id'])) {
    $contenido_id = (int)$_GET['id'];
    $comentarios = obtenerComentarios($contenido_id);
    echo json_encode($comentarios);
} else {
    echo json_encode(['error' => 'ID no proporcionado']);
}
