<?php

session_start();

if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'admin') {
    http_response_code(403);
    exit('No tenés permiso para acceder a esta página.');
}

require_once __DIR__ . '/conexion.php';

$busqueda = '';

$usuarios = [];

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['buscar'])) {

    $busqueda = trim($_GET['buscar']);

    $sql = "SELECT id, nombre, apellido, dni, email, rol
            FROM usuarios
            WHERE archivado_en IS NULL
            AND (
                nombre LIKE :nombre
                OR apellido LIKE :apellido
                OR dni LIKE :dni
                OR email LIKE :email
            )
            ORDER BY id DESC";

    $consulta = $conexion->prepare($sql);

    $valor = '%' . $busqueda . '%';

    $consulta->execute([
        'nombre' => $valor,
        'apellido' => $valor,
        'dni' => $valor,
        'email' => $valor
    ]);

    $usuarios = $consulta->fetchAll(PDO::FETCH_ASSOC);
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Buscar usuarios - PULSE</title>
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body>

    <h1>Buscar usuarios</h1>

    <form method="GET">

        <label>Nombre, apellido, DNI o email:</label>

        <input type="text" name="buscar"
               value="<?php echo htmlspecialchars($busqueda, ENT_QUOTES, 'UTF-8'); ?>">

        <button type="submit">Buscar</button>

    </form>

    <?php if (isset($_GET['buscar'])): ?>

        <h2>Resultados</h2>

        <p>Usuarios encontrados: <?php echo count($usuarios); ?></p>

        <?php if (empty($usuarios)): ?>

            <p>No se encontraron usuarios.</p>

        <?php else: ?>

            <table border="1" cellpadding="8">

                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Apellido</th>
                    <th>DNI</th>
                    <th>Email</th>
                    <th>Rol</th>
                </tr>

                <?php foreach ($usuarios as $usuario): ?>

                    <tr>
                        <td><?php echo htmlspecialchars($usuario['id'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($usuario['nombre'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($usuario['apellido'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($usuario['dni'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($usuario['email'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($usuario['rol'], ENT_QUOTES, 'UTF-8'); ?></td>
                    </tr>

                <?php endforeach; ?>

            </table>

        <?php endif; ?>

    <?php endif; ?>

    <br>

    <a href="admin_usuarios.php">Volver al panel</a>

</body>
</html>