<?php
// Define una contraseña segura (ejemplo)
$password = 'miContraseñaSegura';

// Verifica si la contraseña no está vacía
if (empty($password)) {
    die('Error: La contraseña no puede estar vacía.');
}

// Genera el hash de la contraseña
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

// Verifica si la función password_hash ha devuelto un resultado válido
if ($hashedPassword === false) {
    die('Error al generar el hash de la contraseña.');
}

// Muestra el hash generado
echo "Hash de la contraseña: " . htmlspecialchars($hashedPassword, ENT_QUOTES, 'UTF-8');
?>
