<?php
require_once 'includes/header.php';
require_once 'includes/functions.php';

$pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$orden = isset($_GET['orden']) ? $_GET['orden'] : 'fecha_subida DESC';

$videos = obtenerContenidos('video', $orden, $pagina);

$total_paginas = ceil(contarContenidos('video') / ITEMS_PER_PAGE);

$base_url = "https://zonapicoteratvb-cra9cjdfc0ajb2f0.eastus-01.azurewebsites.net/uploads/";
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
                    <h3><a href="contenido.php?id=<?php echo $video['id']; ?>" class="video-link"><?php echo $video['titulo']; ?></a></h3>
                    <video src="<?php echo $base_url . $video['file_path']; ?>" controls class="video-player"></video>
                    <p>Vistas: <?php echo $video['vistas']; ?></p>
                    <a href="comentarios.php?id=<?php echo $video['id']; ?>" class="btn btn-sm btn-primary">Comentarios</a>
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