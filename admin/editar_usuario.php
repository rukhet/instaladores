<?php
require '../includes/auth.php';
require '../includes/db.php';

if (!es_admin()) {
    header("Location: dashboard.php");
    exit;
}

$id = intval($_GET['id'] ?? 0);

// Obtener datos actuales
$stmt = $conn->prepare("SELECT * FROM usuarios WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result();
if ($res->num_rows === 0) {
    die("Usuario no encontrado.");
}
$usuario = $res->fetch_assoc();

// Procesar actualización
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nuevo_usuario = $_POST['usuario'];
    $nuevo_rol = $_POST['rol'];

    // Si es admin, permitir modificar cualquier usuario (incluyendo rol)
    $stmt = $conn->prepare("UPDATE usuarios SET usuario = ?, rol = ? WHERE id = ?");
    $stmt->bind_param("ssi", $nuevo_usuario, $nuevo_rol, $id);
    
    if ($stmt->execute()) {
        header("Location: usuarios.php");
        exit;
    } else {
        $error = "Error al actualizar el usuario";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Usuario</title>
    <style>
        /* Estilos generales */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
            color: #333;
        }

        /* Cabecera */
        .div1 {
            background-color: #2c3e50;
            padding: 15px 20px;
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
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

        /* Contenedor principal */
        .contenedor {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }

        /* Formulario */
        form {
            background-color: white;
            padding: 25px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #2c3e50;
        }

        input[type="text"],
        select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
            box-sizing: border-box;
        }

        select {
            height: 40px;
            background-color: white;
        }

        /* Mensajes de error */
        .mensaje-error {
            color: #e74c3c;
            background-color: #fadbd8;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 15px;
        }

        /* Botones */
        button[type="submit"] {
            background-color: #3498db;
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s;
        }

        button[type="submit"]:hover {
            background-color: #2980b9;
        }

        .btn-volver {
            display: inline-block;
            background-color: #6c757d;
            color: white;
            padding: 10px 15px;
            border-radius: 4px;
            text-decoration: none;
            transition: background-color 0.3s;
        }

        .btn-volver:hover {
            background-color: #5a6268;
        }

        /* Responsividad */
        @media (max-width: 768px) {
            .div1 {
                flex-direction: column;
                gap: 10px;
                text-align: center;
            }
            
            .div1 nav ul {
                flex-direction: column;
                gap: 5px;
            }
            
            .contenedor {
                padding: 15px;
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

    <div class="contenedor">
        <?php if (isset($error)): ?>
            <div class="mensaje-error"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label for="usuario">Usuario:</label>
                <input type="text" name="usuario" id="usuario" value="<?= htmlspecialchars($usuario['usuario']) ?>" required>
            </div>

            <div class="form-group">
                <label for="rol">Rol:</label>
                <select name="rol" id="rol">
                    <option value="usuario" <?= $usuario['rol'] === 'usuario' ? 'selected' : '' ?>>Usuario</option>
                    <option value="admin" <?= $usuario['rol'] === 'admin' ? 'selected' : '' ?>>Administrador</option>
                </select>
            </div>

            <button type="submit">Guardar cambios</button>
        </form>

        <a href="usuarios.php" class="btn-volver">Volver a la lista de usuarios</a>
    </div>
</body>
</html>