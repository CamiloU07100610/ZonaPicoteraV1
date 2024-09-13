<?php
require_once 'includes/header.php';
require_once 'includes/functions.php';

$pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$orden = isset($_GET['orden']) ? $_GET['orden'] : 'fecha_subida DESC';

$imagenes = obtenerContenidos('imagen', $orden, $pagina);

$total_paginas = ceil(contarContenidos('imagen') / ITEMS_PER_PAGE);

$base_url = "https://zonapicoteratvb-cra9cjdfc0ajb2f0.eastus-01.azurewebsites.net/uploads/";
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

        <div class="row image-container">
            <?php foreach ($imagenes as $imagen): ?>
                <div class="col-md-4 mb-4">
                    <h3><a href="contenido.php?id=<?php echo $imagen['id']; ?>" class="image-link"><?php echo $imagen['titulo']; ?></a></h3>
                    <img src="<?php echo $base_url . $imagen['file_path']; ?>" class="img-fluid">
                    <p>Vistas: <?php echo $imagen['vistas']; ?></p>
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