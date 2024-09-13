<?php

session_start();

require_once 'includes/functions.php';

header('Content-Type: application/json');

$contenido_id = $_POST['contenido_id'];
$usuario_id = $_SESSION['usuario_id'];
$comentario = $_POST['comentario'];

$response = [];

if (agregarComentario($contenido_id, $usuario_id, $comentario)) {
    $response['success'] = true;
    $response['comentario'] = $comentario;
    $response['usuario_id'] = $usuario_id;
} else {
    $response['success'] = false;
}

echo json_encode($response);
?>