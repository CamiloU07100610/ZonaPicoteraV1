<?php
session_start();
require_once '../includes/header.php';
require_once '../includes/functions.php';

if (!es_admin()) {
    header('Location: ../index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $titulo = $_POST['titulo'];
    $tipo = $_POST['tipo'];
    $archivo = file_get_contents($_FILES['archivo']['tmp_name']);

    if (subirContenido($titulo, $tipo, $archivo)) {
        echo "<div class='alert alert-success'>Contenido subido exitosamente.</div>";
    } else {
        echo "<div class='alert alert-danger'>Error al subir el contenido.</div>";
    }
}
?>

<div class="container">
    <h1>Subir Contenido</h1>

    <form method="post" enctype="multipart/form-data">
        <div class="form-group">
            <label for="titulo">Título</label>
            <input type="text" class="form-control" id="titulo" name="titulo" required>
        </div>
        <div class="form-group">
            <label for="tipo">Tipo</label>
            <select class="form-control" id="tipo" name="tipo" required>
                <option value="video">Video</option>
                <option value="imagen">Imagen</option>
            </select>
        </div>
        <div class="form-group">
            <label for="archivo">Archivo</label>
            <input type="file" class="form-control-file" id="archivo" name="archivo" required>
        </div>
        <button type="submit" class="btn btn-primary">Subir</button>
    </form>
</div>

<?php require_once '../includes/footer.php'; ?>
