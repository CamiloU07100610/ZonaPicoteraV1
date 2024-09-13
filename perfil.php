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

    if (nombreRegistrado($nombre, $usuario_id)) {
        echo "<div class='alert alert-danger'>El nombre ya está registrado. Por favor, elige otro nombre.</div>";
    } else {
        if (actualizarUsuario($usuario_id, $nombre, $email, $password, $imagen)) {
            echo "<div class='alert alert-success'>Perfil actualizado exitosamente.</div>";
        } else {
            echo "<div class='alert alert-danger'>Error al actualizar el perfil.</div>";
        }
    }
}
?>

<div class="container">
    <h1>Mi Perfil</h1>
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
            <label for="password">Contraseña</label>
            <input type="password" class="form-control" id="password" name="password">
        </div>
        <div class="form-group">
            <label for="imagen">Imagen de Perfil</label>
            <input type="file" class="form-control" id="imagen" name="imagen">
        </div>
        <button type="submit" class="btn btn-primary">Actualizar</button>
    </form>
</div>

<?php require_once 'includes/footer.php'; ?>