<?php
// Iniciar sesión para asegurar que se puede manipular la sesión
session_start();

// Eliminar todas las variables de sesión
session_unset();

// Destruir la sesión
session_destroy();

// Redirigir al usuario a la página de inicio de sesión
// Se recomienda usar una URL relativa para evitar problemas con diferentes configuraciones de servidor
header("Location: login.php");

// Asegurarse de que no se ejecute ningún código adicional
exit();
?>
