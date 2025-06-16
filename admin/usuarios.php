<?php
include '../includes/auth.php';
include '../includes/db.php';
if (!es_admin()) exit('Acceso denegado');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = $_POST['usuario'];
    $clave = password_hash($_POST['clave'], PASSWORD_DEFAULT);
    $rol = $_POST['rol'];
    $stmt = $conn->prepare("INSERT INTO usuarios (usuario, clave, rol) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $usuario, $clave, $rol);
    $stmt->execute();
}

$usuarios = $conn->query("SELECT id, usuario, rol FROM usuarios");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrar Usuarios</title>
 

</head>
<body>
        <?php include '../estructura/header.php'; ?>
    <form method="POST" class="form-horizontal">
        <div class="form-group">
            <label for="usuario">Usuario:</label>
            <input type="text" name="usuario" id="usuario" required>
        </div>
        
        <div class="form-group">
            <label for="clave">Clave:</label>
            <input type="password" name="clave" id="clave" required>
        </div>
        
        <div class="form-group">
            <label for="rol">Rol:</label>
            <select name="rol" id="rol">
                <option value="usuario">Usuario</option>
                <option value="admin">Admin</option>
            </select>
        </div>
        
        <button type="submit">Agregar</button>
    </form>

    <table>
        <tr>
            <th>ID</th>
            <th>Usuario</th>
            <th>Rol</th>
            <th>Acciones</th>
        </tr>
        <?php while ($u = $usuarios->fetch_assoc()): ?>
        <tr>
            <td><?= $u['id'] ?></td>
            <td><?= htmlspecialchars($u['usuario']) ?></td>
            <td><?= htmlspecialchars($u['rol']) ?></td>
            <td class="acciones">
                <?php if (es_admin()): ?>
                    <a href="editar_usuario.php?id=<?= $u['id'] ?>">Editar</a>
                    <?php if ($u['rol'] === 'usuario'): ?>
                        <a href="eliminar_usuario.php?id=<?= $u['id'] ?>" onclick="return confirm('¿Eliminar este usuario?')">Eliminar</a>
                    <?php endif; ?>
                    <a href="resetear_contrasena.php?id=<?= $u['id'] ?>">Resetear contraseña</a>
                <?php else: ?>
                    -
                <?php endif; ?>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>

    <a href="dashboard.php" class="volver">Volver al panel</a>
</body>
</html>