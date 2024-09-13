<?php
require_once 'includes/header.php';
require_once 'includes/functions.php';

$contenido_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$contenido = obtenerContenidoPorId($contenido_id);
$comentarios = obtenerComentarios($contenido_id);
$otros_contenidos = obtenerOtrosContenidos($contenido['tipo'], $contenido_id);

if (!$contenido) {
    echo "<div class='alert alert-danger'>Contenido no encontrado.</div>";
    require_once 'includes/footer.php';
    exit;
}
?>

    <div class="container">
        <div class="row">
            <div class="col-lg-8">
                <h1><?php echo htmlspecialchars($contenido['titulo']); ?></h1>
                <?php if ($contenido['tipo'] == 'video'): ?>
                    <video src="<?php echo $base_url . $contenido['file_path']; ?>" controls class="video-player"></video>
                <?php else: ?>
                    <img src="<?php echo $base_url . $contenido['file_path']; ?>" class="img-fluid">
                <?php endif; ?>
                <div id="commentsSection">
                    <h5>Comentarios</h5>
                    <div id="commentsList">
                        <?php foreach ($comentarios as $comentario): ?>
                            <div class="comment">
                                <p><strong><?php echo htmlspecialchars($comentario['usuario_nombre']); ?></strong> - <?php echo $comentario['fecha']; ?></p>
                                <p><?php echo htmlspecialchars($comentario['comentario']); ?></p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <?php if (isset($_SESSION['usuario_id'])): ?>
                        <form id="commentForm" method="post" action="comentarios.php">
                            <div class="form-group">
                                <label for="comentario">Agregar Comentario</label>
                                <textarea class="form-control" id="comentario" name="comentario" rows="3" required></textarea>
                            </div>
                            <input type="hidden" name="contenido_id" value="<?php echo $contenido_id; ?>">
                            <button type="submit" class="btn btn-primary">Enviar</button>
                        </form>
                    <?php else: ?>
                        <p><a href="login.php">Inicia sesión</a> para agregar un comentario.</p>
                    <?php endif; ?>
                </div>
            </div>
            <div class="col-lg-4">
                <h5>Otros Contenidos</h5>
                <ul class="list-unstyled">
                    <?php foreach ($otros_contenidos as $otro): ?>
                        <li>
                            <a href="contenido.php?id=<?php echo $otro['id']; ?>">
                                <?php if ($otro['tipo'] == 'video'): ?>
                                    <video src="<?php echo $base_url . $otro['file_path']; ?>" class="img-thumbnail"></video>
                                <?php else: ?>
                                    <img src="<?php echo $base_url . $otro['file_path']; ?>" class="img-thumbnail">
                                <?php endif; ?>
                                <p><?php echo htmlspecialchars($otro['titulo']); ?></p>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>

<?php require_once 'includes/footer.php'; ?>