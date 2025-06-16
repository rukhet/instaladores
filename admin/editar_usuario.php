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

    // Si es admin, permitir modificar cualquier usuario (incluyendo rol)
    $stmt = $conn->prepare("UPDATE usuarios SET usuario = ?, rol = ? WHERE id = ?");
    $stmt->bind_param("ssi", $nuevo_usuario, $nuevo_rol, $id);
    
    if ($stmt->execute()) {
        header("Location: usuarios.php");
        exit;
    } else {
        $error = "Error al actualizar el usuario";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Usuario</title>
    
</head>
<body>
    <?php include '../estructura/header.php'; ?>

    <div class="contenedor">
        <?php if (isset($error)): ?>
            <div class="mensaje-error"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label for="usuario">Usuario:</label>
                <input type="text" name="usuario" id="usuario" value="<?= htmlspecialchars($usuario['usuario']) ?>" required>
            </div>

            <div class="form-group">
                <label for="rol">Rol:</label>
                <select name="rol" id="rol">
                    <option value="usuario" <?= $usuario['rol'] === 'usuario' ? 'selected' : '' ?>>Usuario</option>
                    <option value="admin" <?= $usuario['rol'] === 'admin' ? 'selected' : '' ?>>Administrador</option>
                </select>
            </div>

            <button type="submit">Guardar cambios</button>
        </form>

        <a href="usuarios.php" class="btn-volver">Volver a la lista de usuarios</a>
    </div>
</body>
</html>