<?php
require_once 'includes/header.php';
require_once 'includes/functions.php';

$mas_vistas = obtenerContenidos('video', 'vistas DESC', 1);
$recientes_videos = obtenerContenidos('video', 'fecha_subida DESC', 1);
$recientes_imagenes = obtenerContenidos('imagen', 'fecha_subida DESC', 1);

$base_url = "https://zonapicoteratvb-cra9cjdfc0ajb2f0.eastus-01.azurewebsites.net/uploads/";
?>

    <div class="container">
        <h2>Videos más vistos</h2>
        <div class="row">
            <?php foreach ($mas_vistas as $video): ?>
                <div class="col-md-4 mb-4">
                    <h3><a href="#" class="video-link" data-video-id="<?php echo $video['id']; ?>" data-video-src="<?php echo $base_url . $video['file_path']; ?>" data-video-title="<?php echo $video['titulo']; ?>"><?php echo $video['titulo']; ?></a></h3>
                    <video src="<?php echo $base_url . $video['file_path']; ?>" controls class="video-player"></video>
                    <p>Vistas: <?php echo $video['vistas']; ?></p>
                </div>
            <?php endforeach; ?>
        </div>

        <h2>Publicaciones más recientes</h2>
        <div class="row">
            <?php foreach ($recientes_videos as $video): ?>
                <div class="col-md-4 mb-4">
                    <h3><a href="#" class="video-link" data-video-id="<?php echo $video['id']; ?>" data-video-src="<?php echo $base_url . $video['file_path']; ?>" data-video-title="<?php echo $video['titulo']; ?>"><?php echo $video['titulo']; ?></a></h3>
                    <video src="<?php echo $base_url . $video['file_path']; ?>" controls class="video-player"></video>
                    <p>Vistas: <?php echo $video['vistas']; ?></p>
                </div>
            <?php endforeach; ?>
            <?php foreach ($recientes_imagenes as $imagen): ?>
                <div class="col-md-4 mb-4">
                    <h3><a href="#" class="image-link" data-image-id="<?php echo $imagen['id']; ?>" data-image-src="<?php echo $base_url . $imagen['file_path']; ?>" data-image-title="<?php echo $imagen['titulo']; ?>"><?php echo $imagen['titulo']; ?></a></h3>
                    <img src="<?php echo $base_url . $imagen['file_path']; ?>" class="img-fluid">
                    <p>Vistas: <?php echo $imagen['vistas']; ?></p>
                </div>
            <?php endforeach; ?>
        </div>


<?php require_once 'includes/footer.php'; ?>