<?php
function obtenerPersonas(mysqli $conexion): array {
    $sql = "SELECT id_persona, nombre, apellido, dni FROM personas ORDER BY apellido, nombre";
    $resultado = mysqli_query($conexion, $sql);
    return $resultado ? mysqli_fetch_all($resultado, MYSQLI_ASSOC) : [];
}

// Modelo para usuarios
function obtenerUsuarios(mysqli $conexion): array {
    $usuarios = [];
    $sql = "SELECT u.id_usuario, p.nombre, p.apellido, u.email, u.rol FROM usuarios u JOIN personas p ON u.id_persona = p.id_persona ORDER BY p.apellido, p.nombre";
    $resultado = mysqli_query($conexion, $sql);
    if ($resultado) {
        $usuarios = mysqli_fetch_all($resultado, MYSQLI_ASSOC);
    }
    return $usuarios;
}

function crearUsuario(mysqli $conexion, int $id_persona, string $email, string $password, string $rol): bool {
    $password_hash = password_hash($password, PASSWORD_DEFAULT);
    $sql = "INSERT INTO usuarios (id_persona, email, password, rol) VALUES (?, ?, ?, ?)";
    $stmt = mysqli_prepare($conexion, $sql);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "isss", $id_persona, $email, $password_hash, $rol);
        $exito = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        return $exito;
    }
    return false;
}
