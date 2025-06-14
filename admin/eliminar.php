<?php
include '../includes/auth.php';
include '../includes/db.php';

if (!es_admin()) { exit('Acceso denegado'); }

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $conn->prepare("SELECT archivo FROM programas WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $res = $stmt->get_result()->fetch_assoc();
    unlink("../uploads/" . $res['archivo']);

    $stmt = $conn->prepare("DELETE FROM programas WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: dashboard.php");
}
?>
