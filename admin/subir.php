<?php
require_once '../includes/header.php';
require_once '../includes/functions.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!es_admin()) {
    die('Acceso denegado');
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
        <form id="uploadForm" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label for="titulo">Título</label>
                <input type="text" class="form-control" id="titulo" name="titulo" required>
            </div>
            <div class="form-group">
                <label for="tipo">Tipo</label>
                <select class="form-control" id="tipo" name="tipo" required>
                    <option value="imagen">Imagen</option>
                    <option value="video">Video</option>
                </select>
            </div>
            <div class="form-group">
                <label for="archivo">Archivo</label>
                <input type="file" class="form-control" id="archivo" name="archivo" required>
            </div>
            <button type="submit" class="btn btn-primary">Subir</button>
        </form>
        <div class="progress mt-3">
            <div id="progressBar" class="progress-bar" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">0%</div>
        </div>
    </div>

    <script>
        document.getElementById('uploadForm').addEventListener('submit', function(event) {
            event.preventDefault();
            var formData = new FormData(this);
            var xhr = new XMLHttpRequest();
            xhr.open('POST', 'subir.php', true);

            xhr.upload.addEventListener('progress', function(e) {
                if (e.lengthComputable) {
                    var percentComplete = (e.loaded / e.total) * 100;
                    var progressBar = document.getElementById('progressBar');
                    progressBar.style.width = percentComplete + '%';
                    progressBar.setAttribute('aria-valuenow', percentComplete);
                    progressBar.textContent = Math.round(percentComplete) + '%';
                }
            });

            xhr.onload = function() {
                if (xhr.status === 200) {
                    alert('Contenido subido exitosamente.');
                } else {
                    alert('Error al subir el contenido.');
                }
            };

            xhr.send(formData);
        });
    </script>

<?php require_once '../includes/footer.php'; ?>