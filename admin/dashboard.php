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
    <style>
        /* Estilos generales */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
        }

        /* Layout con grid */
        .parent {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            grid-template-rows: repeat(7, 1fr);
            gap: 8px;
            height: 100vh;
        }
        
        /* Cabecera con menú horizontal */
        .div1 {
            grid-column: span 7;
            grid-row: span 2;
            background: #ffffff;
            padding: 15px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            display: flex;
            flex-direction: column;
        }

        .div1 h1 {
            margin: 0 0 15px 0;
            color: #333;
        }

        .div1 nav ul {
            display: flex;
            gap: 15px;
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .div1 nav a {
            text-decoration: none;
            color: #333;
            padding: 8px 12px;
            background: #e9ecef;
            border-radius: 4px;
            transition: background 0.3s;
            font-size: 14px;
        }
        
        .div1 nav a:hover {
            background: #dee2e6;
        }

        /* Contenedor principal con tabla dividida */
        .div2 {
           grid-column: span 5 / span 5;
    grid-row: span 5 / span 5;
    grid-row-start: 3;
            /*overflow-y: auto;*/
            padding: 10px;
            background: #ffffff;
            border-radius: 4px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        .tabla-columnas {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        @media (max-width: 768px) {
            .tabla-columnas {
                grid-template-columns: 1fr;
            }
        }

        /* Estilos para las tablas */
        .programas-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        .programas-table th, .programas-table td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: left;
        }
        
        .programas-table th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        
        .programa-icono {
            width: 32px;
            height: 32px;
            cursor: pointer;
            transition: transform 0.2s;
        }
        
        .programa-icono:hover {
            transform: scale(1.1);
        }

        /* Estilos para botones/links */
        .descarga-link, .btn-editar, .btn-eliminar {
            padding: 6px 12px;
            border-radius: 4px;
            text-decoration: none;
            color: white;
            font-size: 12px;
            margin-right: 5px;
            display: inline-block;
            margin-top: 3px;
        }
        
        .descarga-link {
            background-color: #28a745;
        }
        
        .btn-editar {
            background-color: #ffc107;
            color: #000;
        }
        
        .btn-eliminar {
            background-color: #dc3545;
        }

        /* Panel de detalles */
        .div3 {
         grid-column: span 2 / span 2;
    grid-row: span 5 / span 5;
    grid-column-start: 6;
    grid-row-start: 3;
            padding: 15px;
            background: #ffffff;
            border-left: 1px solid #ddd;
            overflow-y: auto;
            box-shadow: -1px 0 3px rgba(0,0,0,0.1);
        }
        
        #detalles-programa {
            display: none;
        }
        
        .detalle-titulo {
            font-size: 20px;
            margin-bottom: 15px;
            color: #333;
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .detalle-campo {
            margin-bottom: 12px;
            line-height: 1.5;
        }
        
        .detalle-campo strong {
            display: inline-block;
            width: 100px;
            color: #555;
        }

        .icono-detalle {
            width: 32px;
            height: 32px;
            object-fit: contain;
        }
    </style>

  <div class="parent">
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
  
        <div class="div2">
            <div class="tabla-columnas">
                <!-- Primera columna de la tabla -->
                <table class="programas-table">
                    <thead>
                        <tr>
                            <th>Icono</th>
                            <th>Nombre</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $result->data_seek(0);
                        $count = 0;
                        while ($row = $result->fetch_assoc()): 
                            if ($count % 2 == 0):
                        ?>
                        <tr>
                            <td>
                                <?php if ($row['icono']): ?>
                                    <img src="../uploads/icons/<?= htmlspecialchars($row['icono']) ?>" 
                                         class="programa-icono"
                                         onclick="mostrarDetalles(<?= htmlspecialchars(json_encode($row)) ?>)">
                                <?php endif; ?>
                            </td>
                            <td>
                                <strong><?= htmlspecialchars($row['nombre']) ?></strong><br>
                         </td>
                            <td class="acciones">
                                <a href="../uploads/<?= htmlspecialchars($row['archivo']) ?>" class="descarga-link">Descargar</a>
                                <?php if (es_admin()): ?>
                                    <a href="editar_programa.php?id=<?= $row['id'] ?>" class="btn-editar">Editar</a>
                                    <a href="eliminar.php?id=<?= $row['id'] ?>" class="btn-eliminar" onclick="return confirm('¿Eliminar este programa?')">Eliminar</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php 
                            endif;
                            $count++;
                        endwhile; 
                        ?>
                    </tbody>
                </table>

                <!-- Segunda columna de la tabla -->
                <table class="programas-table">
                    <thead>
                        <tr>
                            <th>Icono</th>
                            <th>Nombre</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $result->data_seek(0);
                        $count = 0;
                        while ($row = $result->fetch_assoc()): 
                            if ($count % 2 != 0):
                        ?>
                        <tr>
                            <td>
                                <?php if ($row['icono']): ?>
                                    <img src="../uploads/icons/<?= htmlspecialchars($row['icono']) ?>" 
                                         class="programa-icono"
                                         onclick="mostrarDetalles(<?= htmlspecialchars(json_encode($row)) ?>)">
                                <?php endif; ?>
                            </td>
                            <td>
                                <strong><?= htmlspecialchars($row['nombre']) ?></strong><br>                    
                            </td>
                            <td class="acciones">
                                <a href="../uploads/<?= htmlspecialchars($row['archivo']) ?>" class="descarga-link">Descargar</a>
                                <?php if (es_admin()): ?>
                                    <a href="editar_programa.php?id=<?= $row['id'] ?>" class="btn-editar">Editar</a>
                                    <a href="eliminar.php?id=<?= $row['id'] ?>" class="btn-eliminar" onclick="return confirm('¿Eliminar este programa?')">Eliminar</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php 
                            endif;
                            $count++;
                        endwhile; 
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="div3" id="detalles-programa">
            <h3 class="detalle-titulo">
                <span id="detalle-icono-container">
                    <!-- El icono se insertará aquí -->
                </span>
                <span id="detalle-nombre"></span>
            </h3>
            <div class="detalle-campo">
                <strong>Versión:</strong>
                <span id="detalle-version"></span>
            </div>
            <div class="detalle-campo">
                <strong>Descripción:</strong>
                <span id="detalle-descripcion"></span>
            </div>
            <div class="detalle-campo">
                <strong>Archivo:</strong>
                <span id="detalle-archivo"></span>
            </div>
            <div class="detalle-campo">
                <strong>Fecha:</strong>
                <span id="detalle-fecha"></span>
            </div>
        </div>
    </div>

    <script>
        function mostrarDetalles(programa) {
            const detalleDiv = document.getElementById("detalles-programa");
            
            // Mostrar el icono
            const iconoContainer = document.getElementById("detalle-icono-container");
            if (programa.icono) {
                iconoContainer.innerHTML = `
                    <img src="../uploads/icons/${programa.icono}" alt="Icono" class="icono-detalle">
                `;
            } else {
                iconoContainer.innerHTML = '';
            }
            
            // Actualizar el resto de campos
            document.getElementById("detalle-nombre").textContent = programa.nombre;
            document.getElementById("detalle-version").textContent = programa.version;
            document.getElementById("detalle-descripcion").textContent = programa.descripcion;
            document.getElementById("detalle-archivo").textContent = programa.archivo;
            document.getElementById("detalle-fecha").textContent = new Date(programa.fecha_subida).toLocaleDateString();
            
            // Mostrar el panel de detalles
            detalleDiv.style.display = "block";
        }
    </script>
</body>
</html>