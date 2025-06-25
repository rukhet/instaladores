<?php
session_start();

include '../includes/auth.php';
include '../includes/db.php';
include '../models/usuarios.php';

if (!es_admin() && !es_root()) {
    exit('Acceso denegado');
}

$usuario_logueado = $_SESSION['usuario'];
$rolPermitido = es_root() ? ['usuario', 'admin', 'root'] : ['usuario'];

$accion = $_GET['accion'] ?? 'listar';

switch ($accion) {
    case 'agregar':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usuario = trim($_POST['usuario']);
            $clave = $_POST['clave'];
            $rol = $_POST['rol'];

            if (!in_array($rol, $rolPermitido)) {
                exit('No tienes permiso para asignar ese rol.');
            }

            if (Usuario::insertar($conn, $usuario, $clave, $rol)) {
                header('Location: usuario.php');
                exit;
            } else {
                exit('Error al agregar usuario.');
            }
        }
        break;

    case 'editar':
        $id = intval($_GET['id'] ?? 0);
        if (!$id) exit('ID inválido.');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usuario = trim($_POST['usuario']);
            $rol = $_POST['rol'];

            if (!in_array($rol, $rolPermitido)) {
                exit('No tienes permiso para asignar ese rol.');
            }

            if (Usuario::actualizar($conn, $id, $usuario, $rol)) {
                header('Location: usuario.php');
                exit;
            } else {
                exit('Error al actualizar usuario.');
            }
        } else {
            // Mostrar formulario editar
            $usuarioData = Usuario::obtenerPorId($conn, $id);
            if (!$usuarioData) exit('Usuario no encontrado.');

            include '../views/usuarios/editar_usuario.php';
            exit;
        }
        break;

    case 'eliminar':
        $id = intval($_GET['id'] ?? 0);
        if (!$id) exit('ID inválido.');

        $usuarioData = Usuario::obtenerPorId($conn, $id);
        if (!$usuarioData) exit('Usuario no encontrado.');

        if ($usuarioData['usuario'] === $usuario_logueado) {
            exit('No puedes eliminar tu propio usuario.');
        }

        if ($usuarioData['rol'] === 'root' && !es_root()) {
            exit('No tienes permiso para eliminar usuario root.');
        }

        if (Usuario::eliminar($conn, $id)) {
            header('Location: usuario.php');
            exit;
        } else {
            exit('Error al eliminar usuario.');
        }
        break;

    case 'resetear':
    $id = intval($_GET['id'] ?? 0);
    if (!$id) exit('ID inválido.');

    $usuarioData = Usuario::obtenerPorId($conn, $id);
    if (!$usuarioData) exit('Usuario no encontrado.');

    // Validaciones de acceso
    if (es_admin() && in_array($usuarioData['rol'], ['admin', 'root'])) {
        exit("No tienes permiso para cambiar la contraseña de este usuario.");
    }

    if ($_SESSION['usuario'] === $usuarioData['usuario']) {
        exit("No puedes resetear tu propia contraseña desde aquí.");
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $nuevaClave = trim($_POST['nueva_clave']);

        if (strlen($nuevaClave) < 6) {
            $error = "La contraseña debe tener al menos 6 caracteres.";
        } else {
            $claveHash = password_hash($nuevaClave, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE usuarios SET clave = ? WHERE id = ?");
            $stmt->bind_param("si", $claveHash, $id);
            if ($stmt->execute()) {
                header("Location: usuario.php?accion=listar&mensaje=reset_ok");
                exit;
            } else {
                $error = "Error al actualizar contraseña.";
            }
        }
    }

    include '../views/usuarios/resetear_contrasena.php';
    exit;

case 'listar':
default:
    try {
        $usuarios = Usuario::obtenerTodos($conn, es_root());
    } catch (Exception $e) {
        die("Error al obtener usuarios: " . $e->getMessage());
    }
    include '../views/usuarios/usuarios.php';
    break;
}
