
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

$mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (
        !isset($_SESSION['csrf_token']) ||
        !isset($_POST['csrf_token']) ||
        !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
    ) {
        http_response_code(403);
        exit('Solicitud no válida.');
    }

    $nombre = trim($_POST['nombre'] ?? '');
    $apellido = trim($_POST['apellido'] ?? '');
    $dni = trim($_POST['dni'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (
        $nombre === '' ||
        $apellido === '' ||
        $dni === '' ||
        $email === '' ||
        $password === ''
    ) {
        $mensaje = 'Completá todos los campos.';

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $mensaje = 'El correo no es válido.';

    } else {

        $sql = "SELECT id FROM usuarios
                WHERE dni = :dni OR email = :email";

        $consulta = $conexion->prepare($sql);
        $consulta->execute([
            'dni' => $dni,
            'email' => $email
        ]);

        if ($consulta->fetch()) {
            $mensaje = 'El DNI o el correo ya están registrados.';

        } else {

            $passwordHash = password_hash($password, PASSWORD_DEFAULT);

            $sql = "INSERT INTO usuarios
                    (nombre, apellido, dni, email, password_hash, rol)
                    VALUES
                    (:nombre, :apellido, :dni, :email, :password_hash, 'usuario')";

            $consulta = $conexion->prepare($sql);

            $resultado = $consulta->execute([
                'nombre' => $nombre,
                'apellido' => $apellido,
                'dni' => $dni,
                'email' => $email,
                'password_hash' => $passwordHash
            ]);

            if ($resultado) {
                $mensaje = '¡Usuario registrado correctamente!';
            } else {
                $mensaje = 'No se pudo registrar el usuario.';
            }
        }
    }
}

$sql = "SELECT id, nombre, apellido, dni, email, rol, creado_en
        FROM usuarios
        WHERE archivado_en IS NULL
        ORDER BY id DESC";

$consulta = $conexion->query($sql);

$usuarios = $consulta->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Usuarios - PULSE</title>
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body>

    <h1>Usuarios registrados en PULSE</h1>

    <h2>Registrar nuevo usuario</h2>

    <?php if (!empty($mensaje)): ?>
        <p><?php echo htmlspecialchars($mensaje, ENT_QUOTES, 'UTF-8'); ?></p>
    <?php endif; ?>

    <form method="POST" action="admin_usuarios.php">

        <input type="hidden" name="csrf_token"
        value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">

        <label>Nombre:</label>
        <input type="text" name="nombre" required>

        <label>Apellido:</label>
        <input type="text" name="apellido" required>

        <label>DNI:</label>
        <input type="text" name="dni" required>

        <label>Correo electrónico:</label>
        <input type="email" name="email" required>

        <label>Contraseña:</label>
        <input type="password" name="password" required>

        <button type="submit">Registrar usuario</button>

    </form>

    <hr>

    <p><a href="buscar_usuarios.php">Buscar usuarios</a></p>

    <p><a href="usuarios_archivados.php">Ver usuarios archivados</a></p>

    <p>Total de usuarios: <?php echo count($usuarios); ?></p>

    <?php if (empty($usuarios)): ?>

        <p>No hay usuarios registrados.</p>

    <?php else: ?>

        <table>

            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Apellido</th>
                    <th>DNI</th>
                    <th>Email</th>
                    <th>Rol</th>
                    <th>Fecha de registro</th>
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
                        <td><?php echo htmlspecialchars($usuario['creado_en'], ENT_QUOTES, 'UTF-8'); ?></td>

                        <td>
                            <a href="editar_usuarios.php?id=<?php echo $usuario['id']; ?>">Editar</a>

                            <form method="POST" action="archivar_usuarios.php">

                                <input type="hidden" name="id" value="<?php echo $usuario['id']; ?>">

                                <input type="hidden" name="csrf_token"
                                value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">

                                <button type="submit">Archivar</button>

                            </form>
                        </td>
                    </tr>

                <?php endforeach; ?>

            </tbody>

        </table>

    <?php endif; ?>

</body>
</html>
