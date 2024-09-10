<?php
require_once 'includes/header.php';
require_once 'includes/functions.php';

$pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$orden = isset($_GET['orden']) ? $_GET['orden'] : 'fecha_subida DESC';

$videos = obtenerContenidos('video', $orden, $pagina);
$total_paginas = ceil(contarContenidos('video') / ITEMS_PER_PAGE);
?>

    <div class="container">
        <h1>Videos</h1>

        <form class="mb-3">
            <select name="orden" onchange="this.form.submit()">
                <option value="fecha_subida DESC">Más recientes</option>
                <option value="vistas DESC">Más vistos</option>
                <option value="titulo ASC">Alfabético A-Z</option>
                <option value="titulo DESC">Alfabético Z-A</option>
            </select>
        </form>

        <div class="row video-container">
            <?php foreach ($videos as $video): ?>
                <div class="col-md-4 mb-4">
                    <h3><a href="#" class="video-link" data-video-id="<?php echo $video['id']; ?>" data-video-src="data:video/mp4;base64,<?php echo base64_encode($video['archivo']); ?>" data-video-title="<?php echo $video['titulo']; ?>"><?php echo $video['titulo']; ?></a></h3>
                    <video src="data:video/mp4;base64,<?php echo base64_encode($video['archivo']); ?>" controls class="video-player"></video>
                    <p>Vistas: <?php echo $video['vistas']; ?></p>
                    <a href="comentarios.php?id=<?php echo $video['id']; ?>" class="btn btn-sm btn-primary">Comentarios</a>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Single Modal -->
        <div class="modal fade" id="videoModal" tabindex="-1" role="dialog" aria-labelledby="videoModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="videoModalLabel"></h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <video id="modalVideo" controls class="w-100"></video>
                        <div id="commentsSection">
                            <h5>Comentarios</h5>
                            <div id="commentsList"></div>
                            <?php if (isset($_SESSION['usuario_id'])): ?>
                                <form id="commentForm">
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
                    </div>
                </div>
            </div>
        </div>

        <nav>
            <ul class="pagination">
                <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
                    <li class="page-item <?php echo $i == $pagina ? 'active' : ''; ?>">
                        <a class="page-link" href="?pagina=<?php echo $i; ?>&orden=<?php echo $orden; ?>"><?php echo $i; ?></a>
                    </li>
                <?php endfor; ?>
            </ul>
        </nav>
    </div>

<?php require_once 'includes/footer.php'; ?>