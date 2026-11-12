
<?php

session_start();

require_once __DIR__ . '/conexion.php';

$mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $sql = "SELECT id, nombre, apellido, email, password_hash, rol
            FROM usuarios
            WHERE email = :email
            AND archivado_en IS NULL";

    $consulta = $conexion->prepare($sql);
    $consulta->execute(['email' => $email]);

    $usuario = $consulta->fetch(PDO::FETCH_ASSOC);

    if ($usuario && password_verify($password, $usuario['password_hash'])) {

        session_regenerate_id(true);

        $_SESSION['usuario_id'] = $usuario['id'];
        $_SESSION['nombre'] = $usuario['nombre'];
        $_SESSION['rol'] = $usuario['rol'];

        if ($usuario['rol'] === 'admin') {
            header('Location: admin_usuarios.php');
            exit;
        } else {
            header('Location: panel.php');
            exit;
        }

    } else {
        $mensaje = 'Correo o contraseña incorrectos.';
    }
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Iniciar sesión - PULSE</title>
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body>

    <h1>Iniciar sesión en PULSE</h1>

    <p><?php echo htmlspecialchars($mensaje, ENT_QUOTES, 'UTF-8'); ?></p>

    <form method="POST">

        <label>Correo electrónico:</label>
        <input type="email" name="email" required>

        <br><br>

        <label>Contraseña:</label>
        <input type="password" name="password" required>

        <br><br>

        <button type="submit">Iniciar sesión</button>

    </form>

</body>
</html>
