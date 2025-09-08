<?php
// Siempre iniciar la sesión, incluso para destruirla.
session_start();

// 1. Desvincular todas las variables de sesión.
$_SESSION = array();

// 2. Destruir la sesión.
// Esto eliminará la cookie de sesión del lado del servidor.
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

session_destroy();

// 3. Redirigir al usuario a la página de login.
header("Location: login.php");
exit();
?>
