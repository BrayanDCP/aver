<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$recordId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if ($recordId === false || $recordId === null) {
    die("ID de registro no válido.");
}

$servername = "localhost";
$username = "root";
$password = "";
$database = "mi_base_de_datos";

$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Obtener detalles del registro
$stmt = $conn->prepare("SELECT * FROM registros WHERE id = ?");
if (!$stmt) {
    die("Error en la preparación de la consulta: " . $conn->error);
}
$stmt->bind_param("i", $recordId);
$stmt->execute();
$record = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Obtener el nombre de la categoría
$categoryName = 'No disponible';
if ($record) {
    $categoryId = $record['categoria_id'];
    $categoryStmt = $conn->prepare("SELECT nombre_categoria FROM categorias WHERE id = ?");
    if (!$categoryStmt) {
        die("Error en la preparación de la consulta: " . $conn->error);
    }
    $categoryStmt->bind_param("i", $categoryId);
    $categoryStmt->execute();
    $categoryResult = $categoryStmt->get_result();
    if ($categoryRow = $categoryResult->fetch_assoc()) {
        $categoryName = htmlspecialchars($categoryRow['nombre_categoria'], ENT_QUOTES, 'UTF-8');
    }
    $categoryStmt->close();
}

$conn->close();

if (!$record) {
    die("Registro no encontrado.");
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalles del Registro</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 900px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .back-btn {
            display: inline-block;
            margin-bottom: 20px;
            font-size: 16px;
            color: #007bff;
            text-decoration: none;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #007bff;
            transition: background-color 0.3s, color 0.3s;
        }
        .back-btn:hover {
            background-color: #007bff;
            color: #fff;
        }
        h1 {
            margin-top: 0;
            color: #333;
            font-size: 24px;
        }
        .detail-table {
            width: 100%;
            border-collapse: collapse;
        }
        .detail-table th, .detail-table td {
            padding: 12px;
            text-align: left;
            border: 1px solid #ddd;
        }
        .detail-table th {
            background-color: #007bff;
            color: #fff;
            font-size: 16px;
        }
        .detail-table td {
            background-color: #f9f9f9;
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="view_records.php" class="back-btn">Volver a la Lista de Registros</a>
        <h1>Detalles del Registro</h1>
        <table class="detail-table">
            <tr>
                <th>ID</th>
                <td><?php echo htmlspecialchars($record['id'], ENT_QUOTES, 'UTF-8'); ?></td>
            </tr>
            <tr>
                <th>Marca</th>
                <td><?php echo htmlspecialchars($record['marca'], ENT_QUOTES, 'UTF-8'); ?></td>
            </tr>
            <tr>
                <th>Modelo</th>
                <td><?php echo htmlspecialchars($record['modelo'], ENT_QUOTES, 'UTF-8'); ?></td>
            </tr>
            <tr>
                <th>Serie</th>
                <td><?php echo htmlspecialchars($record['serie'], ENT_QUOTES, 'UTF-8'); ?></td>
            </tr>
            <tr>
                <th>Características</th>
                <td><?php echo htmlspecialchars($record['caracteristicas'], ENT_QUOTES, 'UTF-8'); ?></td>
            </tr>
            <tr>
                <th>Otorgado a</th>
                <td><?php echo htmlspecialchars($record['otorgado_a'], ENT_QUOTES, 'UTF-8'); ?></td>
            </tr>
            <tr>
                <th>Categoría</th>
                <td><?php echo htmlspecialchars($categoryName, ENT_QUOTES, 'UTF-8'); ?></td>
            </tr>
        </table>
    </div>
</body>
</html>
