<?php
// Incluir archivo de configuración
require_once __DIR__ . '/config.php'; // Usa una ruta absoluta para evitar problemas de ubicación

// Crear conexión
$conn = new mysqli(DB_SERVER, DB_USER, DB_PASS, DB_NAME);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Generar un hash de la contraseña
$password = 'miContraseñaSegura'; // Contraseña de ejemplo
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Preparar la consulta para evitar inyección SQL
$stmt = $conn->prepare("INSERT INTO usuarios (username, password) VALUES (?, ?)");
if ($stmt === false) {
    die("Error en la preparación de la consulta: " . $conn->error);
}

// Vincular parámetros
$username = 'usuarioEjemplo';
$stmt->bind_param('ss', $username, $hashed_password);

// Ejecutar la consulta
if ($stmt->execute()) {
    echo "Usuario de ejemplo insertado correctamente.";
    // Puedes redirigir o mostrar un mensaje más estilizado en lugar de imprimir directamente
} else {
    echo "Error al insertar usuario: " . $stmt->error;
}

// Cerrar declaración y conexión
$stmt->close();
$conn->close();
?>
