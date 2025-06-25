<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title><?= htmlspecialchars($titulo ?? 'Panel') ?></title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
  <link rel="stylesheet" href="../css/estilo.css" />
</head>
<body>
  <div class="div1">
    <h1>JCL</h1>
    <nav>
      <ul>
        <li><a href="../admin/dashboard.php"><i class="fas fa-home"></i> Inicio</a></li>
        <?php if (function_exists('es_admin') && function_exists('es_root')): ?>
          <?php if (es_admin() || es_root()): ?>
            <li><a href="../admin/subir.php"><i class="fas fa-upload"></i> Subir instalador</a></li>
            <li><a href="../controllers/usuario.php"><i class="fas fa-users-cog"></i> Administrar usuarios</a></li>
          <?php endif; ?>
        <?php endif; ?>
        <li><a href="/instaladores/logout.php"><i class="fas fa-sign-out-alt"></i> Cerrar sesión</a></li>

        <li><a aria-hidden="true"><i class="fa fa-user-circle-o"></i> Usuario: <?= htmlspecialchars($_SESSION['usuario'] ?? '') ?></a></li>
        <li><a><i class="fas fa-user-tag"></i> Rol: <?= htmlspecialchars($_SESSION['rol'] ?? '') ?></a></li>
      </ul>
    </nav>
  </div>
