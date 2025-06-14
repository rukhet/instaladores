<?php
session_start();
include 'includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = $_POST['usuario'];
    $clave = $_POST['clave'];

    $stmt = $conn->prepare("SELECT * FROM usuarios WHERE usuario = ?");
    $stmt->bind_param("s", $usuario);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows === 1) {
        $user = $res->fetch_assoc();
        if (password_verify($clave, $user['clave'])) {
            $_SESSION['usuario'] = $user['usuario'];
            $_SESSION['rol'] = $user['rol'];
            header("Location: admin/dashboard.php");
            exit;
        }
    }
    $error = "Credenciales inválidas";
}
?>
<form method="POST">
    Usuario: <input name="usuario"><br>
    Clave: <input name="clave" type="password"><br>
    <button type="submit">Ingresar</button>
    <?php if (isset($error)) echo "<p>$error</p>"; ?>
</form>
