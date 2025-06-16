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
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
        }

        .parent {
            display: flex;
            flex-direction: column;
            height: 100vh;
        }

        .div1 {
            background-color: #333;
            color: white;
            padding: 1rem;
        }

        .div1 h1 {
            margin: 0;
        }

        .div1 nav ul {
            list-style: none;
            padding: 0;
            display: flex;
            gap: 1rem;
        }

        .div1 nav a {
            color: white;
            text-decoration: none;
        }

        .div2 {
            display: flex;
            flex: 1;
            overflow: hidden;
        }

        .tabla-container {
            width: 70%;
            overflow-y: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        th, td {
            border: 1px solid #ccc;
            padding: 1rem;
            text-align: left;
            vertical-align: middle;
        }

        th:nth-child(1), td:nth-child(1) {
            width: 64px;
            text-align: center;
        }

        th:nth-child(2), td:nth-child(2) {
            width: 30%;
        }

        th:nth-child(3), td:nth-child(3) {
            width: auto;
        }

        .programa-icono {
            width: 48px;
            height: 48px;
            cursor: pointer;
        }

        .acciones a {
            display: inline-block;
            margin: 0.25rem;
            padding: 0.4rem 0.6rem;
            background-color: #eee;
            text-decoration: none;
            border-radius: 4px;
        }

        #detalles-programa {
            width: 30%;
            background-color: #f5f5f5;
            padding: 1rem;
            display: none;
            border-left: 1px solid #ccc;
        }

        .detalle-titulo {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .icono-detalle {
            width: 64px;
            height: 64px;
        }

        .detalle-campo {
            margin: 1rem 0;
        }

        @media (max-width: 992px) {
            .div2 {
                flex-direction: column;
            }

            .tabla-container, #detalles-programa {
                width: 100%;
            }

            #detalles-programa {
                border-left: none;
                border-top: 1px solid #ccc;
            }
        }
    </style>
</head>
<body>
     <?php include '../estructura/header.php'; ?>
    <div class="parent">

        <div class="div2">
            <!-- Tabla -->
            <div class="tabla-container">
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
                                        <img src="../uploads/icons/<?= htmlspecialchars($row['icono']) ?>" class="programa-icono" alt="Icono">
                                    <?php endif; ?>
                                </td>
                                <td><?= htmlspecialchars($row['nombre']) ?></td>
                                <td class="acciones">
                                    <a href="../uploads/<?= htmlspecialchars($row['archivo']) ?>">Descargar</a>
                                    <?php if (es_admin()): ?>
                                        <a href="editar_programa.php?id=<?= $row['id'] ?>">Editar</a>
                                        <a href="eliminar.php?id=<?= $row['id'] ?>" onclick="return confirm('¿Eliminar este programa?')">Eliminar</a>
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
    </script>
</body>
</html>
