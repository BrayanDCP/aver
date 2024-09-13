<?php
// Mostrar errores y advertencias para depuración
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
} catch (PDOException $e) {
    die("Conexión fallida: " . $e->getMessage());
}

// Inicialización de variables
$categories = [];
$records = [];
$searchTerm = '';
$selectedCategoryId = null;
$editRecord = null;
$page = filter_input(INPUT_GET, 'page', FILTER_SANITIZE_STRING) ?? 'home';

// Obtener todas las categorías
try {
    $stmt = $pdo->query("SELECT id, nombre_categoria FROM categorias ORDER BY nombre_categoria");
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error al obtener las categorías: " . $e->getMessage());
}

// Obtener la categoría seleccionada y el término de búsqueda
$selectedCategoryId = filter_input(INPUT_GET, 'category_id', FILTER_SANITIZE_NUMBER_INT);
$searchTerm = filter_input(INPUT_GET, 'search_term', FILTER_SANITIZE_STRING);

// Construir la consulta para obtener los registros
$sql_get_records = "SELECT r.id, r.model, r.brand, r.series, r.characteristics, r.assigned_to, c.nombre_categoria as category_name
                    FROM records r
                    JOIN categorias c ON r.category_id = c.id" .
                    ($selectedCategoryId ? " WHERE c.id = " . intval($selectedCategoryId) : "") .
                    ($searchTerm ? " AND r.series LIKE '%" . $pdo->quote($searchTerm) . "%'" : "");

// Ejecutar la consulta para obtener registros
try {
    $stmt = $pdo->query($sql_get_records);
    $records = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error al obtener los registros: " . $e->getMessage());
}

// Manejo de eliminación de registros
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_record'])) {
    $deleteId = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);

    if ($deleteId) {
        $sql_delete = "DELETE FROM records WHERE id = ?";
        $stmt = $pdo->prepare($sql_delete);

        if ($stmt) {
            $stmt->bindValue(1, $deleteId, PDO::PARAM_INT);
            if ($stmt->execute()) {
                // Reordenar los IDs
                $sql_reorder = "SET @rank = 0; UPDATE records SET id = @rank := @rank + 1";
                $pdo->query($sql_reorder);

                header("Location: ?page=home&category_id=" . urlencode($selectedCategoryId) . "&search_term=" . urlencode($searchTerm));
                exit;
            } else {
                die("Error al eliminar el registro: " . $stmt->errorInfo()[2]);
            }
        } else {
            die("Error en la preparación de la consulta SQL: " . $pdo->errorInfo()[2]);
        }
    }
}

// Manejo de edición de registros
if (isset($_GET['edit_id'])) {
    $editId = filter_input(INPUT_GET, 'edit_id', FILTER_SANITIZE_NUMBER_INT);

    if ($editId) {
        $sql_get_record = "SELECT id, model, brand, series, characteristics, assigned_to, category_id
                           FROM records WHERE id = ?";
        $stmt = $pdo->prepare($sql_get_record);

        if ($stmt) {
            $stmt->bindValue(1, $editId, PDO::PARAM_INT);
            $stmt->execute();
            $editRecord = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$editRecord) {
                die("Registro no encontrado.");
            }
        } else {
            die("Error en la preparación de la consulta SQL: " . $pdo->errorInfo()[2]);
        }
    }
}

// Procesar actualización de registros
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_record'])) {
    $updateId = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
    $model = filter_input(INPUT_POST, 'model', FILTER_SANITIZE_STRING);
    $brand = filter_input(INPUT_POST, 'brand', FILTER_SANITIZE_STRING);
    $series = filter_input(INPUT_POST, 'series', FILTER_SANITIZE_STRING);
    $characteristics = filter_input(INPUT_POST, 'characteristics', FILTER_SANITIZE_STRING);
    $assignedTo = filter_input(INPUT_POST, 'assigned_to', FILTER_SANITIZE_STRING);
    $categoryId = filter_input(INPUT_POST, 'category_id', FILTER_SANITIZE_NUMBER_INT);

    if ($updateId) {
        $sql_update = "UPDATE records SET model = ?, brand = ?, series = ?, characteristics = ?, assigned_to = ?, category_id = ? WHERE id = ?";
        $stmt = $pdo->prepare($sql_update);

        if ($stmt) {
            $stmt->bindValue(1, $model, PDO::PARAM_STR);
            $stmt->bindValue(2, $brand, PDO::PARAM_STR);
            $stmt->bindValue(3, $series, PDO::PARAM_STR);
            $stmt->bindValue(4, $characteristics, PDO::PARAM_STR);
            $stmt->bindValue(5, $assignedTo, PDO::PARAM_STR);
            $stmt->bindValue(6, $categoryId, PDO::PARAM_INT);
            $stmt->bindValue(7, $updateId, PDO::PARAM_INT);
            if ($stmt->execute()) {
                header("Location: ?page=home&category_id=" . urlencode($selectedCategoryId) . "&search_term=" . urlencode($searchTerm));
                exit;
            } else {
                die("Error al actualizar el registro: " . $stmt->errorInfo()[2]);
            }
        } else {
            die("Error en la preparación de la consulta SQL: " . $pdo->errorInfo()[2]);
        }
    }
}

// Obtener estadísticas de registros por categoría
$statistics = [];
try {
    $stmt = $pdo->query("SELECT c.nombre_categoria, COUNT(r.id) as record_count
                         FROM categorias c
                         LEFT JOIN records r ON c.id = r.category_id
                         GROUP BY c.id");
    $statistics = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error al obtener las estadísticas: " . $e->getMessage());
}

// Cerrar conexión a la base de datos
$pdo = null;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Innovador - Gestión de Categorías y Registros</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .dashboard-container {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        .main-content {
            flex: 1;
        }
        @media(min-width: 768px) {
            .dashboard-container {
                flex-direction: row;
            }
            .sidebar {
                width: 20rem;
            }
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <aside class="sidebar bg-gray-800 text-white p-6">
            <h1 class="text-2xl font-bold mb-8">Dashboard</h1>
            <nav>
                <ul class="space-y-4">
                    <li><a href="?page=home" class="flex items-center"><i class="fas fa-home mr-2"></i> Inicio</a></li>
                    <li><a href="view_categories.php?page=statistics" class="flex items-center"><i class="fas fa-chart-bar mr-2"></i> Estadísticas</a></li>
                    <li><a href="view_categories.php?page=settings" class="flex items-center"><i class="fas fa-cog mr-2"></i> Configuración</a></li>
                </ul>
            </nav>
        </aside>
        <main class="main-content p-6">
            <?php if ($page === 'home'): ?>
                <header class="flex flex-col md:flex-row justify-between items-center mb-8">
                    <h1 class="text-3xl font-bold">Gestión de Categorías y Registros</h1>
                    <div class="flex space-x-4 mt-4 md:mt-0">
                        <a href="add_record.php" class="btn-primary flex items-center bg-blue-600 text-white py-2 px-4 rounded">
                            <i class="fas fa-plus mr-2"></i> Nuevo Registro
                        </a>
                        <a href="dashboard.php" class="btn-primary flex items-center bg-blue-900 text-white py-2 px-4 rounded">
                            <i class="fas fa-tachometer-alt mr-2"></i> Panel de Control
                        </a>
                    </div>
                </header>

                <section class="card p-6 mb-8 bg-white shadow rounded">
                    <h2 class="text-xl font-semibold mb-4">Filtrar Registros</h2>
                    <form method="get" action="" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <input type="hidden" name="page" value="home">
                        <div>
                            <label class="block text-sm font-medium mb-1" for="category_id">Categoría:</label>
                            <select id="category_id" name="category_id" class="w-full p-2 border rounded">
                                <option value="">Todas las categorías</option>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?= htmlspecialchars($category['id']) ?>"
                                        <?= $selectedCategoryId == $category['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($category['nombre_categoria']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1" for="search_term">Buscar:</label>
                            <input type="text" id="search_term" name="search_term" value="<?= htmlspecialchars($searchTerm) ?>"
                                   class="w-full p-2 border rounded">
                        </div>
                        <div class="md:col-span-2">
                            <button type="submit" class="btn-primary bg-blue-600 text-white py-2 px-4 rounded">
                                <i class="fas fa-search mr-2"></i> Filtrar
                            </button>
                        </div>
                    </form>
                </section>

                <?php if (is_array($records) && count($records) > 0): ?>
                    <section class="card p-6 bg-white shadow rounded">
                        <h2 class="text-xl font-semibold mb-4">Registros</h2>
                        <div class="table-container overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead>
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">N°</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Modelo</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Marca</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Serie</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Características</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Asignado a</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Categoría</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <?php
                                    $counter = 1;
                                    foreach ($records as $record): ?>
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap"><?= $counter++ ?></td>
                                            <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($record['model']) ?></td>
                                            <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($record['brand']) ?></td>
                                            <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($record['series']) ?></td>
                                            <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($record['characteristics']) ?></td>
                                            <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($record['assigned_to']) ?></td>
                                            <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($record['category_name']) ?></td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                                <a href="?page=home&edit_id=<?= htmlspecialchars($record['id']) ?>" class="text-blue-500 hover:text-blue-700 mr-2">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form method="post" action="" class="inline">
                                                    <input type="hidden" name="id" value="<?= htmlspecialchars($record['id']) ?>">
                                                    <button type="submit" name="delete_record" class="text-red-500 hover:text-red-700">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </section>
                <?php else: ?>
                    <section class="card p-6 bg-white shadow rounded">
                        <h2 class="text-xl font-semibold mb-4">No hay registros</h2>
                        <p>No se encontraron registros para los criterios de búsqueda seleccionados.</p>
                    </section>
                <?php endif; ?>

            <?php elseif ($page === 'statistics'): ?>
                <header class="mb-8">
                    <h1 class="text-3xl font-bold">Estadísticas de Categorías</h1>
                </header>
                <section class="card p-6 bg-white shadow rounded">
                    <canvas id="categoryStatsChart"></canvas>
                </section>
                <script>
                    const ctx = document.getElementById('categoryStatsChart').getContext('2d');
                    const categoryStatsChart = new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: <?= json_encode(array_column($statistics, 'nombre_categoria')) ?>,
                            datasets: [{
                                label: 'Número de Registros',
                                data: <?= json_encode(array_column($statistics, 'record_count')) ?>,
                                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                                borderColor: 'rgba(75, 192, 192, 1)',
                                borderWidth: 1
                            }]
                        },
                        options: {
                            scales: {
                                y: {
                                    beginAtZero: true
                                }
                            }
                        }
                    });
                </script>
            <?php elseif ($page === 'settings'): ?>
                <header class="mb-8">
                    <h1 class="text-3xl font-bold">Configuración</h1>
                </header>
                <section class="card p-6 bg-white shadow rounded">
                    <p>Aquí puede configurar los ajustes de la aplicación.</p>
                    <!-- Aquí puedes añadir más configuraciones o ajustes -->
                </section>
            <?php endif; ?>
        </main>
    </div>
</body>
</html>
