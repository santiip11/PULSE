<?php

session_start();

if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'admin') {
    http_response_code(403);
    exit('No tenés permiso para acceder a esta página.');
}

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

require_once __DIR__ . '/conexion.php';

$sql = "SELECT id, nombre, apellido, dni, email, rol, archivado_en
        FROM usuarios
        WHERE archivado_en IS NOT NULL
        ORDER BY archivado_en DESC";

$consulta = $conexion->query($sql);

$usuarios = $consulta->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Usuarios archivados - PULSE</title>
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body>

    <h1>Usuarios archivados</h1>

    <p>Total de usuarios archivados: <?php echo count($usuarios); ?></p>

    <?php if (empty($usuarios)): ?>

        <p>No hay usuarios archivados.</p>

    <?php else: ?>

        <table border="1" cellpadding="8">

            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Apellido</th>
                    <th>DNI</th>
                    <th>Email</th>
                    <th>Rol</th>
                    <th>Fecha de archivado</th>
                    <th>Acciones</th>
                </tr>
            </thead>

            <tbody>

                <?php foreach ($usuarios as $usuario): ?>

                    <tr>
                        <td><?php echo htmlspecialchars($usuario['id'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($usuario['nombre'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($usuario['apellido'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($usuario['dni'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($usuario['email'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($usuario['rol'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($usuario['archivado_en'], ENT_QUOTES, 'UTF-8'); ?></td>

                        <td>
                            <form method="POST" action="reactivar_usuarios.php">
                                <input type="hidden" name="id" value="<?php echo $usuario['id']; ?>">
                                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">
                                <button type="submit">Reactivar</button>
                            </form>
                        </td>
                    </tr>

                <?php endforeach; ?>

            </tbody>

        </table>

    <?php endif; ?>

    <br>

    <a href="admin_usuarios.php">Volver al panel</a>

</body>
</html>