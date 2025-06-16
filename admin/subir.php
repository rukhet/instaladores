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
    <style>
        :root {
            --color-primary: #3498db;
            --color-primary-hover: #2980b9;
            --color-secondary: #2ecc71;
            --color-danger: #e74c3c;
            --color-warning: #f39c12;
            --color-dark: #2c3e50;
            --color-light: #ecf0f1;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f8f9fa;
            color: #34495e;
            line-height: 1.6;
        }
        
        .div1 {
            background: linear-gradient(135deg, var(--color-dark), #1a252f);
            padding: 1rem 2rem;
            color: white;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
        }
        
        .div1 h1 {
            margin: 0;
            font-size: 1.8rem;
            font-weight: 600;
        }
        
        .div1 nav ul {
            list-style: none;
            margin: 0;
            padding: 0;
            display: flex;
            gap: 1.5rem;
        }
        
        .div1 nav a {
            color: white;
            text-decoration: none;
            padding: 0.5rem 1rem;
            border-radius: 4px;
            transition: all 0.3s ease;
            font-weight: 500;
            display: flex;
            align-items: center;
        }
        
        .div1 nav a:hover {
            background-color: rgba(255,255,255,0.2);
            transform: translateY(-2px);
        }
        
        .div1 nav a::before {
            content: "→";
            margin-right: 8px;
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        
        .div1 nav a:hover::before {
            opacity: 1;
        }
        
        .form-datos {
            background-color: white;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            max-width: 700px;
            margin: 0 auto;
            border: 1px solid #e0e0e0;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: var(--color-dark);
            font-size: 1rem;
        }
        
        input[type="text"],
        input[type="file"],
        textarea {
            width: 100%;
            padding: 0.8rem;
            border: 2px solid #ddd;
            border-radius: 6px;
            box-sizing: border-box;
            font-size: 1rem;
            transition: border-color 0.3s ease;
        }
        
        input[type="text"]:focus,
        input[type="file"]:focus,
        textarea:focus {
            border-color: var(--color-primary);
            outline: none;
            box-shadow: 0 0 0 3px rgba(52,152,219,0.2);
        }
        
        textarea {
            height: 120px;
            resize: vertical;
        }
        
        /* Botón principal */
        button[type="submit"] {
            background: linear-gradient(135deg, var(--color-secondary), #27ae60);
            color: white;
            border: none;
            padding: 0.8rem 1.5rem;
            border-radius: 6px;
            cursor: pointer;
            font-size: 1rem;
            font-weight: 600;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 6px rgba(46,204,113,0.3);
        }
        
        button[type="submit"]:hover {
            background: linear-gradient(135deg, #27ae60, var(--color-secondary));
            transform: translateY(-2px);
            box-shadow: 0 6px 8px rgba(46,204,113,0.4);
        }
        
        button[type="submit"]::after {
            content: "↑";
            margin-left: 8px;
            transition: transform 0.3s ease;
        }
        
        button[type="submit"]:hover::after {
            transform: translateY(-3px);
        }
        
        /* Mensajes de error */
        .mensaje-error {
            color: white;
            background: linear-gradient(135deg, var(--color-danger), #c0392b);
            padding: 1rem;
            border-radius: 6px;
            margin-bottom: 1.5rem;
            text-align: center;
            font-weight: 500;
            box-shadow: 0 4px 6px rgba(231,76,60,0.3);
            max-width: 700px;
            margin-left: auto;
            margin-right: auto;
        }
        
        /* Estilos para los inputs de archivo */
        .file-input-container {
            position: relative;
            overflow: hidden;
            display: inline-block;
            width: 100%;
        }
        
        .file-input-button {
            background: linear-gradient(135deg, var(--color-primary), var(--color-primary-hover));
            color: white;
            padding: 0.6rem 1.2rem;
            border-radius: 6px;
            font-size: 0.9rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        
        .file-input-button:hover {
            background: linear-gradient(135deg, var(--color-primary-hover), var(--color-primary));
        }
        
        .file-input-button i {
            font-size: 1.2rem;
        }
        
        .file-input {
            position: absolute;
            left: 0;
            top: 0;
            opacity: 0;
            width: 100%;
            height: 100%;
            cursor: pointer;
        }
        
        .file-name {
            margin-top: 0.5rem;
            font-size: 0.9rem;
            color: #7f8c8d;
            font-style: italic;
        }
        
        /* Responsividad */
        @media (max-width: 768px) {
            .div1 {
                padding: 1rem;
            }
            
            .div1 nav ul {
                flex-direction: column;
                gap: 0.5rem;
            }
            
            .form-datos {
                padding: 1.5rem;
            }
            
            button[type="submit"] {
                width: 100%;
            }
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="div1">
        <h1>Nombre del Sitio</h1>
        <nav>
            <ul>
                <li><a href="dashboard.php"><i class="fas fa-home"></i> Inicio</a></li>
                <?php if (es_admin()): ?>
                <li><a href="subir.php"><i class="fas fa-upload"></i> Subir instalador</a></li>
                <li><a href="usuarios.php"><i class="fas fa-users-cog"></i> Administrar usuarios</a></li>
                <?php endif; ?>
                <li><a href="/instaladores/logout.php"><i class="fas fa-sign-out-alt"></i> Cerrar sesión</a></li>
            </ul>
        </nav>
    </div>

    <?php if (!empty($error)): ?>
        <div class="mensaje-error">
            <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
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