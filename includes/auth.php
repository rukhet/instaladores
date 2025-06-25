<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['usuario'])) {
    header('Location: /instaladores/login.php');
    exit;
}

function es_admin() {
    return isset($_SESSION['rol']) && $_SESSION['rol'] === 'admin';
}

function es_root() {
    return isset($_SESSION['rol']) && $_SESSION['rol'] === 'root';
}
?>
