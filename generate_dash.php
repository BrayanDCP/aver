<?php
// Función para generar un hash de una contraseña
function generatePasswordHash($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

// Contraseña a hashear (se puede adaptar para que se ingrese a través de un formulario)
$password = 'miContraseñaSegura';

// Generar el hash de la contraseña
$hash = generatePasswordHash($password);

// Verificar si se ha generado el hash correctamente
if ($hash === false) {
    echo "Error al generar el hash de la contraseña.";
} else {
    // Mostrar el hash de manera segura en HTML
    echo "Hash generado: <br><code>" . htmlspecialchars($hash, ENT_QUOTES, 'UTF-8') . "</code>";
}
?>
