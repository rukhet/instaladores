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
        // Crear directorio si no existe
        if (!file_exists('../uploads/icons')) {
            mkdir('../uploads/icons', 0777, true);
        }
        move_uploaded_file($_FILES['icono']['tmp_name'], $destino_icono);
    }

    if (move_uploaded_file($_FILES['archivo']['tmp_name'], $destino)) {
        $stmt = $conn->prepare("INSERT INTO programas (nombre, archivo, descripcion, version, icono) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $nombre, $archivo, $descripcion, $version, $icono);
        
        if ($stmt->execute()) {
            header("Location: dashboard.php");
            exit();
        } else {
            $error = "Error al guardar en la base de datos";
        }
    } else {
        $error = "Error al subir el archivo principal";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subir Nuevo Software</title>


</head>
<body>

    <?php include '../estructura/header.php'; ?>

<?php if (!empty($error)): ?>
  <div class="mensaje-error">
    <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?>
  </div>
<?php endif; ?>

<form class="form-datos" method="POST" enctype="multipart/form-data">
  <div class="form-group">
    <label for="nombre"><i class="fas fa-tag"></i> Nombre del programa:</label>
    <input type="text" name="nombre" id="nombre" required placeholder="Ej: Photoshop 2023">
  </div>
  
  <div class="form-group">
    <label for="version"><i class="fas fa-code-branch"></i> Versión:</label>
    <input type="text" name="version" id="version" required placeholder="Ej: 1.0.0">
  </div>
  
  <div class="form-group">
    <label for="descripcion"><i class="fas fa-align-left"></i> Descripción:</label>
    <textarea name="descripcion" id="descripcion" placeholder="Describe las características del programa..."></textarea>
  </div>
  
  <div class="form-group">
    <label><i class="fas fa-file-archive"></i> Archivo del programa:</label>
    <div class="file-input-container">
      <div class="file-input-button">
        <i class="fas fa-cloud-upload-alt"></i> Seleccionar archivo
        <input type="file" name="archivo" id="archivo" class="file-input" required>
      </div>
      <div class="file-name" id="archivo-name">Ningún archivo seleccionado</div>
    </div>
  </div>
  
  <div class="form-group">
    <label><i class="fas fa-image"></i> Ícono (opcional):</label>
    <div class="file-input-container">
      <div class="file-input-button">
        <i class="fas fa-file-image"></i> Seleccionar imagen
        <input type="file" name="icono" id="icono" class="file-input" accept="image/*">
      </div>
      <div class="file-name" id="icono-name">Ningún archivo seleccionado</div>
    </div>
  </div>
  
  <button type="submit">
    <i class="fas fa-paper-plane"></i> Subir Programa
  </button>
</form>

    <script>
        // Mostrar nombres de archivos seleccionados
        document.getElementById('archivo').addEventListener('change', function(e) {
            const fileName = e.target.files[0] ? e.target.files[0].name : 'Ningún archivo seleccionado';
            document.getElementById('archivo-name').textContent = fileName;
        });
        
        document.getElementById('icono').addEventListener('change', function(e) {
            const fileName = e.target.files[0] ? e.target.files[0].name : 'Ningún archivo seleccionado';
            document.getElementById('icono-name').textContent = fileName;
        });
    </script>
</body>
</html>