<?php
require '../includes/auth.php';
require '../includes/db.php';

if (!es_admin()) {
    header("Location: dashboard.php");
    exit;
}

$id = intval($_GET['id'] ?? 0);

// Obtener datos actuales
$stmt = $conn->prepare("SELECT * FROM usuarios WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result();
if ($res->num_rows === 0) {
    die("Usuario no encontrado.");
}
$usuario = $res->fetch_assoc();

// Procesar actualización
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nuevo_usuario = $_POST['usuario'];
    $nuevo_rol = $_POST['rol'];

    // Evitar que se cambie a admin si no era
    if ($usuario['rol'] !== 'admin') {
        $stmt = $conn->prepare("UPDATE usuarios SET usuario = ?, rol = ? WHERE id = ?");
        $stmt->bind_param("ssi", $nuevo_usuario, $nuevo_rol, $id);
    } else {
        // Solo permitir cambio de nombre si es admin
        $stmt = $conn->prepare("UPDATE usuarios SET usuario = ? WHERE id = ?");
        $stmt->bind_param("si", $nuevo_usuario, $id);
    }

    $stmt->execute();
    header("Location: usuarios.php");
    exit;
}
?>

<h2>Editar Usuario</h2>
<form method="POST">
    Usuario: <input type="text" name="usuario" value="<?= htmlspecialchars($usuario['usuario']) ?>" required><br>

    <?php if ($usuario['rol'] !== 'admin'): ?>
    Rol:
    <select name="rol">
        <option value="usuario" <?= $usuario['rol'] === 'usuario' ? 'selected' : '' ?>>Usuario</option>
        <option value="admin" <?= $usuario['rol'] === 'admin' ? 'selected' : '' ?>>Administrador</option>
    </select><br>
    <?php else: ?>
    Rol: <strong>Administrador (no modificable)</strong><br>
    <?php endif; ?>

    <button type="submit">Guardar cambios</button>
</form>
<a href="usuarios.php">Volver</a>
