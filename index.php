
<?php
require_once 'includes/header.php';
require_once 'includes/functions.php';

$mas_vistas = obtenerContenidos('video', 'vistas DESC', 1);
$recientes = obtenerContenidos('video', 'fecha_subida DESC', 1);
?>

<div class="container">
    <h2>Videos más vistos</h2>
    <div class="row">
        <?php foreach ($mas_vistas as $video): ?>
            <div class="col-md-4 mb-4">
                <h3><a href="#" class="video-link" data-video-src="data:video/mp4;base64,<?php echo base64_encode($video['archivo']); ?>" data-video-title="<?php echo $video['titulo']; ?>"><?php echo $video['titulo']; ?></a></h3>
                <video src="data:video/mp4;base64,<?php echo base64_encode($video['archivo']); ?>" controls class="video-player"></video>
                <p>Vistas: <?php echo $video['vistas']; ?></p>
            </div>
        <?php endforeach; ?>
    </div>

    <h2>Videos más recientes</h2>
    <div class="row">
        <?php foreach ($recientes as $video): ?>
            <div class="col-md-4 mb-4">
                <h3><a href="#" class="video-link" data-video-src="data:video/mp4;base64,<?php echo base64_encode($video['archivo']); ?>" data-video-title="<?php echo $video['titulo']; ?>"><?php echo $video['titulo']; ?></a></h3>
                <video src="data:video/mp4;base64,<?php echo base64_encode($video['archivo']); ?>" controls class="video-player"></video>
                <p>Vistas: <?php echo $video['vistas']; ?></p>
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
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>