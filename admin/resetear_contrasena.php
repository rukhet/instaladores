<?php
require '../includes/auth.php';
require '../includes/db.php';

if (!es_admin()) {
    header("Location: dashboard.php");
    exit;
}

$id = intval($_GET['id'] ?? 0);

// Obtener datos del usuario
$stmt = $conn->prepare("SELECT id, usuario FROM usuarios WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result();
if ($res->num_rows === 0) {
    die("Usuario no encontrado.");
}
$usuario = $res->fetch_assoc();

// Procesar cambio
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nueva_clave = password_hash($_POST['nueva_clave'], PASSWORD_DEFAULT);
    $stmt = $conn->prepare("UPDATE usuarios SET clave = ? WHERE id = ?");
    $stmt->bind_param("si", $nueva_clave, $id);
    $stmt->execute();
    echo "<p>Contraseña actualizada correctamente.</p>";
    echo '<a href="usuarios.php">Volver a la lista de usuarios</a>';
    exit;
}
?>

<h2>Resetear contraseña para <?= htmlspecialchars($usuario['usuario']) ?></h2>
<form method="POST">
    Nueva contraseña: <input type="password" name="nueva_clave" required><br>
    <button type="submit">Guardar</button>
</form>
<a href="usuarios.php">Cancelar</a>
