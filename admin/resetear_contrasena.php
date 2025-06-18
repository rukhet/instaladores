<?php
require '../includes/auth.php';
require '../includes/db.php';

// Validar acceso: solo admin o root pueden entrar
if (!es_admin() && !es_root()) {
    header("Location: dashboard.php");
    exit;
}

$id = intval($_GET['id'] ?? 0);

// Obtener datos del usuario a modificar (incluyendo rol)
$stmt = $conn->prepare("SELECT id, usuario, rol FROM usuarios WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result();
if ($res->num_rows === 0) {
    die("Usuario no encontrado.");
}
$usuario = $res->fetch_assoc();

// Restringir: si el usuario logueado es admin y el destino es admin o root → denegar
if (es_admin() && ($usuario['rol'] === 'admin' || $usuario['rol'] === 'root')) {
    die("No tiene permiso para cambiar la contraseña de este usuario.");
}

// Procesar cambio
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nueva_clave = password_hash($_POST['nueva_clave'], PASSWORD_DEFAULT);
    $stmt = $conn->prepare("UPDATE usuarios SET clave = ? WHERE id = ?");
    $stmt->bind_param("si", $nueva_clave, $id);
   if ($stmt->execute()) {
            header("Location: usuarios.php");
            exit;
        } else {
            $error = "Error al actualizar contraseña.";
        }
}
?>
<?php include '../estructura/header.php'; ?>
<h2>Resetear contraseña para <?= htmlspecialchars($usuario['usuario']) ?></h2>
<form method="POST">
    Nueva contraseña: <input type="password" name="nueva_clave" required><br>
    <button type="submit" " onclick="return confirm('Clave cambiada con exito')">Guardar</button>
</form>
