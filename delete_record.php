<?php
// Mostrar errores y advertencias para depuración (desactivar en producción)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Iniciar sesión
session_start();

// Verificar si el usuario está autenticado
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Configuración de conexión a la base de datos
$servername = "localhost";
$username = "root"; // Cambia según tu configuración
$password = ""; // Cambia según tu configuración
$dbname = "usuarios_db"; // Cambia según el nombre de tu base de datos

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Inicializar variables para mensajes
$message = "";
$error = "";

// Validar y sanitizar el ID del registro
$record_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if ($record_id === false || $record_id === null) {
    $conn->close();
    header("Location: view_categories.php?error=" . urlencode("ID de registro no válido"));
    exit();
}

// Obtener detalles del registro actual
$stmt = $conn->prepare("SELECT data FROM records WHERE id = ?");
if ($stmt === false) {
    $conn->close();
    die("Error en la preparación de la consulta: " . $conn->error);
}
$stmt->bind_param("i", $record_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 1) {
    $record = $result->fetch_assoc();
    $record_data = htmlspecialchars($record['data']); // Sanitizar para la salida HTML
} else {
    $error = "Registro no encontrado.";
}

$stmt->close();

// Procesar el formulario cuando se envía
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_record_data = trim($_POST['record_data']);

    if (empty($new_record_data)) {
        $error = "El campo de registro no puede estar vacío.";
    } else {
        // Consultas preparadas para actualizar el registro
        $stmt = $conn->prepare("UPDATE records SET data = ? WHERE id = ?");
        if ($stmt === false) {
            $conn->close();
            die("Error en la preparación de la consulta: " . $conn->error);
        }
        $stmt->bind_param("si", $new_record_data, $record_id);

        if ($stmt->execute()) {
            $message = "Registro actualizado exitosamente.";
            header("Location: view_categories.php?message=" . urlencode($message));
            exit();
        } else {
            $error = "Error al actualizar el registro. Intenta de nuevo.";
        }

        $stmt->close();
    }
}

// Cerrar la conexión de base de datos
$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Registro</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h2>Editar Registro</h2>

    <?php if (!empty($message)): ?>
        <p class="success"><?php echo htmlspecialchars($message); ?></p>
    <?php endif; ?>

    <?php if (!empty($error)): ?>
        <p class="error"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>

    <form method="post" action="">
        <label for="record_data">Nuevo Contenido del Registro:</label>
        <textarea id="record_data" name="record_data" required><?php echo htmlspecialchars($record_data); ?></textarea>
        <button type="submit">Actualizar Registro</button>
    </form>

    <a href="view_categories.php">Volver a Categorías</a>
</body>
</html>
