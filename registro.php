<?php
require_once 'includes/header.php';
require_once 'includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = filter_input(INPUT_POST, 'nombre', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);

    $resultado = registrarUsuario($nombre, $email, $password);
    if ($resultado === true) {
        $_SESSION['mensaje_exito'] = "Registro exitoso.";
        header('Location: index.php');
        exit;
    } else {
        echo "<div class='alert alert-danger'>Error en el registro: $resultado</div>";
    }
}
?>

    <div class="container">
        <h1>Registrarse</h1>
        <form method="post">
            <div class="form-group">
                <label for="nombre">Nombre De Usuario</label>
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