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

// Obtener el ID del registro de forma segura
$record_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if ($record_id === null || $record_id === false) {
    header("Location: view_category.php?error=" . urlencode("ID de registro inválido o no especificado."));
    exit();
}

// Obtener el registro a editar
$stmt = $mysqli->prepare("SELECT marca, modelo, serie, caracteristicas, otorgado_a, categoria_id FROM registros WHERE id = ?");
if (!$stmt) {
    die("Error en la preparación de la consulta: " . $mysqli->error);
}
$stmt->bind_param('i', $record_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: view_category.php?error=" . urlencode("Registro no encontrado."));
    exit();
}

$record = $result->fetch_assoc();
$stmt->close();

// Manejar el envío del formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitizar y validar la entrada
    $marca = filter_input(INPUT_POST, 'marca', FILTER_SANITIZE_STRING);
    $modelo = filter_input(INPUT_POST, 'modelo', FILTER_SANITIZE_STRING);
    $serie = filter_input(INPUT_POST, 'serie', FILTER_SANITIZE_STRING);
    $caracteristicas = filter_input(INPUT_POST, 'caracteristicas', FILTER_SANITIZE_STRING);
    $otorgado_a = filter_input(INPUT_POST, 'otorgado_a', FILTER_SANITIZE_STRING);
    $categoria_id = filter_input(INPUT_POST, 'categoria_id', FILTER_VALIDATE_INT);

    // Validar campos requeridos
    if (!$marca || !$modelo || !$serie || !$caracteristicas || !$otorgado_a || $categoria_id === false) {
        $error = "Todos los campos son obligatorios.";
    } else {
        // Preparar la actualización
        $stmt = $mysqli->prepare("UPDATE registros SET marca = ?, modelo = ?, serie = ?, caracteristicas = ?, otorgado_a = ?, categoria_id = ? WHERE id = ?");
        if (!$stmt) {
            die("Error en la preparación de la consulta: " . $mysqli->error);
        }
        $stmt->bind_param('ssssssi', $marca, $modelo, $serie, $caracteristicas, $otorgado_a, $categoria_id, $record_id);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            header("Location: view_category.php?id=" . urlencode($categoria_id) . "&message=" . urlencode("Registro actualizado exitosamente."));
            exit();
        } else {
            $error = "No se realizaron cambios o el registro no se encontró.";
        }
        $stmt->close();
    }
}

$mysqli->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Registro</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        /* Agregar estilos básicos para una mejor visualización */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 80%;
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            color: #333;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        input[type="text"], input[type="hidden"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        button {
            padding: 10px 20px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        button:hover {
            background-color: #0056b3;
        }
        .error {
            color: red;
            margin-top: 10px;
        }
        .back-btn {
            display: inline-block;
            margin-bottom: 20px;
            font-size: 16px;
            color: #007bff;
            text-decoration: none;
        }
        .back-btn:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="view_category.php?id=<?php echo htmlspecialchars($record['categoria_id'], ENT_QUOTES, 'UTF-8'); ?>" class="back-btn">← Volver</a>
        <h1>Editar Registro</h1>
        <form method="POST" action="">
            <div class="form-group">
                <label for="marca">Marca</label>
                <input type="text" id="marca" name="marca" value="<?php echo htmlspecialchars($record['marca'], ENT_QUOTES, 'UTF-8'); ?>" required>
            </div>
            <div class="form-group">
                <label for="modelo">Modelo</label>
                <input type="text" id="modelo" name="modelo" value="<?php echo htmlspecialchars($record['modelo'], ENT_QUOTES, 'UTF-8'); ?>" required>
            </div>
            <div class="form-group">
                <label for="serie">Serie</label>
                <input type="text" id="serie" name="serie" value="<?php echo htmlspecialchars($record['serie'], ENT_QUOTES, 'UTF-8'); ?>" required>
            </div>
            <div class="form-group">
                <label for="caracteristicas">Características</label>
                <input type="text" id="caracteristicas" name="caracteristicas" value="<?php echo htmlspecialchars($record['caracteristicas'], ENT_QUOTES, 'UTF-8'); ?>" required>
            </div>
            <div class="form-group">
                <label for="otorgado_a">¿A quién fue otorgado?</label>
                <input type="text" id="otorgado_a" name="otorgado_a" value="<?php echo htmlspecialchars($record['otorgado_a'], ENT_QUOTES, 'UTF-8'); ?>" required>
            </div>
            <input type="hidden" name="categoria_id" value="<?php echo htmlspecialchars($record['categoria_id'], ENT_QUOTES, 'UTF-8'); ?>">
            <button type="submit" class="btn">Actualizar Registro</button>
            <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
        </form>
    </div>
</body>
</html>
