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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

</head>
<body>
    <?php include '../estructura/header.php'; ?>
    <div class="parent">
        <div class="div2">
            <!-- Tabla -->
            <div class="tabla-container">
                <input type="text" id="busqueda" placeholder="Buscar por nombre..." autocomplete="off" />

                <table>
                    <thead>
                        <tr>
                            <th>Icono</th>
                            <th>Nombre</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td onclick='mostrarDetalles(<?= json_encode($row, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>)'>
                                    <?php if ($row['icono']): ?>
                                        <img src="../uploads/icons/<?= htmlspecialchars($row['icono']) ?>" class="programa-icono" alt="Icono" />
                                    <?php endif; ?>
                                </td>
                                <td><?= htmlspecialchars($row['nombre']) ?></td>
                                <td class="acciones">
                                    <a class="btn-descargar" href="../uploads/<?= htmlspecialchars($row['archivo']) ?>">Descargar</a>
                                    <?php if (es_admin()|| es_root()): ?>
                                        <a class="btn-editar" href="editar_programa.php?id=<?= $row['id'] ?>">Editar</a>
                                        <a class="btn-eliminar" href="eliminar.php?id=<?= $row['id'] ?>" onclick="return confirm('¿Eliminar este programa?')">Eliminar</a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

            <!-- Detalles a la derecha -->
            <div id="detalles-programa">
                <h3 class="detalle-titulo">
                    <span id="detalle-icono-container"></span>
                    <span id="detalle-nombre"></span>
                </h3>
                <div class="detalle-campo"><strong>Versión:</strong> <span id="detalle-version"></span></div>
                <div class="detalle-campo"><strong>Descripción:</strong> <span id="detalle-descripcion"></span></div>
                <div class="detalle-campo"><strong>Archivo:</strong> <span id="detalle-archivo"></span></div>
                <div class="detalle-campo"><strong>Fecha:</strong> <span id="detalle-fecha"></span></div>
            </div>
        </div>
    </div>

    <script>
        function mostrarDetalles(programa) {
            const iconoContainer = document.getElementById("detalle-icono-container");
            iconoContainer.innerHTML = programa.icono
                ? `<img src="../uploads/icons/${programa.icono}" class="icono-detalle" alt="Icono">`
                : "";

            document.getElementById("detalle-nombre").textContent = programa.nombre || '';
            document.getElementById("detalle-version").textContent = programa.version || '';
            document.getElementById("detalle-descripcion").textContent = programa.descripcion || '';
            document.getElementById("detalle-archivo").textContent = programa.archivo || '';
            document.getElementById("detalle-fecha").textContent = new Date(programa.fecha_subida).toLocaleDateString();

            document.getElementById("detalles-programa").style.display = "block";
        }

        // Filtro de búsqueda en vivo
        document.getElementById('busqueda').addEventListener('input', function () {
            const filtro = this.value.toLowerCase();
            const filas = document.querySelectorAll('.tabla-container tbody tr');

            filas.forEach(fila => {
                const nombre = fila.cells[1].textContent.toLowerCase();
                if (nombre.includes(filtro)) {
                    fila.style.display = '';
                } else {
                    fila.style.display = 'none';
                }
            });
        });
    </script>
</body>
</html>