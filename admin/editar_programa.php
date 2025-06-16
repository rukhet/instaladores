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
    <style>
        /* Estilos generales */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
        }

        /* Cabecera */
        .div1 {
            background-color: #2c3e50;
            padding: 15px 20px;
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .div1 h1 {
            margin: 0;
            font-size: 1.5rem;
        }

        .div1 nav ul {
            list-style: none;
            margin: 0;
            padding: 0;
            display: flex;
            gap: 15px;
        }

        .div1 nav a {
            color: white;
            text-decoration: none;
            padding: 5px 10px;
            border-radius: 3px;
            transition: background-color 0.3s;
        }

        .div1 nav a:hover {
            background-color: #3498db;
        }

        /* Formulario */
        form {
            background-color: white;
            padding: 20px;
            margin: 20px auto;
            max-width: 800px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .form-group {
            margin-bottom: 15px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        input[type="text"],
        textarea,
        input[type="file"] {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }

        textarea {
            height: 100px;
            resize: vertical;
        }

        button[type="submit"] {
            background-color: #3498db;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s;
        }

        button[type="submit"]:hover {
            background-color: #2980b9;
        }

        /* Mensajes */
        .mensaje-exito {
            color: #27ae60;
            background-color: #d5f5e3;
            padding: 10px;
            border-radius: 4px;
            margin: 20px auto;
            max-width: 800px;
        }

        .mensaje-error {
            color: #e74c3c;
            background-color: #fadbd8;
            padding: 10px;
            border-radius: 4px;
            margin: 20px auto;
            max-width: 800px;
        }

        /* Archivo actual */
        .archivo-actual {
            display: flex;
            align-items: center;
            gap: 10px;
            margin: 10px 0;
        }

        .icono-actual {
            width: 32px;
            height: 32px;
            object-fit: contain;
        }

        /* Botón volver */
        .volver {
            display: inline-block;
            background-color: #2c3e50;
            color: white;
            padding: 10px 15px;
            border-radius: 4px;
            text-decoration: none;
            margin: 20px;
            transition: background-color 0.3s;
        }

        .volver:hover {
            background-color: #1a252f;
        }

        /* Responsividad */
        @media (max-width: 768px) {
            .div1 {
                flex-direction: column;
                gap: 10px;
            }
            
            form {
                margin: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="div1">
        <h1>Nombre del Sitio</h1>
        <nav>
            <ul>
                <li><a href="dashboard.php">Inicio</a></li>
                <?php if (es_admin()): ?>
                <li><a href="subir.php">Subir instalador</a></li>
                <li><a href="usuarios.php">Administrar usuarios</a></li>
                <?php endif; ?>
                <li><a href="/instaladores/logout.php">Cerrar sesión</a></li>
            </ul>
        </nav>
    </div>

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