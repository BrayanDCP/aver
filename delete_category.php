<?php 
// Configuración de la base de datos
$host = 'localhost';
$dbname = 'mi_base_de_datos';
$username = 'root';
$password = '';

try {
    // Crear una nueva conexión PDO
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Manejo de solicitudes AJAX
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $response = ['success' => false, 'message' => ''];

    if ($_POST['action'] === 'delete' && isset($_POST['id'])) {
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        if ($id) {
            try {
                // Preparar y ejecutar la consulta para eliminar
                $stmt = $pdo->prepare("DELETE FROM categorias WHERE id = ?");
                $stmt->execute([$id]);
                $response = ['success' => true, 'message' => 'Categoría eliminada con éxito'];
            } catch(PDOException $e) {
                $response['message'] = "Error al eliminar la categoría: " . $e->getMessage();
            }
        } else {
            $response['message'] = "ID de categoría inválido";
        }
    } elseif ($_POST['action'] === 'add' && isset($_POST['name'])) {
        $name = trim($_POST['name']);
        if (!empty($name)) {
            try {
                // Preparar y ejecutar la consulta para agregar
                $stmt = $pdo->prepare("INSERT INTO categorias (nombre_categoria) VALUES (:name)");
                $stmt->bindParam(':name', $name, PDO::PARAM_STR);
                $stmt->execute();
                $response = ['success' => true, 'message' => 'Categoría agregada con éxito', 'id' => $pdo->lastInsertId()];
            } catch(PDOException $e) {
                $response['message'] = "Error al agregar la categoría: " . $e->getMessage();
            }
        } else {
            $response['message'] = "El nombre de la categoría no puede estar vacío";
        }
    } elseif ($_POST['action'] === 'edit' && isset($_POST['id']) && isset($_POST['name'])) {
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        $name = trim($_POST['name']);
        if ($id && !empty($name)) {
            try {
                // Preparar y ejecutar la consulta para actualizar
                $stmt = $pdo->prepare("UPDATE categorias SET nombre_categoria = ? WHERE id = ?");
                $stmt->execute([$name, $id]);
                $response = ['success' => true, 'message' => 'Categoría actualizada con éxito'];
            } catch(PDOException $e) {
                $response['message'] = "Error al actualizar la categoría: " . $e->getMessage();
            }
        } else {
            $response['message'] = "ID de categoría inválido o nombre vacío";
        }
    }

    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

// Función para obtener todas las categorías
function getCategories($pdo) {
    try {
        $stmt = $pdo->query("SELECT id, nombre_categoria FROM categorias ORDER BY nombre_categoria");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch(PDOException $e) {
        return [];
    }
}

// Obtener las categorías para mostrar
$categories = getCategories($pdo);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Gestión de Categorías</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&family=Pacifico&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        :root {
            --primary-color: #4a90e2;
            --secondary-color: #50c878;
            --accent-color: #ffa500;
            --background-color: #f0f4f8;
            --text-color: #333;
            --card-background: #ffffff;
            --border-color: #e0e0e0;
            --success-color: #28a745;
            --error-color: #dc3545;
            --hover-color: #3a7bd5;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: url('images/paisaje1.png') no-repeat center center fixed;
            background-size: cover;
            color: var(--text-color);
            line-height: 1.6;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 2rem;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        .container {
            max-width: 1000px;
            width: 100%;
            background-color: var(--card-background);
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            display: grid;
            grid-template-columns: 1fr 2fr;
        }

        .sidebar {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)),
                        url('images/paisaje1.png') no-repeat center center;
            background-size: cover;
            padding: 2rem;
            color: white;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .main-content {
            padding: 2rem;
        }

        h1 {
            font-family: 'Pacifico', cursive;
            color: white;
            text-align: center;
            margin-bottom: 2rem;
            font-size: 2.5rem;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.1);
        }

        .category-list {
            list-style-type: none;
            padding: 0;
            display: grid;
            gap: 1rem;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        }

        .category-item {
            background-color: var(--card-background);
            border-radius: 10px;
            padding: 1rem;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }

        .category-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 12px rgba(0,0,0,0.15);
        }

        .category-name {
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: var(--primary-color);
        }

        .btn {
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: 600;
            font-size: 0.9rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .btn i {
            margin-right: 0.5rem;
        }

        .btn-delete {
            background-color: var(--error-color);
            color: white;
        }

        .btn-delete:hover {
            background-color: #c82333;
        }

        .btn-edit {
            background-color: var(--accent-color);
            color: white;
            margin-right: 0.5rem;
        }

        .btn-edit:hover {
            background-color: #e69500;
        }

        .add-category {
            margin-top: 2rem;
        }

        .add-category input {
            width: 100%;
            padding: 0.7rem;
            border: 2px solid var(--border-color);
            border-radius: 5px;
            font-size: 1rem;
            transition: border-color 0.3s ease;
        }

        .add-category input:focus {
            border-color: var(--primary-color);
            outline: none;
        }

        .btn-add {
            background-color: var(--success-color);
            color: white;
            width: 100%;
            margin-top: 1rem;
        }

        .btn-add:hover {
            background-color: #218838;
        }

        .message {
            padding: 1rem;
            margin: 1rem 0;
            border-radius: 5px;
            text-align: center;
            font-weight: 600;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .message.show {
            opacity: 1;
        }

        .message.success {
            background-color: var(--success-color);
            color: white;
        }

        .message.error {
            background-color: var(--error-color);
            color: white;
        }

        .btn-back {
            display: inline-flex;
            align-items: center;
            background-color: var(--primary-color);
            color: white;
            border: none;
            border-radius: 5px;
            padding: 0.5rem 1rem;
            font-size: 1rem;
            cursor: pointer;
            text-decoration: none;
        }

        .btn-back i {
            margin-right: 0.5rem;
        }

        .btn-back:hover {
            background-color: var(--hover-color);
        }

        .container img {
            width: 100%;
            margin-top: 2rem;
            border-radius: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="sidebar">
            <h1>Gestión de Categorías</h1>
            <!-- Aquí puedes agregar más contenido si es necesario -->
        </div>
        <div class="main-content">
            <button class="btn-back" onclick="window.location.href='dashboard.php'"><i class="fas fa-arrow-left"></i> Volver al Panel de Control</button>
            <div class="message" id="message"></div>
            <div class="category-list">
                <?php foreach ($categories as $category): ?>
                    <div class="category-item">
                        <div class="category-name"><?php echo htmlspecialchars($category['nombre_categoria']); ?></div>
                        <button class="btn btn-edit" onclick="editCategory(<?php echo $category['id']; ?>)"><i class="fas fa-edit"></i> Editar</button>
                        <button class="btn btn-delete" onclick="deleteCategory(<?php echo $category['id']; ?>)"><i class="fas fa-trash"></i> Eliminar</button>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="add-category">
                <input type="text" id="categoryName" placeholder="Nombre de la categoría">
                <button class="btn btn-add" onclick="addCategory()"><i class="fas fa-plus"></i> Añadir Categoría</button>
    </div>
    <script>
        function showMessage(type, message) {
            const messageElement = document.getElementById('message');
            messageElement.className = `message ${type} show`;
            messageElement.textContent = message;
            setTimeout(() => {
                messageElement.classList.remove('show');
            }, 3000);
        }

        function deleteCategory(id) {
            if (confirm('¿Estás seguro de que quieres eliminar esta categoría?')) {
                fetch('', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: new URLSearchParams({
                        action: 'delete',
                        id: id
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showMessage('success', data.message);
                        document.querySelector(`[onclick="deleteCategory(${id})"]`).closest('.category-item').remove();
                    } else {
                        showMessage('error', data.message);
                    }
                });
            }
        }

        function addCategory() {
            const name = document.getElementById('categoryName').value.trim();
            if (name === '') {
                showMessage('error', 'El nombre de la categoría no puede estar vacío');
                return;
            }
            fetch('', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams({
                    action: 'add',
                    name: name
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showMessage('success', data.message);
                    const categoryList = document.querySelector('.category-list');
                    const newCategory = document.createElement('div');
                    newCategory.className = 'category-item';
                    newCategory.innerHTML = 
                        `<div class="category-name">${name}</div>
                        <button class="btn btn-edit" onclick="editCategory(${data.id})"><i class="fas fa-edit"></i> Editar</button>
                        <button class="btn btn-delete" onclick="deleteCategory(${data.id})"><i class="fas fa-trash"></i> Eliminar</button>`;
                    ;
                    categoryList.appendChild(newCategory);
                    document.getElementById('categoryName').value = '';
                } else {
                    showMessage('error', data.message);
                }
            });
        }

        function editCategory(id) {
            const name = prompt('Introduce el nuevo nombre de la categoría:');
            if (name === null || name.trim() === '') {
                showMessage('error', 'El nombre de la categoría no puede estar vacío');
                return;
            }
            fetch('', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams({
                    action: 'edit',
                    id: id,
                    name: name
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showMessage('success', data.message);
                    document.querySelector(`[onclick="editCategory(${id})"]`).previousElementSibling.textContent = name;
                } else {
                    showMessage('error', data.message);
                }
            });
        }
    </script>
</body>
</html>
