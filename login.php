<?php
session_start();
require_once 'includes/header.php';
require_once 'includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    if (iniciarSesion($email, $password)) {
        header('Location: index.php');
        exit;
    } else {
        echo "<div class='alert alert-danger'>Email o contraseña incorrectos.</div>";
    }
}
?>

    <div class="container">
        <h1>Iniciar Sesión</h1>
        <form method="post">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">Contraseña</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary">Iniciar Sesión</button>
        </form>
    </div>

<?php require_once 'includes/footer.php'; ?>