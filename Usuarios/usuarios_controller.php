<?php
// Controlador para gestionar usuarios (CRUD y recuperación)
require_once '../conexion.php';
require_once 'modelo_usuarios.php';

$accion = $_GET['accion'] ?? null;

if ($accion === 'crear' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $conexion = conectar();
    $id_persona = isset($_POST['id_persona']) ? (int)$_POST['id_persona'] : 0;
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $rol = $_POST['rol'] ?? 'usuario';
    $exito = crearUsuario($conexion, $id_persona, $email, $password, $rol);
    mysqli_close($conexion);
    header('Location: /spartanproject/admin_dashboard.php');
    exit;
}

$conexion = conectar();
$usuarios = obtenerUsuarios($conexion);
$personas = obtenerPersonas($conexion);
mysqli_close($conexion);
include 'usuarios_view.php';
exit;
