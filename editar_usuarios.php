
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

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    exit('ID de usuario no válido.');
}

$sql = "SELECT id, nombre, apellido, dni, email
        FROM usuarios
        WHERE id = :id AND archivado_en IS NULL";

$consulta = $conexion->prepare($sql);
$consulta->execute(['id' => $id]);

$usuario = $consulta->fetch(PDO::FETCH_ASSOC);

if (!$usuario) {
    exit('Usuario no encontrado.');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (
        !isset($_SESSION['csrf_token']) ||
        !isset($_POST['csrf_token']) ||
        !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
    ) {
        http_response_code(403);
        exit('Token de seguridad no válido.');
    }

    $nombre = trim($_POST['nombre'] ?? '');
    $apellido = trim($_POST['apellido'] ?? '');
    $dni = trim($_POST['dni'] ?? '');
    $email = trim($_POST['email'] ?? '');

    $usuario['nombre'] = $nombre;
    $usuario['apellido'] = $apellido;
    $usuario['dni'] = $dni;
    $usuario['email'] = $email;

    if ($nombre === '' || $apellido === '' || $dni === '' || $email === '') {
        $error = 'Todos los campos son obligatorios.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'El correo no es válido.';
    } else {

        $sql = "SELECT id FROM usuarios
                WHERE (dni = :dni OR email = :email)
                AND id != :id";

        $consulta = $conexion->prepare($sql);
        $consulta->execute([
            'dni' => $dni,
            'email' => $email,
            'id' => $id
        ]);

        if ($consulta->fetch()) {
            $error = 'El DNI o el correo ya están registrados.';
        } else {

            $sql = "UPDATE usuarios
                    SET nombre = :nombre,
                        apellido = :apellido,
                        dni = :dni,
                        email = :email
                    WHERE id = :id AND archivado_en IS NULL";

            $consulta = $conexion->prepare($sql);
            $consulta->execute([
                'nombre' => $nombre,
                'apellido' => $apellido,
                'dni' => $dni,
                'email' => $email,
                'id' => $id
            ]);

            header('Location: admin_usuarios.php');
            exit;
        }
    }
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar usuario - PULSE</title>
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body>

    <h1>Editar usuario</h1>

    <p><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></p>

    <form method="POST">

        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">

        <label>Nombre:</label>
        <input type="text" name="nombre" value="<?php echo htmlspecialchars($usuario['nombre'], ENT_QUOTES, 'UTF-8'); ?>" required>

        <br><br>

        <label>Apellido:</label>
        <input type="text" name="apellido" value="<?php echo htmlspecialchars($usuario['apellido'], ENT_QUOTES, 'UTF-8'); ?>" required>

        <br><br>

        <label>DNI:</label>
        <input type="text" name="dni" value="<?php echo htmlspecialchars($usuario['dni'], ENT_QUOTES, 'UTF-8'); ?>" required>

        <br><br>

        <label>Email:</label>
        <input type="email" name="email" value="<?php echo htmlspecialchars($usuario['email'], ENT_QUOTES, 'UTF-8'); ?>" required>

        <br><br>

        <button type="submit">Guardar cambios</button>

    </form>

    <br>

    <a href="admin_usuarios.php">Volver</a>

</body>
</html>
