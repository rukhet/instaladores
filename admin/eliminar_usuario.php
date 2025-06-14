<?php
require '../includes/auth.php';
require '../includes/db.php';

if (!es_admin()) {
    header("Location: dashboard.php");
    exit;
}

$id = intval($_GET['id'] ?? 0);

// Buscar el usuario a eliminar
$stmt = $conn->prepare("SELECT * FROM usuarios WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows === 0) {
    die("Usuario no encontrado.");
}

$usuario = $res->fetch_assoc();

// Solo permitir eliminar si es rol usuario
if ($usuario['rol'] !== 'usuario') {
    die("No está permitido eliminar administradores.");
}

$stmt = $conn->prepare("DELETE FROM usuarios WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();

header("Location: usuarios.php");
exit;
