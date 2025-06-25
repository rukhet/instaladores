<?php include '../estructura/header.php'; ?>

<div class="contenedor">
    <h2>Editar Usuario</h2>

    <?php if (!empty($error)): ?>
        <div class="mensaje-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="form-group">
            <label for="usuario">Usuario:</label>
            <input type="text" name="usuario" id="usuario" value="<?= htmlspecialchars($usuarioData['usuario']) ?>" required>
        </div>

        <div class="form-group">
            <label for="rol">Rol:</label>
            <select name="rol" id="rol" required>
                <option value="usuario" <?= $usuarioData['rol'] === 'usuario' ? 'selected' : '' ?>>Usuario</option>
                <?php if ($_SESSION['rol'] === 'root'): ?>
                    <option value="admin" <?= $usuarioData['rol'] === 'admin' ? 'selected' : '' ?>>Administrador</option>
                    <option value="root" <?= $usuarioData['rol'] === 'root' ? 'selected' : '' ?>>Root</option>
                <?php endif; ?>
            </select>
        </div>

        <button type="submit">Guardar cambios</button>
    </form>
</div>
