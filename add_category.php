<?php
// Mostrar errores y advertencias para depuración (desactivar en producción)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

// Configuración de conexión a la base de datos
$servername = "localhost";
$username = "root"; // Cambia esto si tu configuración es diferente
$password = ""; // Cambia esto si tu configuración es diferente
$dbname = "usuarios_db"; // Nombre de tu base de datos

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Crear tabla 'categories' si no existe
$sql_create_table = "CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if ($conn->query($sql_create_table) === FALSE) {
    die("Error al crear la tabla 'categories': " . $conn->error);
}

$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validar y sanitizar la entrada del usuario
    $category_name = filter_input(INPUT_POST, 'category_name', FILTER_SANITIZE_STRING);

    if (!empty($category_name)) {
        // Consulta SQL para insertar una nueva categoría
        $sql = "INSERT INTO categories (name) VALUES (?)";

        // Preparar consulta
        $stmt = $conn->prepare($sql);

        // Verificar si la consulta preparada fue exitosa
        if ($stmt === false) {
            die("Error en la consulta SQL: " . $conn->error);
        }

        // Vincular parámetros
        $stmt->bind_param("s", $category_name);

        // Ejecutar consulta
        if ($stmt->execute()) {
            $success = "Categoría añadida exitosamente.";
        } else {
            $error = "Error al añadir la categoría: " . $stmt->error;
        }

        // Cerrar la declaración
        $stmt->close();
    } else {
        $error = "Por favor, introduce el nombre de la categoría.";
    }
}

// Cerrar conexión a la base de datos
$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Añadir Categoría</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
        }
        h2 {
            color: #333;
        }
        .error {
            color: red;
        }
        .success {
            color: green;
        }
        form {
            margin-top: 20px;
        }
        label {
            display: block;
            margin-bottom: 10px;
        }
        input[type="text"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        button {
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            background-color: #007bff;
            color: white;
            font-size: 16px;
            cursor: pointer;
        }
        button:hover {
            background-color: #0056b3;
        }
        a {
            display: inline-block;
            margin-top: 20px;
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
        <h2>Añadir Nueva Categoría</h2>

        <?php if (!empty($error)): ?>
            <p class="error"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></p>
        <?php endif; ?>

        <?php if (!empty($success)): ?>
            <p class="success"><?php echo htmlspecialchars($success, ENT_QUOTES, 'UTF-8'); ?></p>
        <?php endif; ?>

        <form method="post" action="">
            <label for="category_name">Nombre de la Categoría:</label>
            <input type="text" id="category_name" name="category_name" required>

            <button type="submit">Añadir Categoría</button>
        </form>

        <a href="dashboard.php">Volver al Panel de Control</a>
    </div>
</body>
</html>
