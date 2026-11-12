<?php

session_start();

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/conexion.php';

$sql = "SELECT nombre, apellido, dni, email, rol
        FROM usuarios
        WHERE id = :id AND archivado_en IS NULL";

$consulta = $conexion->prepare($sql);
$consulta->execute(['id' => $_SESSION['usuario_id']]);

$usuario = $consulta->fetch(PDO::FETCH_ASSOC);

if (!$usuario) {
    session_destroy();
    header('Location: login.php');
    exit;
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mi cuenta - PULSE</title>
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body class="pagina-panel">

    <div class="panel">

        <h1>BIENVENIDO A PULSE</h1>

        <h2>Mi cuenta</h2>

        <div class="datos-usuario">

            <p><strong>Nombre:</strong> <?php echo htmlspecialchars($usuario['nombre'], ENT_QUOTES, 'UTF-8'); ?></p>

            <p><strong>Apellido:</strong> <?php echo htmlspecialchars($usuario['apellido'], ENT_QUOTES, 'UTF-8'); ?></p>

            <p><strong>DNI:</strong> <?php echo htmlspecialchars($usuario['dni'], ENT_QUOTES, 'UTF-8'); ?></p>

            <p><strong>Email:</strong> <?php echo htmlspecialchars($usuario['email'], ENT_QUOTES, 'UTF-8'); ?></p>

            <p><strong>Rol:</strong> <?php echo htmlspecialchars($usuario['rol'], ENT_QUOTES, 'UTF-8'); ?></p>

        </div>

        <?php if ($usuario['rol'] === 'admin'): ?>
            <p><a class="boton-panel" href="admin_usuarios.php">Administrar usuarios</a></p>
        <?php endif; ?>

        <p><a class="boton-panel" href="cerrar_sesion.php">Cerrar sesión</a></p>

    </div>

</body>
</html>
