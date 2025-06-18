<?php
include '../includes/auth.php';
include '../includes/db.php';

if (!es_admin() && !es_root()) exit('Acceso denegado');

$usuario_logueado = $_SESSION['usuario'];

// Roles permitidos para asignación
$rolPermitido = ['usuario'];
if (es_root()) {
    $rolPermitido = ['usuario', 'admin', 'root'];
}

// Procesar nuevo usuario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = trim($_POST['usuario']);
    $clave = password_hash($_POST['clave'], PASSWORD_DEFAULT);
    $rol = $_POST['rol'];

    if (!in_array($rol, $rolPermitido)) {
        exit('No tienes permiso para asignar ese rol.');
    }

    $stmt = $conn->prepare("INSERT INTO usuarios (usuario, clave, rol) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $usuario, $clave, $rol);
    $stmt->execute();
}

// Listar usuarios
if (es_root()) {
    $usuarios = $conn->query("SELECT id, usuario, rol FROM usuarios");
} else {
    $usuarios = $conn->query("SELECT id, usuario, rol FROM usuarios WHERE rol != 'root'");
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Administrar Usuarios</title>
    <link rel="stylesheet" href="../css/estilo.css">
    <style>
        #busquedaUsuario {
            margin: 15px 0;
            padding: 8px;
            font-size: 1rem;
            width: 100%;
            max-width: 300px;
            border: 1px solid #ccc;
            border-radius: 6px;
        }
    </style>
</head>
<body>
<?php include '../estructura/header.php'; ?>

    <form method="POST" class="form-horizontal">
        <div class="form-group">
            <label for="usuario">Usuario:</label>
            <input type="text" name="usuario" id="usuario" required />
        </div>

        <div class="form-group">
            <label for="clave">Clave:</label>
            <input type="password" name="clave" id="clave" required />
        </div>

        <div class="form-group">
            <label for="rol">Rol:</label>
            <select name="rol" id="rol">
                <?php foreach ($rolPermitido as $rol): ?>
                    <option value="<?= $rol ?>"><?= ucfirst($rol) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <button type="submit">Agregar</button>
    </form>

    <input type="text" id="busquedaUsuario" placeholder="Buscar usuario por nombre..." />

    <table id="tablaUsuarios">
        <thead>
            <tr>
                <th>ID</th>
                <th>Usuario</th>
                <th>Rol</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
        <?php while ($u = $usuarios->fetch_assoc()): ?>
            <tr>
                <td><?= $u['id'] ?></td>
                <td><?= htmlspecialchars($u['usuario']) ?></td>
                <td><?= htmlspecialchars($u['rol']) ?></td>
                <td class="acciones">
                    <?php
                        $esPropio = $u['usuario'] === $usuario_logueado;
                        $esEditable = (es_root() || (es_admin() && $u['rol'] === 'usuario')) && !$esPropio;
                    ?>
                    <?php if ($esEditable): ?>
                        <a class="btn-editar" href="editar_usuario.php?id=<?= $u['id'] ?>">Editar</a>
                        <a class="btn-eliminar" href="eliminar_usuario.php?id=<?= $u['id'] ?>" onclick="return confirm('¿Eliminar este usuario?')">Eliminar</a>
                        <a class="btn-descargar" href="resetear_contrasena.php?id=<?= $u['id'] ?>">Resetear contraseña</a>
                    <?php elseif (!$esPropio): ?>
                        <a class="btn-descargar" href="resetear_contrasena.php?id=<?= $u['id'] ?>">Resetear contraseña</a>
                    <?php else: ?>
                        -
                    <?php endif; ?>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>

    <script>
        const inputBusqueda = document.getElementById('busquedaUsuario');
        const tabla = document.getElementById('tablaUsuarios').getElementsByTagName('tbody')[0];

        inputBusqueda.addEventListener('input', function () {
            const filtro = this.value.toLowerCase();
            const filas = tabla.getElementsByTagName('tr');

            for (let i = 0; i < filas.length; i++) {
                const celdaUsuario = filas[i].getElementsByTagName('td')[1];
                if (celdaUsuario) {
                    const textoUsuario = celdaUsuario.textContent.toLowerCase();
                    filas[i].style.display = textoUsuario.includes(filtro) ? '' : 'none';
                }
            }
        });
    </script>

</body>
</html>
