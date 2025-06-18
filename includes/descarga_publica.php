<?php
require '../includes/db.php';

define('CLAVE_SECRETA', 'clave_super_segura_123'); // debe coincidir

function desencriptar_token($token) {
    $datos = base64_decode($token);
    if ($datos === false || strlen($datos) < 17) return false;

    $iv = substr($datos, 0, 16);
    $cifrado = substr($datos, 16);
    $id = openssl_decrypt($cifrado, 'aes-256-cbc', CLAVE_SECRETA, OPENSSL_RAW_DATA, $iv);
    return is_numeric($id) ? intval($id) : false;
}

$token = $_GET['token'] ?? '';
$id = desencriptar_token($token);
if (!$id) {
    http_response_code(400);
    exit("Token inválido.");
}

// Buscar archivo
$stmt = $conn->prepare("SELECT nombre, archivo FROM programas WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows === 0) {
    http_response_code(404);
    exit("Programa no encontrado.");
}

$programa = $res->fetch_assoc();
$archivo = basename($programa['archivo']);
$ruta = realpath(__DIR__ . '/../uploads/' . $archivo);

if (!$ruta || !file_exists($ruta)) {
    http_response_code(404);
    exit("Archivo no encontrado.");
}

// Descarga forzada
header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . basename($archivo) . '"');
header('Content-Length: ' . filesize($ruta));
readfile($ruta);
exit;
