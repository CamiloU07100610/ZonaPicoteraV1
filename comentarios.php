<?php
session_start();
require_once 'includes/header.php';
require_once 'includes/functions.php';

$contenido_id = $_GET['id'];
$comentarios = obtenerComentarios($contenido_id);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $usuario_id = $_SESSION['usuario_id'];
    $comentario = $_POST['comentario'];

    if (agregarComentario($contenido_id, $usuario_id, $comentario)) {
        echo "<div class='alert alert-success'>Comentario agregado exitosamente.</div>";
        $comentarios = obtenerComentarios($contenido_id); // Refresh comments
    } else {
        echo "<div class='alert alert-danger'>Error al agregar el comentario.</div>";
    }
}
?>

<div class="container">
    <h1>Comentarios</h1>
    <div class="mb-4">
        <?php foreach ($comentarios as $comentario): ?>
            <div class="card mb-2">
                <div class="card-body">
                    <p class="card-text"><?php echo $comentario['comentario']; ?></p>
                    <footer class="blockquote-footer"><?php echo $comentario['usuario_id']; ?></footer>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <?php if (isset($_SESSION['usuario_id'])): ?>
        <form method="post">
            <div class="form-group">
                <label for="comentario">Agregar Comentario</label>
                <textarea class="form-control" id="comentario" name="comentario" rows="3" required></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Enviar</button>
        </form>
    <?php else: ?>
        <p><a href="login.php">Inicia sesión</a> para agregar un comentario.</p>
    <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?>
