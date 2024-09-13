<?php
// Conexión a la base de datos
$host = 'localhost'; // Cambia esto si tu servidor MySQL no está en localhost
$db = 'nombre_base_datos'; // Reemplaza con el nombre de tu base de datos
$user = 'nuevo_usuario'; // Reemplaza con el nuevo nombre de usuario
$pass = 'nueva_contraseña'; // Reemplaza con la nueva contraseña

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die('Error de conexión: ' . $e->getMessage());
}

// Agregar la columna last_update_date a la tabla inventory
try {
    $sql = "ALTER TABLE inventory ADD COLUMN last_update_date DATE";
    $pdo->exec($sql);
    echo "Columna 'last_update_date' añadida correctamente a la tabla 'inventory'.";
} catch (PDOException $e) {
    die('Error al agregar columna: ' . $e->getMessage());
}
?>
