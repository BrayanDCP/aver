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
    die("ID de categoría inválido o no especificado.");
}

// Comprobar si se solicita eliminar la categoría
if (isset($_GET['delete_category']) && $_GET['delete_category'] == 'true') {
    // Eliminar todos los registros asociados a la categoría
    $stmt = $mysqli->prepare("DELETE FROM registros WHERE categoria_id = ?");
    if (!$stmt) {
        die("Error en la preparación de la consulta: " . $mysqli->error);
    }
    $stmt->bind_param('i', $categoria_id);
    $stmt->execute();
    $stmt->close();

    // Eliminar la categoría
    $stmt = $mysqli->prepare("DELETE FROM categorias WHERE id = ?");
    if (!$stmt) {
        die("Error en la preparación de la consulta: " . $mysqli->error);
    }
    $stmt->bind_param('i', $categoria_id);
    $stmt->execute();
    $stmt->close();

    // Redirigir a la página principal después de eliminar
    header("Location: index.php");
    exit;
}

// Obtener la categoría seleccionada de forma segura
$stmt = $mysqli->prepare("SELECT nombre_categoria FROM categorias WHERE id = ?");
if (!$stmt) {
    die("Error en la preparación de la consulta: " . $mysqli->error);
}
$stmt->bind_param('i', $categoria_id);
$stmt->execute();
$result = $stmt->get_result();
$category = $result->fetch_assoc();
$stmt->close();

// Comprobar si la categoría existe
if (!$category) {
    die("Categoría no encontrada.");
}

// Obtener los registros de la categoría
$records = [];
$stmt = $mysqli->prepare("SELECT id, marca, modelo, serie, caracteristicas, otorgado_a FROM registros WHERE categoria_id = ?");
if (!$stmt) {
    die("Error en la preparación de la consulta: " . $mysqli->error);
}
$stmt->bind_param('i', $categoria_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $records[] = $row;
}
$stmt->close();

$mysqli->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registros en Categoría</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
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
        h1 {
            color: #333;
        }
        h2 {
            color: #666;
        }
        a {
            text-decoration: none;
            color: #007bff;
            font-weight: bold;
        }
        a:hover {
            text-decoration: underline;
        }
        .btn {
            display: inline-block;
            padding: 10px 15px;
            color: white;
            background-color: #007bff;
            border-radius: 5px;
            font-size: 16px;
            margin-bottom: 20px;
        }
        .btn:hover {
            background-color: #0056b3;
        }
        ul {
            list-style: none;
            padding: 0;
        }
        li {
            padding: 10px;
            border-bottom: 1px solid #ddd;
        }
        li:last-child {
            border-bottom: none;
        }
        .actions a {
            display: inline-block;
            padding: 5px 10px;
            margin-right: 5px;
            color: white;
            border-radius: 4px;
            text-decoration: none;
            font-size: 14px;
        }
        .edit {
            background-color: #28a745;
        }
        .delete {
            background-color: #dc3545;
        }
        .edit:hover {
            background-color: #218838;
        }
        .delete:hover {
            background-color: #c82333;
        }
        .back-btn {
            display: inline-block;
            padding: 10px;
            background-color: #6c757d;
            color: white;
            border-radius: 5px;
            text-decoration: none;
            margin-bottom: 20px;
        }
        .back-btn:hover {
            background-color: #5a6268;
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="index.php" class="back-btn">← Volver</a>
        <h1><?php echo htmlspecialchars($category['nombre_categoria'], ENT_QUOTES, 'UTF-8'); ?></h1>
        <h2>Registros</h2>
        <a href="delete_all_records.php?id=<?php echo $categoria_id; ?>" onclick="return confirm('¿Estás seguro de que quieres eliminar todos los registros de esta categoría?');" class="btn">Eliminar Todos los Registros</a>
        <a href="?id=<?php echo $categoria_id; ?>&delete_category=true" onclick="return confirm('¿Estás seguro de que quieres eliminar esta categoría y todos sus registros?');" class="btn">Eliminar Categoría</a>
        <ul>
            <?php if (!empty($records)): ?>
                <?php foreach ($records as $record): ?>
                    <li>
                        <strong>Marca:</strong> <?php echo htmlspecialchars($record['marca'], ENT_QUOTES, 'UTF-8'); ?><br>
                        <strong>Modelo:</strong> <?php echo htmlspecialchars($record['modelo'], ENT_QUOTES, 'UTF-8'); ?><br>
                        <strong>Serie:</strong> <?php echo htmlspecialchars($record['serie'], ENT_QUOTES, 'UTF-8'); ?><br>
                        <strong>Características:</strong> <?php echo htmlspecialchars($record['caracteristicas'], ENT_QUOTES, 'UTF-8'); ?><br>
                        <strong>Otorgado a:</strong> <?php echo htmlspecialchars($record['otorgado_a'], ENT_QUOTES, 'UTF-8'); ?><br>
                        <div class="actions">
                            <a href="edit_record.php?id=<?php echo $record['id']; ?>" class="edit">Editar</a>
                            <a href="delete_record.php?id=<?php echo $record['id']; ?>" onclick="return confirm('¿Estás seguro de que quieres eliminar este registro?');" class="delete">Eliminar</a>
                        </div>
                    </li>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No hay registros en esta categoría.</p>
            <?php endif; ?>
        </ul>
    </div>
</body>
</html>
