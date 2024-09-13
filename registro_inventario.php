<?php
// Conexión a la base de datos
$host = 'localhost'; // Cambia esto si tu servidor MySQL no está en localhost
$db = 'nombre_base_datos'; // Reemplaza con el nombre de tu base de datos
$user = 'nuevo_usuario'; // Reemplaza con el nuevo nombre de usuario
$pass = 'nueva_contraseña'; // Reemplaza con la nueva contraseña

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die('Error de conexión: ' . $e->getMessage());
}

// Eliminar un registro
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    try {
        $stmt = $pdo->prepare("DELETE FROM inventory WHERE id = ?");
        $stmt->execute([$id]);
        header("Location: {$_SERVER['PHP_SELF']}"); // Redirigir para evitar reenvíos de formulario
        exit;
    } catch (PDOException $e) {
        $message = "<p>Error al eliminar el registro: " . $e->getMessage() . "</p>";
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
            overflow-x: auto; /* Permite el desplazamiento horizontal */
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
                overflow-x: auto; /* Asegura que la tabla pueda desplazarse horizontalmente */
            }

            th, td {
                padding: 0.25rem;
            }
        }
    </style>
</head>
<body>
    <header>
        <a href="edit.php?id=<?= isset($_GET['id']) ? htmlspecialchars($_GET['id']) : '' ?>">Volver a Editar</a>
        <h1>Registro de Inventario</h1>
    </header>

    <div class="container">
        <!-- Mensaje de estado -->
        <?php if (isset($message)) echo $message; ?>

        <!-- Tabla de inventario -->
        <table>
            <thead>
                <tr>
                    <th>N°</th>
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
                    $counter = 1; // Inicializar contador para los números secuenciales
                    foreach ($inventory as $row) {
                        echo "<tr>
                                <td>{$counter}</td>
                                <td>{$row['material']}</td>
                                <td>{$row['initial_quantity']}</td>
                                <td>{$row['withdrawn_quantity']}</td>
                                <td>{$row['deposited_quantity']}</td>
                                <td>{$row['remaining_quantity']}</td>
                                <td class='action-buttons'>
                                    <a href='edit.php?id={$row['id']}' class='edit'>Editar</a>
                                    <a href='?delete={$row['id']}' class='delete' onclick='return confirm(\"¿Estás seguro de que quieres eliminar este registro?\");'>Eliminar</a>
                                </td>
                              </tr>";
                        $counter++; // Incrementar contador
                    }
                } else {
                    echo "<tr><td colspan='7'>No hay datos disponibles</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <footer>
        <p>&copy; 2024 Tecdatel. Todos los derechos reservados.</p>
    </footer>
</body>
</html>
