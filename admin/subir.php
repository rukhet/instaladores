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

<!DOCTYPE html>
<html lang="es">
<head>
      <link rel="stylesheet" href="./css/estilos-dashboard.css">
    
     
    <meta charset="UTF-8">
    <title>Subir Nuevo Software</title>

  

</head>
<div>
<h2>Panel de Control</h2>
</div>
<div class="menu"> 
<a href="dashboard.php">Volver al panel</a>
<?php if (es_admin()): ?>
   
<a href="subir.php">Subir instalador</a>
<a href="usuarios.php">Administrar usuarios</a>
<?php endif; ?>

 | <a href="/instaladores/logout.php">Cerrar sesión</a>
</div>
<form class="form-datos" method="POST" enctype="multipart/form-data">
    <div class="form-group">
    Nombre: <input name="nombre" required><br>
</div>
    <div class="form-group">
    Versión: <input name="version" required><br>
</div>
    <div class="form-group">
    Descripción: <textarea name="descripcion"></textarea><br>
</div>
    <div class="form-group">
    Archivo del programa: <input type="file" name="archivo" required><br>
</div>
    <div class="form-group">
    Ícono (imagen): <input type="file" name="icono" accept="image/*"><br>
</div>
    
    <button type="submit">Subir</button>
</form>

</html>