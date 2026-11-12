
<?php

session_start();

if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'admin') {
    http_response_code(403);
    exit('No tenés permiso para acceder a esta página.');
}

require_once __DIR__ . '/conexion.php';

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (
        !isset($_POST['csrf_token']) ||
        !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
    ) {
        http_response_code(403);
        exit('Token de seguridad no válido.');
    }

    $email = trim($_POST['email'] ?? '');

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $mensaje = 'Ingresá un correo válido.';
    } else {

        $sql = "UPDATE usuarios
                SET rol = 'admin'
                WHERE email = :email
                AND archivado_en IS NULL";

        $consulta = $conexion->prepare($sql);
        $consulta->execute(['email' => $email]);

        if ($consulta->rowCount() > 0) {
            $mensaje = 'Administrador creado correctamente.';
        } else {
            $mensaje = 'No se encontró ese usuario.';
        }
    }
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Crear administrador - PULSE</title>
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body>

    <h1>Crear administrador</h1>

    <p><?php echo htmlspecialchars($mensaje, ENT_QUOTES, 'UTF-8'); ?></p>

    <form method="POST">

        <input type="hidden" name="csrf_token"
               value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">

        <label>Correo electrónico:</label>
        <input type="email" name="email" required>

        <button type="submit">Convertir en administrador</button>

    </form>

</body>
</html>
