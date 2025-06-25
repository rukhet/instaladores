<!-- views/usuarios/usuarios.php -->
<?php include '../estructura/header.php'; ?>
<h2>Administrar Usuarios</h2>

<form method="POST" action="../controllers/usuario.php?accion=agregar" class="form-horizontal">
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
            <td>
                <div class="acciones">
                    <?php
                        $esPropio = $u['usuario'] === $usuario_logueado;
                        $esEditable = (es_root() || (es_admin() && $u['rol'] === 'usuario')) && !$esPropio;
                    ?>
                    <?php if ($esEditable): ?>
                        <a class="btn-editar" href="../controllers/usuario.php?accion=editar&id=<?= $u['id'] ?>">Editar</a>
                        <a class="btn-eliminar" href="../controllers/usuario.php?accion=eliminar&id=<?= $u['id'] ?>" onclick="return confirm('¿Eliminar este usuario?')">Eliminar</a>
                        <a class="btn-descargar" href="../controllers/usuario.php?accion=resetear&id=<?= $u['id'] ?>">Resetear contraseña</a>
                    <?php elseif (!$esPropio): ?>
                        <a class="btn-descargar" href="../controllers/usuario.php?accion=resetear&id=<?= $u['id'] ?>">Resetear contraseña</a>
                    <?php else: ?>
                        -
                    <?php endif; ?>
                </div>
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
