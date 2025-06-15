<?php
session_start();
include 'includes/db.php';

// Si ya está logueado, redirigir al panel
if (isset($_SESSION['usuario'])) {
    header("Location: admin/dashboard.php");
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = $_POST['usuario'];
    $clave = $_POST['clave'];

    $stmt = $conn->prepare("SELECT * FROM usuarios WHERE usuario = ?");
    $stmt->bind_param("s", $usuario);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows === 1) {
        $usuario_data = $resultado->fetch_assoc();
        if (password_verify($clave, $usuario_data['clave'])) {
            $_SESSION['usuario'] = $usuario_data['usuario'];
            $_SESSION['rol'] = $usuario_data['rol'];
            header("Location: admin/dashboard.php");
            exit;
        }
    }

    $error = "Usuario o contraseña incorrectos";
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
      <link rel="stylesheet" href="./css/estilos.css">
      
    <meta charset="UTF-8">
    <title>Iniciar sesión</title>

  

</head>
<body class="login-container">
    <h2>Iniciar sesión</h2>
    <?php if ($error): ?>
        <p style="color:red;"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>
    <form class="login-container" method="POST">
        Usuario: <input type="text" name="usuario" required><br>
        Contraseña: <input type="password" name="clave" required><br>
        <button type="submit">Ingresar</button>
    </form>
</body>
</html>
