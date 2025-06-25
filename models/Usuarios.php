<?php
class Usuario {
public static function obtenerTodos($conn, $esRoot) {
    $sql = $esRoot
        ? "SELECT id, usuario, rol FROM usuarios"
        : "SELECT id, usuario, rol FROM usuarios WHERE rol != 'root'";

    $resultado = $conn->query($sql);

    if ($resultado === false) {
        throw new Exception("Error en la consulta SQL: " . $conn->error);
    }

    return $resultado;
}
    public static function insertar($conn, $usuario, $clave, $rol) {
        $claveHash = password_hash($clave, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO usuarios (usuario, clave, rol) VALUES (?, ?, ?)");
        if (!$stmt) {
            throw new Exception("Error en la preparación de la consulta: " . $conn->error);
        }
        $stmt->bind_param("sss", $usuario, $claveHash, $rol);
        return $stmt->execute();
    }

    public static function obtenerPorId($conn, $id) {
        $stmt = $conn->prepare("SELECT * FROM usuarios WHERE id = ?");
        if (!$stmt) {
            throw new Exception("Error en la preparación de la consulta: " . $conn->error);
        }
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public static function actualizar($conn, $id, $usuario, $rol) {
        $stmt = $conn->prepare("UPDATE usuarios SET usuario = ?, rol = ? WHERE id = ?");
        if (!$stmt) {
            throw new Exception("Error en la preparación de la consulta: " . $conn->error);
        }
        $stmt->bind_param("ssi", $usuario, $rol, $id);
        return $stmt->execute();
    }

    public static function eliminar($conn, $id) {
        $stmt = $conn->prepare("DELETE FROM usuarios WHERE id = ?");
        if (!$stmt) {
            throw new Exception("Error en la preparación de la consulta: " . $conn->error);
        }
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}
