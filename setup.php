<?php
// Conexión a la base de datos
$host = 'localhost'; 
$db = 'nombre_base_datos'; 
$user = 'nuevo_usuario'; 
$pass = 'nueva_contraseña'; 

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die('Error de conexión: ' . $e->getMessage());
}

// Crear tabla withdrawals si no existe
try {
    $pdo->exec("CREATE TABLE IF NOT EXISTS withdrawals (
        id INT AUTO_INCREMENT PRIMARY KEY,
        material VARCHAR(255) NOT NULL,
        quantity INT NOT NULL,
        withdrawal_date DATE NOT NULL
    )");
} catch (PDOException $e) {
    die('Error al crear la tabla withdrawals: ' . $e->getMessage());
}

// Crear tabla deposits si no existe
try {
    $pdo->exec("CREATE TABLE IF NOT EXISTS deposits (
        id INT AUTO_INCREMENT PRIMARY KEY,
        material VARCHAR(255) NOT NULL,
        quantity INT NOT NULL,
        deposit_date DATE NOT NULL
    )");
} catch (PDOException $e) {
    die('Error al crear la tabla deposits: ' . $e->getMessage());
}

echo "Tablas creadas o ya existentes.";
?>
