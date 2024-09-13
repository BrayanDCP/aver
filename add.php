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

// Inicializar variables
$message = '';
$error = '';

// Procesar el formulario cuando se envía
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['add_category'])) {
        // Agregar nueva categoría
        $category_name = trim($_POST['category_name']);
        if (!empty($category_name)) {
            $stmt = $mysqli->prepare("INSERT INTO categorias (nombre_categoria) VALUES (?)");
            if ($stmt === false) {
                die("Error en la preparación de la consulta: " . $mysqli->error);
            }
            $stmt->bind_param('s', $category_name);
            if ($stmt->execute()) {
                $message = "Categoría añadida con éxito.";
            } else {
                $error = "Error al añadir la categoría.";
            }
            $stmt->close();
        } else {
            $error = "El nombre de la categoría no puede estar vacío.";
        }
    } elseif (isset($_POST['add_record'])) {
        // Agregar nuevo registro
        $category_id = filter_input(INPUT_POST, 'category_id', FILTER_SANITIZE_NUMBER_INT);
        $marca = filter_input(INPUT_POST, 'marca', FILTER_SANITIZE_STRING);
        $modelo = filter_input(INPUT_POST, 'modelo', FILTER_SANITIZE_STRING);
        $serie = filter_input(INPUT_POST, 'serie', FILTER_SANITIZE_STRING);
        $caracteristicas = filter_input(INPUT_POST, 'caracteristicas', FILTER_SANITIZE_STRING);
        $otorgado_a = filter_input(INPUT_POST, 'otorgado_a', FILTER_SANITIZE_STRING);

        if ($category_id && $marca && $modelo && $serie && $otorgado_a) {
            $stmt = $mysqli->prepare("INSERT INTO registros (categoria_id, marca, modelo, serie, caracteristicas, otorgado_a) VALUES (?, ?, ?, ?, ?, ?)");
            if ($stmt === false) {
                die("Error en la preparación de la consulta: " . $mysqli->error);
            }
            $stmt->bind_param('isssss', $category_id, $marca, $modelo, $serie, $caracteristicas, $otorgado_a);
            if ($stmt->execute()) {
                $message = "Registro añadido con éxito.";
            } else {
                $error = "Error al añadir el registro.";
            }
            $stmt->close();
        } else {
            $error = "Por favor, completa todos los campos obligatorios.";
        }
    }
}

// Obtener categorías para el formulario
$categories = [];
$result = $mysqli->query("SELECT id, nombre_categoria FROM categorias");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $categories[] = $row;
    }
} else {
    die("Error al obtener categorías: " . $mysqli->error);
}

$mysqli->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Categoría y Registro</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f4f8;
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
        h1 {
            color: #2c3e50;
            text-align: center;
        }
        h2 {
            color: #2c3e50;
            border-bottom: 2px solid #3498db;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #34495e;
        }
        input[type="text"], textarea, select {
            width: 100%;
            padding: 10px;
            border: 1px solid #bdc3c7;
            border-radius: 4px;
            box-sizing: border-box;
            background-color: #ecf0f1;
            color: #2c3e50;
        }
        textarea {
            height: 100px;
            resize: vertical;
        }
        button {
            background-color: #3498db;
            color: #fff;
            border: none;
            padding: 10px 15px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            margin-top: 10px;
        }
        button:hover {
            background-color: #2980b9;
        }
        .error, .success {
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 15px;
            color: #fff;
            text-align: center;
        }
        .error {
            background-color: #e74c3c;
        }
        .success {
            background-color: #2ecc71;
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
    </style>
</head>
<body>
    <div class="container">
        <a href="index.php" class="back-btn">← Volver</a>
        <h1>Agregar Categoría y Registro</h1>
        
        <?php if (!empty($message)): ?>
            <p class="success"><?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?></p>
        <?php endif; ?>
        <?php if (!empty($error)): ?>
            <p class="error"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></p>
        <?php endif; ?>
        
        <form method="POST" action="">
            <h2>Agregar Categoría</h2>
            <div class="form-group">
                <label for="category_name">Nombre de la Categoría</label>
                <input type="text" id="category_name" name="category_name" placeholder="Nombre de la categoría" required>
            </div>
            <button type="submit" name="add_category" class="btn">Agregar Categoría</button>
        </form>

        <form method="POST" action="">
            <h2>Agregar Registro</h2>
            <div class="form-group">
                <label for="category_id">Categoría</label>
                <select id="category_id" name="category_id" required>
                    <option value="">-- Selecciona una categoría --</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?php echo htmlspecialchars($category['id'], ENT_QUOTES, 'UTF-8'); ?>">
                            <?php echo htmlspecialchars($category['nombre_categoria'], ENT_QUOTES, 'UTF-8'); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="marca">Marca</label>
                <input type="text" id="marca" name="marca" placeholder="Marca" required>
            </div>
            <div class="form-group">
                <label for="modelo">Modelo</label>
                <input type="text" id="modelo" name="modelo" placeholder="Modelo" required>
            </div>
            <div class="form-group">
                <label for="serie">Serie</label>
                <input type="text" id="serie" name="serie" placeholder="Serie" required>
            </div>
            <div class="form-group">
                <label for="caracteristicas">Características</label>
                <textarea id="caracteristicas" name="caracteristicas" placeholder="Características"></textarea>
            </div>
            <div class="form-group">
                <label for="otorgado_a">¿A quién fue otorgado?</label>
                <input type="text" id="otorgado_a" name="otorgado_a" placeholder="Nombre de quien recibió el ítem" required>
            </div>
            <button type="submit" name="add_record" class="btn">Añadir Registro</button>
        </form>
    </div>
</body>
</html>
