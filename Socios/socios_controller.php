<?php
require_once '../conexion.php';
require_once 'modelo_socios.php';

// Es buena práctica tener la conexión en un solo lugar.
$conexion = conectar();

// La acción viene por el método GET del formulario de eliminación.
$accion = $_GET['accion'] ?? null;

switch ($accion) {
    case 'eliminar':
        // Validamos que el ID sea un entero válido.
        $id_persona = filter_input(INPUT_GET, 'id_persona', FILTER_VALIDATE_INT);
        
        if ($id_persona) {
            // Le pedimos al modelo que se encargue de la eliminación.
            eliminarSocio($conexion, $id_persona);
        }
        break;
    
    default:
        // Si la acción no es reconocida, no hacemos nada.
        break;
}

// Cerramos la conexión.
mysqli_close($conexion);

// Redirigimos siempre de vuelta a la lista de socios.
header('Location: socios.php');
exit; // Detenemos el script después de redirigir.
?>