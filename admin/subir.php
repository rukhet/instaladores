<?php
include '../includes/auth.php';
include '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['archivo'])) {
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $version = $_POST['version'];

    // Archivo principal
    $archivo = $_FILES['archivo']['name'];
    $destino = "../uploads/" . basename($archivo);

    // Ícono (opcional)
    $icono = null;
    if (isset($_FILES['icono']) && $_FILES['icono']['error'] === 0) {
        $icono = $_FILES['icono']['name'];
        $destino_icono = "../uploads/icons/" . basename($icono);
        // Asegúrate que exista la carpeta ../uploads/icons/
        move_uploaded_file($_FILES['icono']['tmp_name'], $destino_icono);
    }

    if (move_uploaded_file($_FILES['archivo']['tmp_name'], $destino)) {
        // Cambié la consulta para incluir el campo icono
        $stmt = $conn->prepare("INSERT INTO programas (nombre, archivo, descripcion, version, icono) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $nombre, $archivo, $descripcion, $version, $icono);
        $stmt->execute();
        echo "<p>Archivo subido correctamente.</p>";
    } else {
        echo "<p>Error al subir el archivo.</p>";
    }
}
?>
<form method="POST" enctype="multipart/form-data">
    Nombre: <input name="nombre" required><br>
    Versión: <input name="version" required><br>
    Descripción: <textarea name="descripcion"></textarea><br>
    Archivo del programa: <input type="file" name="archivo" required><br>
    Ícono (imagen): <input type="file" name="icono" accept="image/*"><br>
    <button type="submit">Subir</button>
</form>
<a href="dashboard.php">Volver al panel</a>
