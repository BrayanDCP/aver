<?php
// Conexión a la base de datos
$host = 'localhost';
$db = 'nombre_base_datos';
$user = 'nuevo_usuario';
$pass = 'nueva_contraseña';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die('Error de conexión: ' . $e->getMessage());
}

// Manejo de eliminación
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);

    try {
        $stmt = $pdo->prepare("DELETE FROM inventory WHERE id = ?");
        $stmt->execute([$id]);
        header("Location: {$_SERVER['PHP_SELF']}");
        exit;
    } catch (PDOException $e) {
        $message = "<p>Error al eliminar el material: " . $e->getMessage() . "</p>";
    }
}

// Obtener todos los materiales para el menú desplegable
try {
    $stmt = $pdo->query("SELECT id, material FROM inventory ORDER BY material ASC");
    $materials = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $message = "<p>Error al obtener los materiales: " . $e->getMessage() . "</p>";
}

// Registrar un nuevo material
if (isset($_POST['register_material'])) {
    $material = trim($_POST['new_material']);
    $initial_quantity = intval($_POST['initial_quantity']);

    if (!empty($material) && $initial_quantity >= 0) {
        try {
            $stmt = $pdo->prepare("INSERT INTO inventory (material, initial_quantity, withdrawn_quantity, deposited_quantity, remaining_quantity) VALUES (?, ?, 0, 0, ?)");
            $stmt->execute([$material, $initial_quantity, $initial_quantity]);
            header("Location: {$_SERVER['PHP_SELF']}");
            exit;
        } catch (PDOException $e) {
            $message = "<p>Error al registrar el material: " . $e->getMessage() . "</p>";
        }
    } else {
        $message = "<p>Error: Todos los campos deben ser completados correctamente.</p>";
    }
}

// Actualizar cantidades de material
if (isset($_POST['update_quantities'])) {
    $id = intval($_POST['material_id']);
    $withdrawn_quantity = isset($_POST['withdrawn_quantity']) ? intval($_POST['withdrawn_quantity']) : 0;
    $deposited_quantity = isset($_POST['deposited_quantity']) ? intval($_POST['deposited_quantity']) : 0;

    try {
        $stmt = $pdo->prepare("SELECT * FROM inventory WHERE id = ?");
        $stmt->execute([$id]);
        $item = $stmt->fetch(PDO::FETCH_ASSOC);

        // Calcular la nueva cantidad restante
        $new_remaining_quantity = $item['remaining_quantity'] - $withdrawn_quantity + $deposited_quantity;

        // Actualizar el registro en la base de datos
        $stmt = $pdo->prepare("UPDATE inventory SET withdrawn_quantity = withdrawn_quantity + ?, deposited_quantity = deposited_quantity + ?, remaining_quantity = ? WHERE id = ?");
        $stmt->execute([$withdrawn_quantity, $deposited_quantity, $new_remaining_quantity, $id]);

        header("Location: {$_SERVER['PHP_SELF']}");
        exit;
    } catch (PDOException $e) {
        $message = "<p>Error al actualizar el material: " . $e->getMessage() . "</p>";
    }
}

// Obtener los datos de inventario
try {
    $stmt = $pdo->query("SELECT id, material, initial_quantity, withdrawn_quantity, deposited_quantity, remaining_quantity FROM inventory ORDER BY id ASC");
    $inventory = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $message = "<p>Error al obtener datos de inventario: " . $e->getMessage() . "</p>";
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Inventario</title>
    <style>
        /* Estilos generales */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            color: #333;
            background-color: #f8f9fa;
        }

        /* Header */
        header {
            background-color: #0056b3;
            color: #fff;
            padding: 1rem;
            text-align: center;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            position: relative;
        }

        header h1 {
            margin: 0;
            font-size: 2rem;
        }

        header a {
            color: #fff;
            text-decoration: none;
            font-size: 1rem;
            position: absolute;
            top: 1rem;
            right: 1rem;
        }

        header a:hover {
            text-decoration: underline;
        }

        /* Container */
        .container {
            width: 90%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 1rem;
        }

        /* Table */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 2rem;
            overflow-x: auto;
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 0.75rem;
            text-align: left;
        }

        th {
            background-color: #f1f1f1;
        }

        .action-buttons {
            display: flex;
            gap: 0.5rem;
        }

        .action-buttons a {
            text-decoration: none;
            color: #007bff;
        }

        .action-buttons a:hover {
            text-decoration: underline;
        }

        .action-buttons .edit {
            color: #28a745;
        }

        .action-buttons .delete {
            color: #dc3545;
        }

        /* Footer */
        footer {
            background-color: #333;
            color: #fff;
            padding: 1rem;
            text-align: center;
            box-shadow: 0 -2px 4px rgba(0, 0, 0, 0.1);
        }

        footer p {
            margin: 0;
            font-size: 0.9rem;
        }

        footer a {
            color: #fff;
            text-decoration: none;
            font-size: 1rem;
        }

        /* Estilos responsivos */
        @media (max-width: 768px) {
            header h1 {
                font-size: 1.5rem;
            }

            header a {
                font-size: 0.875rem;
                top: 0.5rem;
                right: 0.5rem;
            }

            .container {
                width: 100%;
                padding: 0.5rem;
            }

            table {
                font-size: 0.875rem;
            }

            th, td {
                padding: 0.5rem;
            }
        }

        @media (max-width: 480px) {
            header h1 {
                font-size: 1.25rem;
            }

            header a {
                font-size: 0.75rem;
                top: 0.25rem;
                right: 0.25rem;
            }

            table {
                font-size: 0.75rem;
                overflow-x: auto;
            }

            th, td {
                padding: 0.25rem;
            }
        }
    </style>
</head>
<body>
    <header>
        <h1>Registro de Inventario</h1>
        <a href="dashboard.php">Volver al Panel de Control</a>
    </header>

    <div class="container">
        <!-- Mensaje de estado -->
        <?php if (isset($message)) echo $message; ?>

        <!-- Formulario para registrar un nuevo material -->
        <h2>Registrar Nuevo Material</h2>
        <form action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>" method="post">
            <label for="new_material">Nombre del Material:</label>
            <input type="text" id="new_material" name="new_material" required>
            
            <label for="initial_quantity">Cantidad Inicial:</label>
            <input type="number" id="initial_quantity" name="initial_quantity" min="0" required>
            
            <button type="submit" name="register_material">Registrar</button>
        </form>

        <!-- Formulario para actualizar cantidades -->
        <h2>Actualizar Cantidades de Material</h2>
        <form action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>" method="post">
            <label for="material_id">Seleccionar Material:</label>
            <select id="material_id" name="material_id" required>
                <option value="">Seleccione un material</option>
                <?php
                if (isset($materials) && is_array($materials)) {
                    foreach ($materials as $material) {
                        echo "<option value=\"{$material['id']}\">{$material['material']}</option>";
                    }
                }
                ?>
            </select>
            
            <label for="withdrawn_quantity">Cantidad Retirada:</label>
            <input type="number" id="withdrawn_quantity" name="withdrawn_quantity" min="0">
            
            <label for="deposited_quantity">Cantidad Depositada:</label>
            <input type="number" id="deposited_quantity" name="deposited_quantity" min="0">
            
            <button type="submit" name="update_quantities">Actualizar</button>
        </form>

        <!-- Tabla de inventario -->
        <h2>Inventario</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Material</th>
                    <th>Cantidad Inicial</th>
                    <th>Cantidad Retirada</th>
                    <th>Cantidad Depositada</th>
                    <th>Cantidad Restante</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (isset($inventory) && is_array($inventory)) {
                    foreach ($inventory as $item) {
                        echo "<tr>
                            <td>{$item['id']}</td>
                            <td>{$item['material']}</td>
                            <td>{$item['initial_quantity']}</td>
                            <td>{$item['withdrawn_quantity']}</td>
                            <td>{$item['deposited_quantity']}</td>
                            <td>{$item['remaining_quantity']}</td>
                            <td class='action-buttons'>
                                <a href='?delete={$item['id']}' class='delete' onclick='return confirm(\"¿Estás seguro de que deseas eliminar este material?\")'>Eliminar</a>
                            </td>
                        </tr>";
                    }
                }
                ?>
            </tbody>
        </table>
    </div>

    <footer>
        <p>&copy; 2024 Registro de Inventario. Todos los derechos reservados.</p>
    </footer>
</body>
</html>
