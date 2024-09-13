<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ver Registros</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        /* Incluir aquí el CSS inline si no estás utilizando un archivo externo */
    </style>
</head>
<body>
    <div class="container">
        <a href="dashboard.php" class="back-btn">Volver al Panel de Control</a>
        <h1>Ver Registros</h1>
        
        <form method="GET" action="">
            <label for="category">Selecciona una categoría:</label>
            <select id="category" name="category_id" onchange="this.form.submit()">
                <option value="">Todas las categorías</option>
                <?php foreach ($categories as $category): ?>
                    <option value="<?php echo htmlspecialchars($category['id']); ?>" 
                        <?php echo ($selectedCategoryId == $category['id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($category['nombre_categoria']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </form>

        <?php if (empty($records)): ?>
            <p class="no-data">No hay registros disponibles.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Marca</th>
                        <th>Modelo</th>
                        <th>Serie</th>
                        <th>Características</th>
                        <th>Otorgado a</th>
                        <th>Categoría</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($records as $record): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($record['id']); ?></td>
                            <td><?php echo htmlspecialchars($record['marca']); ?></td>
                            <td><?php echo htmlspecialchars($record['modelo']); ?></td>
                            <td><?php echo htmlspecialchars($record['serie']); ?></td>
                            <td><?php echo htmlspecialchars($record['caracteristicas']); ?></td>
                            <td><?php echo htmlspecialchars($record['otorgado_a']); ?></td>
                            <td><?php
                                // Mostrar el nombre de la categoría
                                $categoryId = $record['categoria_id'];
                                $conn2 = new mysqli($servername, $username, $password, $database);
                                if ($conn2->connect_error) {
                                    die("Conexión fallida: " . $conn2->connect_error);
                                }
                                $categoryStmt = $conn2->prepare("SELECT nombre_categoria FROM categorias WHERE id = ?");
                                $categoryStmt->bind_param("i", $categoryId);
                                $categoryStmt->execute();
                                $categoryResult = $categoryStmt->get_result();
                                $categoryName = 'No disponible';
                                if ($categoryRow = $categoryResult->fetch_assoc()) {
                                    $categoryName = htmlspecialchars($categoryRow['nombre_categoria']);
                                }
                                $categoryStmt->close();
                                $conn2->close();
                                echo $categoryName;
                            ?></td>
                            <td>
                                <a href="view_record_detail.php?id=<?php echo htmlspecialchars($record['id']); ?>">Ver Detalles</a>
                                <form method="POST" action="delete_record.php" style="display:inline;">
                                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($record['id']); ?>">
                                    <button type="submit" class="btn-delete">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</body>
</html>


