
<?php

session_start();

if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'admin') {
    http_response_code(403);
    exit('No tenés permiso para realizar esta acción.');
}

require_once __DIR__ . '/conexion.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Método no permitido.');
}

if (
    !isset($_SESSION['csrf_token']) ||
    !isset($_POST['csrf_token']) ||
    !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
) {
    http_response_code(403);
    exit('Token de seguridad no válido.');
}

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    exit('ID de usuario no válido.');
}

$sql = "UPDATE usuarios
        SET archivado_en = CURRENT_TIMESTAMP
        WHERE id = :id AND archivado_en IS NULL";

$consulta = $conexion->prepare($sql);
$consulta->execute(['id' => $id]);

header('Location: admin_usuarios.php');
exit;

?>
