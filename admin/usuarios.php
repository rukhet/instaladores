<?php
include '../includes/auth.php';
include '../includes/db.php';
if (!es_admin()) exit('Acceso denegado');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = $_POST['usuario'];
    $clave = password_hash($_POST['clave'], PASSWORD_DEFAULT);
    $rol = $_POST['rol'];
    $stmt = $conn->prepare("INSERT INTO usuarios (usuario, clave, rol) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $usuario, $clave, $rol);
    $stmt->execute();
}

$usuarios = $conn->query("SELECT id, usuario, rol FROM usuarios");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrar Usuarios</title>
    <style>
        /* Estilos generales */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
            color: #333;
        }

        /* Panel de control */
        h2 {
            color: #2c3e50;
            border-bottom: 2px solid #3498db;
            padding-bottom: 10px;
            margin-top: 0;
        }

        /* Formulario HORIZONTAL */
        .form-horizontal {
            background-color: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 15px;
        }

        .form-horizontal .form-group {
            display: flex;
            align-items: center;
            margin-bottom: 0;
        }

        .form-horizontal label {
            margin-right: 10px;
            margin-bottom: 0;
            white-space: nowrap;
        }

        .form-horizontal input[type="text"],
        .form-horizontal input[type="password"],
        .form-horizontal select {
            width: auto;
            min-width: 150px;
            margin-bottom: 0;
        }

        /* Tabla */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            background-color: white;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        th, td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        /* Enlaces de acciones */
        .acciones a {
            color: #3498db;
            text-decoration: none;
            margin-right: 10px;
            transition: color 0.3s;
        }

        .acciones a:hover {
            color: #2980b9;
            text-decoration: underline;
        }

        /* Botón volver */
        .volver {
            display: inline-block;
            background-color: #2c3e50;
            color: white;
            padding: 10px 15px;
            border-radius: 4px;
            text-decoration: none;
            transition: background-color 0.3s;
        }

        .volver:hover {
            background-color: #1a252f;
        }

        /* Responsividad */
        @media (max-width: 768px) {
            .form-horizontal {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .form-horizontal .form-group {
                width: 100%;
                margin-bottom: 10px;
            }
            
            .form-horizontal input[type="text"],
            .form-horizontal input[type="password"],
            .form-horizontal select {
                width: 100%;
            }
            
            .acciones a {
                display: block;
                margin: 5px 0;
            }
        }
    </style>
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
</head>
<body>
    
    <form method="POST" class="form-horizontal">
        <div class="form-group">
            <label for="usuario">Usuario:</label>
            <input type="text" name="usuario" id="usuario" required>
        </div>
        
        <div class="form-group">
            <label for="clave">Clave:</label>
            <input type="password" name="clave" id="clave" required>
        </div>
        
        <div class="form-group">
            <label for="rol">Rol:</label>
            <select name="rol" id="rol">
                <option value="usuario">Usuario</option>
                <option value="admin">Admin</option>
            </select>
        </div>
        
        <button type="submit">Agregar</button>
    </form>

    <table>
        <tr>
            <th>ID</th>
            <th>Usuario</th>
            <th>Rol</th>
            <th>Acciones</th>
        </tr>
        <?php while ($u = $usuarios->fetch_assoc()): ?>
        <tr>
            <td><?= $u['id'] ?></td>
            <td><?= htmlspecialchars($u['usuario']) ?></td>
            <td><?= htmlspecialchars($u['rol']) ?></td>
            <td class="acciones">
                <?php if (es_admin()): ?>
                    <a href="editar_usuario.php?id=<?= $u['id'] ?>">Editar</a>
                    <?php if ($u['rol'] === 'usuario'): ?>
                        <a href="eliminar_usuario.php?id=<?= $u['id'] ?>" onclick="return confirm('¿Eliminar este usuario?')">Eliminar</a>
                    <?php endif; ?>
                    <a href="resetear_contrasena.php?id=<?= $u['id'] ?>">Resetear contraseña</a>
                <?php else: ?>
                    -
                <?php endif; ?>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>

    <a href="dashboard.php" class="volver">Volver al panel</a>
</body>
</html>