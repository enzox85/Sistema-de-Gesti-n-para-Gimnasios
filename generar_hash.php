<?php

// Elige una contraseña segura para tu administrador
$contrasenaPlana = 'admin12345';

// Genera el hash seguro
$hash = password_hash($contrasenaPlana, PASSWORD_DEFAULT);

// Muestra el hash
echo "Tu contraseña hasheada es: <br>";
echo $hash;

?>