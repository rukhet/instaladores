<?php
require '../includes/auth.php';
require '../includes/db.php';

if (!isset($_GET['id'])) {
    die("ID no especificado");
}

$id = intval($_GET['id']);

// Obtener datos actuales
$stmt = $conn->prepare("SELECT * FROM programas WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    die("Programa no encontrado");
}
$programa = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = $_POST['nombre'];
    $version = $_POST['version'];
    $descripcion = $_POST['descripcion'];

    // Actualizar archivo si se subió uno nuevo
    $archivo = $programa['archivo'];
    if (isset($_FILES['archivo']) && $_FILES['archivo']['error'] === 0) {
        $nuevoArchivo = $_FILES['archivo']['name'];
        $destino = "../uploads/" . basename($nuevoArchivo);
        if (move_uploaded_file($_FILES['archivo']['tmp_name'], $destino)) {
            if (file_exists("../uploads/" . $archivo)) {
                unlink("../uploads/" . $archivo);
            }
            $archivo = $nuevoArchivo;
        } else {
            echo "<p class='mensaje-error'>Error al subir el nuevo archivo.</p>";
        }
    }

    // Actualizar ícono si se subió uno nuevo
    $icono = $programa['icono'];
    if (isset($_FILES['icono']) && $_FILES['icono']['error'] === 0) {
        $nuevoIcono = $_FILES['icono']['name'];
        $destinoIcono = "../uploads/icons/" . basename($nuevoIcono);
        if (move_uploaded_file($_FILES['icono']['tmp_name'], $destinoIcono)) {
            if ($icono && file_exists("../uploads/icons/" . $icono)) {
                unlink("../uploads/icons/" . $icono);
            }
            $icono = $nuevoIcono;
        } else {
            echo "<p class='mensaje-error'>Error al subir el nuevo ícono.</p>";
        }
    }

    // Actualizar en base de datos
    $stmt = $conn->prepare("UPDATE programas SET nombre = ?, version = ?, descripcion = ?, archivo = ?, icono = ? WHERE id = ?");
    $stmt->bind_param("sssssi", $nombre, $version, $descripcion, $archivo, $icono, $id);
    if ($stmt->execute()) {
        echo "<p class='mensaje-exito'>Programa actualizado correctamente.</p>";
        header("Location: dashboard.php");
        exit;
    } else {
        echo "<p class='mensaje-error'>Error al actualizar el programa.</p>";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Programa</title>
    
</head>
<body>
 <?php include '../estructura/header.php'; ?>

    <form method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label for="nombre">Nombre:</label>
            <input type="text" name="nombre" id="nombre" value="<?= htmlspecialchars($programa['nombre']) ?>" required>
        </div>
        
        <div class="form-group">
            <label for="version">Versión:</label>
            <input type="text" name="version" id="version" value="<?= htmlspecialchars($programa['version']) ?>" required>
        </div>
        
        <div class="form-group">
            <label for="descripcion">Descripción:</label>
            <textarea name="descripcion" id="descripcion"><?= htmlspecialchars($programa['descripcion']) ?></textarea>
        </div>
        
        <div class="form-group">
            <label>Archivo actual:</label>
            <div class="archivo-actual">
                <a href="../uploads/<?= htmlspecialchars($programa['archivo']) ?>"><?= htmlspecialchars($programa['archivo']) ?></a>
            </div>
            <label for="archivo">Cambiar archivo:</label>
            <input type="file" name="archivo" id="archivo">
        </div>
        
        <div class="form-group">
            <label>Ícono actual:</label>
            <div class="archivo-actual">
                <?php if ($programa['icono']): ?>
                    <img src="../uploads/icons/<?= htmlspecialchars($programa['icono']) ?>" class="icono-actual">
                <?php endif; ?>
            </div>
            <label for="icono">Cambiar ícono:</label>
            <input type="file" name="icono" id="icono" accept="image/*">
        </div>
        
        <button type="submit">Actualizar</button>
    </form>

    <a href="dashboard.php" class="volver">Volver al panel</a>
</body>
</html>