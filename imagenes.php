<?php
require_once 'includes/header.php';
require_once 'includes/functions.php';

$pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$orden = isset($_GET['orden']) ? $_GET['orden'] : 'fecha_subida DESC';

$imagenes = obtenerContenidos('imagen', $orden, $pagina);
$total_paginas = ceil(contarContenidos('imagen') / ITEMS_PER_PAGE);
?>

<div class="container">
    <h1>Imágenes</h1>

    <form class="mb-3">
        <select name="orden" onchange="this.form.submit()">
            <option value="fecha_subida DESC">Más recientes</option>
            <option value="vistas DESC">Más vistas</option>
            <option value="titulo ASC">Alfabético A-Z</option>
            <option value="titulo DESC">Alfabético Z-A</option>
        </select>
    </form>

    <div class="row">
        <?php foreach ($imagenes as $imagen): ?>
            <div class="col-md-4 mb-4">
                <h3><a href="#" data-toggle="modal" data-target="#modal-<?php echo $imagen['id']; ?>"><?php echo $imagen['titulo']; ?></a></h3>
                <img src="data:image/jpeg;base64,<?php echo base64_encode($imagen['archivo']); ?>" class="img-fluid">
                <p>Vistas: <?php echo $imagen['vistas']; ?></p>
                <a href="comentarios.php?id=<?php echo $imagen['id']; ?>" class="btn btn-sm btn-primary">Comentarios</a>
            </div>

            <!-- Modal -->
            <div class="modal fade" id="modal-<?php echo $imagen['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="modalLabel-<?php echo $imagen['id']; ?>" aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="modalLabel-<?php echo $imagen['id']; ?>"><?php echo $imagen['titulo']; ?></h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <img src="data:image/jpeg;base64,<?php echo base64_encode($imagen['archivo']); ?>" class="img-fluid">
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
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
