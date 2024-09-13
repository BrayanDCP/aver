<?php
// Mostrar errores y advertencias para depuración (desactivar en producción)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Configuración de la base de datos
$host = 'localhost';
$dbname = 'mi_base_de_datos';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Obtener todas las categorías de la tabla 'categorias'
try {
    $stmt = $pdo->query("SELECT id, nombre_categoria FROM categorias ORDER BY nombre_categoria");
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Verificar si se recuperaron categorías
    if (!$categories) {
        throw new Exception("No se encontraron categorías en la base de datos.");
    }
} catch(PDOException $e) {
    die("Error al obtener las categorías: " . $e->getMessage());
} catch(Exception $e) {
    die($e->getMessage());
}

// Procesar la adición de un nuevo registro
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_record'])) {
    $category_id = filter_input(INPUT_POST, 'category_id', FILTER_SANITIZE_NUMBER_INT);
    $model = filter_input(INPUT_POST, 'model', FILTER_SANITIZE_STRING);
    $brand = filter_input(INPUT_POST, 'brand', FILTER_SANITIZE_STRING);
    $series = filter_input(INPUT_POST, 'series', FILTER_SANITIZE_STRING);
    $characteristics = filter_input(INPUT_POST, 'characteristics', FILTER_SANITIZE_STRING);
    $assigned_to = filter_input(INPUT_POST, 'assigned_to', FILTER_SANITIZE_STRING);

    // Verificar si todos los campos requeridos están presentes
    if ($category_id && $model && $brand && $series) {
        $sql_insert = "INSERT INTO records (category_id, model, brand, series, characteristics, assigned_to) 
                       VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql_insert);

        if ($stmt) {
            $stmt->bindValue(1, $category_id, PDO::PARAM_INT);
            $stmt->bindValue(2, $model, PDO::PARAM_STR);
            $stmt->bindValue(3, $brand, PDO::PARAM_STR);
            $stmt->bindValue(4, $series, PDO::PARAM_STR);
            $stmt->bindValue(5, $characteristics, PDO::PARAM_STR);
            $stmt->bindValue(6, $assigned_to, PDO::PARAM_STR);

            if ($stmt->execute()) {
                header("Location: view_categories.php"); // Redirigir después de añadir el registro
                exit;
            } else {
                die("Error al añadir el registro: " . $stmt->errorInfo()[2]);
            }
        } else {
            die("Error en la preparación de la consulta SQL: " . $pdo->errorInfo()[2]);
        }
    } else {
        $error_message = "Por favor, complete todos los campos requeridos.";
    }
}

// Cerrar conexión a la base de datos
$pdo = null;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Registro</title>
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #6c5ce7;
            --secondary-color: #a29bfe;
            --background-color: #f0f3ff;
            --text-color: #2d3436;
            --error-color: #d63031;
            --success-color: #00b894;
            --input-bg: #ffffff;
            --shadow-color: rgba(108, 92, 231, 0.1);
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Quicksand', sans-serif;
            background-color: var(--background-color);
            color: var(--text-color);
            line-height: 1.6;
        }

        .container {
            max-width: 1000px;
            margin: 40px auto;
            padding: 40px;
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 10px 30px var(--shadow-color);
        }

        .header {
            text-align: center;
            margin-bottom: 40px;
        }

        h2 {
            color: var(--primary-color);
            font-size: 32px;
            margin-bottom: 10px;
            font-weight: 700;
        }

        .nav-links {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-bottom: 30px;
            flex-wrap: wrap;
        }

        .nav-link {
            text-decoration: none;
            color: var(--primary-color);
            font-weight: 600;
            transition: all 0.3s ease;
            padding: 10px 20px;
            border-radius: 50px;
            background-color: var(--background-color);
        }

        .nav-link:hover {
            background-color: var(--primary-color);
            color: #fff;
            transform: translateY(-3px);
            box-shadow: 0 5px 15px var(--shadow-color);
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
        }

        .form-group {
            margin-bottom: 25px;
        }

        label {
            font-weight: 600;
            display: block;
            margin-bottom: 8px;
            color: var(--text-color);
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        input[type="text"], select {
            width: 100%;
            padding: 15px;
            border: 2px solid var(--secondary-color);
            border-radius: 12px;
            font-size: 16px;
            transition: all 0.3s ease;
            background-color: var(--input-bg);
            color: var(--text-color);
        }

        input[type="text"]:focus, select:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 4px rgba(108, 92, 231, 0.1);
        }

        button {
            display: block;
            width: 100%;
            padding: 15px;
            background-color: var(--primary-color);
            color: #fff;
            border: none;
            border-radius: 12px;
            font-size: 18px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        button:hover {
            background-color: var(--secondary-color);
            transform: translateY(-3px);
            box-shadow: 0 5px 15px var(--shadow-color);
        }

        .error-message {
            background-color: var(--error-color);
            color: #fff;
            padding: 15px;
            border-radius: 12px;
            margin-bottom: 25px;
            text-align: center;
            font-weight: 600;
            box-shadow: 0 5px 15px rgba(214, 48, 49, 0.2);
        }

        @media (max-width: 768px) {
            .container {
                padding: 30px;
                margin: 20px;
            }

            .form-grid {
                grid-template-columns: 1fr;
            }

            .nav-links {
                flex-direction: column;
                align-items: center;
                gap: 10px;
            }
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .container {
            animation: fadeIn 0.5s ease-out;
        }

        .form-group {
            transition: all 0.3s ease;
        }

        .form-group:hover {
            transform: translateY(-5px);
        }

        input[type="text"], select {
            transition: all 0.3s ease;
        }

        input[type="text"]:hover, select:hover {
            transform: scale(1.02);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Agregar Nuevo Registro</h2>
        </div>
        
        <nav class="nav-links">
            <a href="view_categories.php" class="nav-link">Ver Categorías</a>
            <a href="dashboard.php" class="nav-link">Panel de Control</a>
            <a href="delete_category.php" class="nav-link">Registrar Nueva Categoria</a>
        </nav>

        <?php if (isset($error_message)): ?>
            <div class="error-message"><?php echo htmlspecialchars($error_message); ?></div>
        <?php endif; ?>

        <form method="post" action="">
            <div class="form-grid">
                <div class="form-group">
                    <label for="category_id">Categoría</label>
                    <select id="category_id" name="category_id" required>
                        <option value="">--Selecciona una categoría--</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?php echo htmlspecialchars($category['id']); ?>">
                                <?php echo htmlspecialchars($category['nombre_categoria']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="model">Modelo</label>
                    <input type="text" id="model" name="model" required>
                </div>

                <div class="form-group">
                    <label for="brand">Marca</label>
                    <input type="text" id="brand" name="brand" required>
                </div>

                <div class="form-group">
                    <label for="series">Serie</label>
                    <input type="text" id="series" name="series" required>
                </div>

                <div class="form-group">
                    <label for="characteristics">Características</label>
                    <input type="text" id="characteristics" name="characteristics">
                </div>

                <div class="form-group">
                    <label for="assigned_to">Otorgado a</label>
                    <input type="text" id="assigned_to" name="assigned_to">
                </div>
            </div>

            <button type="submit" name="add_record">Agregar Registro</button>
        </form>
    </div>
</body>
</html>

