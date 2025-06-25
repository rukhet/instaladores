<?php
require '../includes/auth.php';
require '../includes/db.php';

if (!es_admin() && !es_root()) {
    exit("Acceso denegado");
}

define('CLAVE_SECRETA', 'clave_super_segura_123'); // cámbiala y mantenla privada

function encriptar_id($id) {
    $iv = random_bytes(16);
    $cifrado = openssl_encrypt($id, 'aes-256-cbc', CLAVE_SECRETA, OPENSSL_RAW_DATA, $iv);
    return urlencode(base64_encode($iv . $cifrado));
}

$id = intval($_GET['id'] ?? 0);
if ($id <= 0) exit("ID inválido");

$stmt = $conn->prepare("SELECT nombre FROM programas WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows === 0) { !==
    exit("Programa no encontrado.");
}

$programa = $res->fetch_assoc();
$nombre = htmlspecialchars($programa['nombre']);

$token = encriptar_id($id);
$base_url = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'];
$enlace_directo = "$base_url/instaladores/includes/descarga_publica.php?token=$token";
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Compartir Programa</title>
    <style>
        input.enlace {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
        }
        button {
            padding: 8px 12px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <?php include '../estructura/header.php'; ?>
    <h2>Compartir Programa: <?= $nombre ?></h2>
    <p>Comparte este enlace para que cualquier persona pueda descargar el programa sin iniciar sesión:</p>

    <input type="text" id="enlace" class="enlace" value="<?= $enlace_directo ?>" readonly>
    <button onclick="copiarEnlace()">Copiar enlace</button>

    <script>
        function copiarEnlace() {
            const enlace = document.getElementById("enlace");
            enlace.select();
            enlace.setSelectionRange(0, 99999); // Para móviles
            document.execCommand("copy");
            alert("Enlace copiado al portapapeles");
        }
    </script>
</body>
</html>
