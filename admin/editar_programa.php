<?php

require '../includes/auth.php';
require '../includes/db.php';

// resto del código...

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
            // Opcional: eliminar archivo viejo
            if (file_exists("../uploads/" . $archivo)) {
                unlink("../uploads/" . $archivo);
            }
            $archivo = $nuevoArchivo;
        } else {
            echo "<p>Error al subir el nuevo archivo.</p>";
        }
    }

    // Actualizar ícono si se subió uno nuevo
    $icono = $programa['icono'];
    if (isset($_FILES['icono']) && $_FILES['icono']['error'] === 0) {
        $nuevoIcono = $_FILES['icono']['name'];
        $destinoIcono = "../uploads/icons/" . basename($nuevoIcono);
        if (move_uploaded_file($_FILES['icono']['tmp_name'], $destinoIcono)) {
            // Opcional: eliminar ícono viejo
            if ($icono && file_exists("../uploads/icons/" . $icono)) {
                unlink("../uploads/icons/" . $icono);
            }
            $icono = $nuevoIcono;
        } else {
            echo "<p>Error al subir el nuevo ícono.</p>";
        }
    }

    // Actualizar en base de datos
    $stmt = $conn->prepare("UPDATE programas SET nombre = ?, version = ?, descripcion = ?, archivo = ?, icono = ? WHERE id = ?");
    $stmt->bind_param("sssssi", $nombre, $version, $descripcion, $archivo, $icono, $id);
    if ($stmt->execute()) {
        echo "<p>Programa actualizado correctamente.</p>";
        // Recargar datos actualizados
        header("Location: dashboard.php");
        exit;
    } else {
        echo "<p>Error al actualizar el programa.</p>";
    }
}
?>

<form method="POST" enctype="multipart/form-data">
    Nombre: <input name="nombre" value="<?= htmlspecialchars($programa['nombre']) ?>" required><br>
    Versión: <input name="version" value="<?= htmlspecialchars($programa['version']) ?>" required><br>
    Descripción: <textarea name="descripcion"><?= htmlspecialchars($programa['descripcion']) ?></textarea><br>
    Archivo actual: <a href="../uploads/<?= htmlspecialchars($programa['archivo']) ?>"><?= htmlspecialchars($programa['archivo']) ?></a><br>
    Cambiar archivo: <input type="file" name="archivo"><br>
    Ícono actual:
    <?php if ($programa['icono']): ?>
        <img src="../uploads/icons/<?= htmlspecialchars($programa['icono']) ?>" style="width:32px;height:32px;">
    <?php endif; ?>
    <br>
    Cambiar ícono: <input type="file" name="icono" accept="image/*"><br>
    <button type="submit">Actualizar</button>
</form>

<a href="dashboard.php">Volver al panel</a>
