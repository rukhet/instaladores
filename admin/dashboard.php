<?php
include '../includes/auth.php';
include '../includes/db.php';

$result = $conn->query("SELECT * FROM programas ORDER BY fecha_subida DESC");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="../css/estilos-dashboard.css">
</head>
<body>
    <header class="nav">
        <h1>Nombre del Sitio</h1>
        <nav>
            <ul>
                <li><a href="#">Inicio</a></li>
                <?php if (es_admin()): ?>
                <li><a href="subir.php">Subir instalador</a></li>
                <li><a href="usuarios.php">Administrar usuarios</a></li>
                <?php endif; ?>
                <li><a href="/instaladores/logout.php">Cerrar sesión</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <table class="programas-table">
            <thead>
                <tr>
                    <th>Icono</th>
                    <th>Nombre</th>
                    <th>Versión</th>
                    <th>Descripción</th>
                    <th>Archivo</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td>
                        <?php if ($row['icono']): ?>
                            <img src="../uploads/icons/<?= urlencode($row['icono']) ?>" alt="Icono" class="programa-icono">
                        <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($row['nombre']) ?></td>
                    <td><?= htmlspecialchars($row['version']) ?></td>
                    <td><?= htmlspecialchars($row['descripcion']) ?></td>
                    <td><a href="../uploads/<?= urlencode($row['archivo']) ?>" class="descarga-link">Descargar</a></td>
                    <td class="acciones">
                        <?php if (es_admin()): ?>
                            <a href="editar_programa.php?id=<?= $row['id'] ?>" class="btn-editar">Editar</a>
                            <a href="eliminar.php?id=<?= $row['id'] ?>" class="btn-eliminar" onclick="return confirm('¿Seguro que quieres eliminar este programa?')">Eliminar</a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </main>
</body>
</html>