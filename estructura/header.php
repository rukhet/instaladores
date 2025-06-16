<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $titulo ?? 'Panel' ?></title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<link rel="stylesheet" href="../css/estilo.css">

</head>
<body>
  <div class="div1">
    <h1>JCL</h1>
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
