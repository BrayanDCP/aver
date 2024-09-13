<?php
// Configuración de la base de datos
$host = 'localhost'; // Cambia esto si tu servidor es diferente
$dbname = 'mi_base_de_datos'; // Nombre de tu base de datos
$username = 'root'; // Cambia esto si tu usuario es diferente
$password = ''; // Cambia esto si tu contraseña es diferente

// Conectar a la base de datos
$mysqli = new mysqli($host, $username, $password, $dbname);

// Verificar la conexión
if ($mysqli->connect_error) {
    die("Conexión fallida: " . $mysqli->connect_error);
}

// Obtener todas las categorías usando una consulta preparada
$sql = "SELECT id, nombre_categoria FROM categorias";
$categories = [];

if ($stmt = $mysqli->prepare($sql)) {
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $categories[] = $row;
    }

    $stmt->close();
} else {
    echo "Error al preparar la consulta: " . $mysqli->error;
}

$mysqli->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Categorías Disponibles</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .back-btn {
            display: inline-block;
            margin-bottom: 20px;
            text-decoration: none;
            color: #3498db;
            font-size: 16px;
        }
        .back-btn:hover {
            text-decoration: underline;
        }
        .category-button {
            margin-bottom: 10px;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #3498db;
            color: #fff;
            text-decoration: none;
            border-radius: 4px;
            font-size: 16px;
        }
        .btn:hover {
            background-color: #2980b9;
        }
        h1 {
            color: #2c3e50;
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="index.php" class="back-btn">← Volver</a>
        <h1>Categorías Disponibles</h1>
        
        <?php if (!empty($categories)): ?>
            <?php foreach ($categories as $category): ?>
                <div class="category-button">
                    <a href="view_category.php?id=<?php echo htmlspecialchars($category['id'], ENT_QUOTES, 'UTF-8'); ?>" class="btn">
                        <?php echo htmlspecialchars($category['nombre_categoria'], ENT_QUOTES, 'UTF-8'); ?>
                    </a>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No hay categorías disponibles.</p>
        <?php endif; ?>
    </div>
</body>
</html>
