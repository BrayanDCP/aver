<?php
// Configuración de la base de datos
$host = 'localhost';
$dbname = 'mi_base_de_datos';
$username = 'root';
$password = '';

// Conectar a la base de datos
$mysqli = new mysqli($host, $username, $password, $dbname);

// Verificar la conexión
if ($mysqli->connect_error) {
    die("Conexión fallida: " . $mysqli->connect_error);
}

// Obtener el ID de la categoría de forma segura
$categoria_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if ($categoria_id === null || $categoria_id === false) {
    $mysqli->close(); // Cierra la conexión antes de salir
    die("ID de categoría inválido o no especificado.");
}

// Eliminar todos los registros de la categoría
$stmt = $mysqli->prepare("DELETE FROM registros WHERE categoria_id = ?");
if ($stmt === false) {
    $mysqli->close(); // Cierra la conexión antes de salir
    die("Error en la preparación de la consulta: " . $mysqli->error);
}

$stmt->bind_param('i', $categoria_id);
$stmt->execute();

// Verificar si la eliminación fue exitosa
if ($stmt->affected_rows > 0) {
    $stmt->close(); // Cierra el statement
    $mysqli->close(); // Cierra la conexión
    header("Location: view_category.php?id=$categoria_id"); // Redirige a la página de la categoría
    exit(); // Termina la ejecución del script
} else {
    $stmt->close(); // Cierra el statement en caso de error
    $mysqli->close(); // Cierra la conexión en caso de error
    die("Error al eliminar los registros o no se encontraron registros.");
}
?>
