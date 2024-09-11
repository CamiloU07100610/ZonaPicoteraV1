<?php
require_once 'includes/header.php';
require_once 'includes/functions.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$usuario_id = $_SESSION['usuario_id'];
$usuario = obtenerUsuarioPorId($usuario_id);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = $_POST['nombre'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $imagen = $_FILES['imagen'];

    if (actualizarUsuario($usuario_id, $nombre, $email, $password, $imagen)) {
        echo "<div class='alert alert-success'>Perfil actualizado exitosamente.</div>";
    } else {
        echo "<div class='alert alert-danger'>Error al actualizar el perfil.</div>";
    }
}
?>

<div class="container">
    <h1>Perfil</h1>
    <?php if ($usuario['imagen']): ?>
        <img src="data:image/jpeg;base64,<?php echo base64_encode($usuario['imagen']); ?>" class="profile-img" alt="Imagen de perfil">
    <?php endif; ?>
    <br>
    <form method="post" enctype="multipart/form-data">
        <div class="form-group">
            <label for="nombre">Nombre</label>
            <input type="text" class="form-control" id="nombre" name="nombre" value="<?php echo htmlspecialchars($usuario['nombre']); ?>" required>
        </div>
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($usuario['email']); ?>" required>
        </div>
        <div class="form-group">
            <label for="password">Contraseña (dejar en blanco para no cambiar)</label>
            <input type="password" class="form-control" id="password" name="password">
        </div>
        <div class="form-group">
            <label for="imagen">Imagen de perfil</label>
            <input type="file" class="form-control" id="imagen" name="imagen">
        </div>
        <button type="submit" class="btn btn-primary">Actualizar Perfil</button>
    </form>
</div>

<?php require_once 'includes/footer.php'; ?>

