
<?php

require_once __DIR__ . '/conexion.php';

$mensaje = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nombre = trim($_POST['nombre'] ?? '');
    $apellido = trim($_POST['apellido'] ?? '');
    $dni = trim($_POST['dni'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmar = $_POST['confirmar'] ?? '';

    if (
        $nombre === '' || $apellido === '' ||
        $dni === '' || $email === '' ||
        $password === '' || $confirmar === ''
    ) {
        $error = 'Completá todos los campos.';

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'El correo no es válido.';

    } elseif ($password !== $confirmar) {
        $error = 'Las contraseñas no coinciden.';

    } else {

        $sql = "SELECT id FROM usuarios
                WHERE dni = :dni OR email = :email";

        $consulta = $conexion->prepare($sql);
        $consulta->execute([
            'dni' => $dni,
            'email' => $email
        ]);

        if ($consulta->fetch()) {
            $error = 'El DNI o el correo ya están registrados.';

        } else {

            $passwordHash = password_hash($password, PASSWORD_DEFAULT);

            $sql = "INSERT INTO usuarios
                    (nombre, apellido, dni, email, password_hash, rol)
                    VALUES
                    (:nombre, :apellido, :dni, :email, :password_hash, 'usuario')";

            try {
                $consulta = $conexion->prepare($sql);

                $consulta->execute([
                    'nombre' => $nombre,
                    'apellido' => $apellido,
                    'dni' => $dni,
                    'email' => $email,
                    'password_hash' => $passwordHash
                ]);

                $mensaje = '¡Usuario registrado correctamente!';

            } catch (PDOException $e) {
                error_log($e->getMessage());
                $error = 'No se pudo registrar el usuario.';
            }
        }
    }
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro - PULSE</title>
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body>

    <h1>Crear cuenta en PULSE</h1>

    <?php if ($mensaje !== ''): ?>
        <p><?php echo htmlspecialchars($mensaje, ENT_QUOTES, 'UTF-8'); ?></p>
    <?php endif; ?>

    <?php if ($error !== ''): ?>
        <p><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></p>
    <?php endif; ?>

    <form action="" method="POST">

        <label>Nombre:</label>
        <input type="text" name="nombre" required>

        <br><br>

        <label>Apellido:</label>
        <input type="text" name="apellido" required>

        <br><br>

        <label>DNI:</label>
        <input type="text" name="dni" required>

        <br><br>

        <label>Correo:</label>
        <input type="email" name="email" required>

        <br><br>

        <label>Contraseña:</label>
        <input type="password" name="password" required>

        <br><br>

        <label>Confirmar contraseña:</label>
        <input type="password" name="confirmar" required>

        <br><br>

        <button type="submit">Registrarme</button>

    </form>

</body>
</html>
