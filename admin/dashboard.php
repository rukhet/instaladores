<?php
include '../includes/auth.php';
include '../includes/db.php';

$result = $conn->query("SELECT * FROM programas ORDER BY fecha_subida DESC");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    
    <meta charset="UTF-8">
    <title>dashboard</title>
    <link rel="stylesheet" href="./css/estilos.css">
</head>
<h2>Panel de Control</h2>
<?php if (es_admin()): ?>
<a href="subir.php">Subir instalador</a>
<a href="usuarios.php">Administrar usuarios</a>
<?php endif; ?>
 | <a href="/instaladores/logout.php">Cerrar sesión</a>

<table border="1">
<tr>
    <th>Icono</th>
    <th>Nombre</th>
    <th>Versión</th>
    <th>Descripción</th>
    <th>Archivo</th>
    <th>Acciones</th>
</tr>
<?php while ($row = $result->fetch_assoc()): ?>
<tr>
    <td>
        <?php if ($row['icono']): ?>
            <img src="../uploads/icons/<?= urlencode($row['icono']) ?>" alt="Icono" style="width:32px;height:32px;">
        <?php endif; ?>
    </td>
    <td><?= htmlspecialchars($row['nombre']) ?></td>
    <td><?= htmlspecialchars($row['version']) ?></td>
    <td><?= htmlspecialchars($row['descripcion']) ?></td>
    <td><a href="../uploads/<?= urlencode($row['archivo']) ?>">Descargar</a></td>
    <td>
        <?php if (es_admin()): ?>
            <a href="editar_programa.php?id=<?= $row['id'] ?>">Editar</a> |
            <a href="eliminar.php?id=<?= $row['id'] ?>" onclick="return confirm('¿Seguro que quieres eliminar este programa?')">Eliminar</a>
        <?php endif; ?>
    </td>
</tr>
<?php endwhile; ?>
</table>
