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

// Validar y sanitizar el ID de categoría
$category_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if ($category_id === false || $category_id === null) {
    $conn->close();
    header("Location: view_categories.php?error=" . urlencode("ID de categoría no válido"));
    exit();
}

// Obtener detalles de la categoría actual
$stmt = $conn->prepare("SELECT name FROM categories WHERE id = ?");
if (!$stmt) {
    $conn->close();
    die("Error en la preparación de la consulta: " . $conn->error);
}
$stmt->bind_param("i", $category_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 1) {
    $category = $result->fetch_assoc();
    $category_name = htmlspecialchars($category['name']); // Sanitizar para la salida HTML
} else {
    $error = "Categoría no encontrada.";
}
$stmt->close();

// Procesar el formulario cuando se envía
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_category_name = trim($_POST['category_name']);

    if (empty($new_category_name)) {
        $error = "El nombre de la categoría no puede estar vacío.";
    } else {
        // Consultas preparadas para actualizar el nombre de la categoría
        $stmt = $conn->prepare("UPDATE categories SET name = ? WHERE id = ?");
        if (!$stmt) {
            $conn->close();
            die("Error en la preparación de la consulta: " . $conn->error);
        }
        $stmt->bind_param("si", $new_category_name, $category_id);

        if ($stmt->execute()) {
            $message = "Categoría actualizada exitosamente.";
            header("Location: view_categories.php?message=" . urlencode($message));
            exit();
        } else {
            $error = "Error al actualizar la categoría. Intenta de nuevo.";
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
    <title>Editar Categoría</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        /* Agregar estilos básicos para una mejor visualización */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .container {
            width: 80%;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h2 {
            color: #333;
        }
        .success {
            color: green;
        }
        .error {
            color: red;
        }
        form {
            display: flex;
            flex-direction: column;
        }
        label {
            margin-bottom: 0.5rem;
            font-weight: bold;
        }
        input[type="text"] {
            padding: 0.5rem;
            margin-bottom: 1rem;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        button {
            padding: 0.7rem;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        button:hover {
            background-color: #0056b3;
        }
        a {
            display: inline-block;
            margin-top: 1rem;
            color: #007bff;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Editar Categoría</h2>

        <?php if ($message): ?>
            <p class="success"><?php echo $message; ?></p>
        <?php endif; ?>

        <?php if ($error): ?>
            <p class="error"><?php echo $error; ?></p>
        <?php endif; ?>

        <form method="post" action="">
            <label for="category_name">Nuevo Nombre de la Categoría:</label>
            <input type="text" id="category_name" name="category_name" value="<?php echo $category_name; ?>" required>
            <button type="submit">Actualizar Categoría</button>
        </form>

        <a href="view_categories.php">Volver a Categorías</a>
    </div>
</body>
</html>
