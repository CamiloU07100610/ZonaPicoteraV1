<?php
require_once 'includes/header.php';
require_once 'includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = $_POST['nombre'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    if (registrarUsuario($nombre, $email, $password)) {
        echo "<div class='alert alert-success'>Registro exitoso. <a href='login.php'>Iniciar sesión</a></div>";
    } else {
        echo "<div class='alert alert-danger'>Error en el registro.</div>";
    }
}
?>

<div class="container">
    <h1>Registrarse</h1>
    <form method="post">
        <div class="form-group">
            <label for="nombre">Nombre</label>
            <input type="text" class="form-control" id="nombre" name="nombre" required>
        </div>
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" class="form-control" id="email" name="email" required>
        </div>
        <div class="form-group">
            <label for="password">Contraseña</label>
            <input type="password" class="form-control" id="password" name="password" required>
        </div>
        <button type="submit" class="btn btn-primary">Registrarse</button>
    </form>
</div>

<?php require_once 'includes/footer.php'; ?>
