<?php
require '../includes/auth.php';
require '../includes/db.php';

 
if (!es_admin() && !es_root()) {
    header("Location: dashboard.php");
    exit;
}

$id = intval($_GET['id'] ?? 0);

// Obtener datos actuales del usuario que se está editando
$stmt = $conn->prepare("SELECT * FROM usuarios WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result();
if ($res->num_rows === 0) {
    die("Usuario no encontrado.");
}
$usuario = $res->fetch_assoc();

$rol_logueado = $_SESSION['rol'] ?? '';       // Rol del usuario logueado
$rol_actual = $usuario['rol'];                // Rol del usuario a editar

// Procesar actualización
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nuevo_usuario = $_POST['usuario'];
    $nuevo_rol = $_POST['rol'];

    // Solo root puede cambiar el rol de un admin
    if ($rol_actual === 'admin' && $rol_logueado !== 'root' && $nuevo_rol !== $rol_actual) {
        $error = "No tienes permisos para modificar el rol de un administrador.";
    }
    // Solo root puede asignar el rol admin
    elseif ($rol_logueado !== 'root' && $nuevo_rol === 'admin' && $rol_actual !== 'admin') {
        $error = "No tienes permisos para asignar el rol de administrador.";
    } else {
        $stmt = $conn->prepare("UPDATE usuarios SET usuario = ?, rol = ? WHERE id = ?");
        $stmt->bind_param("ssi", $nuevo_usuario, $nuevo_rol, $id);
        
        if ($stmt->execute()) {
            header("Location: usuarios.php");
            exit;
        } else {
            $error = "Error al actualizar el usuario.";
        }
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
        <h2>Editar Usuario</h2>

        <?php if (isset($error)): ?>
            <div class="mensaje-error"><?= htmlspecialchars($error) ?></div>
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
                    <?php if ($rol_logueado === 'root'): ?>
                        <option value="admin" <?= $usuario['rol'] === 'admin' ? 'selected' : '' ?>>Administrador</option>
                    <?php endif; ?>
                </select>
            </div>

            <button type="submit">Guardar cambios</button>
        </form>
    </div>
</body>
</html>
