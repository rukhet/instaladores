<?php
include '../includes/auth.php';
include '../includes/db.php';
if (!es_admin() && !es_root()) exit('Acceso denegado');



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
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Administrar Usuarios</title>
    <style>
        /* Puedes poner aquí tus estilos o linkear el CSS que tienes */
        busquedaUsuario {
            margin-bottom: 15px;
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
                <option value="usuario">Usuario</option>
                <option value="admin">Admin</option>
            </select>
        </div>

        <button type="submit">Agregar</button>
    </form>

    <!-- Input búsqueda -->
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
                    <?php if (es_admin()|| es_root()): ?>
                        <a class="btn-editar" href="editar_usuario.php?id=<?= $u['id'] ?>">Editar</a>
                        <?php if ($u['rol'] === 'usuario'): ?>
                            <a class="btn-eliminar" href="eliminar_usuario.php?id=<?= $u['id'] ?>" onclick="return confirm('¿Eliminar este usuario?')">Eliminar</a>
                        <?php endif; ?>
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
                const celdaUsuario = filas[i].getElementsByTagName('td')[1]; // columna usuario
                if (celdaUsuario) {
                    const textoUsuario = celdaUsuario.textContent.toLowerCase();
                    filas[i].style.display = textoUsuario.includes(filtro) ? '' : 'none';
                }
            }
        });
    </script>

</body>
</html>
