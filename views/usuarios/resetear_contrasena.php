<?php include '../estructura/header.php'; ?>

<h2>Resetear contraseña para <?= htmlspecialchars($usuarioData['usuario']) ?></h2>

<?php if (!empty($error)): ?>
    <div class="error"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<form method="POST" onsubmit="return confirm('¿Deseas cambiar la contraseña?')">
    <label for="nueva_clave">Nueva contraseña:</label>
    <input type="password" name="nueva_clave" id="nueva_clave" required minlength="6"><br>
    <button type="submit">Guardar</button>
</form>
