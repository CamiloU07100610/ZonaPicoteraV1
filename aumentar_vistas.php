<?php
require_once 'includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id'])) {
    $contenido_id = (int)$_POST['id'];
    aumentarVistas($contenido_id);
}
