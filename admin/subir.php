<?php
require_once 'header.php';
require_once '../includes/functions.php';

require_once 'header.php';
require_once '../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['delete_contenido_id'])) {
        $contenido_id = $_POST['delete_contenido_id'];
        if (eliminarContenido($contenido_id)) {
            echo "<div class='alert alert-success'>Contenido eliminado exitosamente.</div>";
        } else {
            echo "<div class='alert alert-danger'>Error al eliminar el contenido.</div>";
        }
    }
}

$contenidos = obtenerContenidos('all', 'fecha_subida DESC', 1); // Fetch all contents
?>

<div class="container">
    <h1>Subir Contenido</h1>
    <form id="uploadForm" method="post" enctype="multipart/form-data">
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
            <input type="file" class="form-control" id="archivo" name="archivo" required>
        </div>
        <button type="submit" class="btn btn-primary">Subir</button>
    </form>
    <div style="margin-top: 25px;" id="uploadMessage"></div> <!-- Element to display the success message -->
</div>

<div class="container mt-5">
    <h2>Lista de Contenidos</h2>
    <ul class="list-group">
        <?php foreach ($contenidos as $contenido): ?>
            <li class="list-group-item d-flex justify-content-between align-items-center">
                <?php echo htmlspecialchars($contenido['titulo']); ?>
                <form method="post" class="d-inline">
                    <input type="hidden" name="delete_contenido_id" value="<?php echo $contenido['id']; ?>">
                    <button type="submit" class="btn btn-danger btn-sm">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </form>
            </li>
        <?php endforeach; ?>
    </ul>
</div>

<?php require_once 'footer.php'; ?>

