<?php
// Código de conexión a la base de datos
// ...

// Obtener el ID del material a actualizar
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    // Obtener los datos actuales del material
    try {
        $stmt = $pdo->prepare("SELECT * FROM inventory WHERE id = ?");
        $stmt->execute([$id]);
        $item = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $message = "<p>Error al obtener los datos del material: " . $e->getMessage() . "</p>";
    }

    // Procesar el formulario de actualización
    if (isset($_POST['update'])) {
        $withdrawn_quantity = intval($_POST['withdrawn_quantity']);
        $deposited_quantity = intval($_POST['deposited_quantity']);

        // Validar cantidades
        if ($withdrawn_quantity > $item['remaining_quantity']) {
            $message = "<p>Error: La cantidad retirada no puede ser mayor que la cantidad restante.</p>";
        } else {
            $new_remaining_quantity = $item['remaining_quantity'] - $withdrawn_quantity + $deposited_quantity;

            try {
                $stmt = $pdo->prepare("UPDATE inventory SET withdrawn_quantity = ?, deposited_quantity = ?, remaining_quantity = ? WHERE id = ?");
                $stmt->execute([$withdrawn_quantity, $deposited_quantity, $new_remaining_quantity, $id]);
                header("Location: inventory.php"); // Redirigir a la página de inventario
                exit;
            } catch (PDOException $e) {
                $message = "<p>Error al actualizar el material: " . $e->getMessage() . "</p>";
            }
        }
    }
} else {
    die('ID de material no proporcionado.');
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <!-- Código de encabezado -->
    <!-- ... -->
</head>
<body>
    <header>
        <a href="inventory.php">Volver a Inventario</a>
        <h1>Actualizar Material</h1>
    </header>

    <div class="container">
        <!-- Mensaje de estado -->
        <?php if (isset($message)) echo $message; ?>

        <!-- Formulario para actualizar cantidades -->
        <form action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>?id=<?= $id ?>" method="post">
            <h2>Actualizar Cantidades</h2>
            
            <label for="withdrawn_quantity">Cantidad Retirada:</label>
            <input type="number" id="withdrawn_quantity" name="withdrawn_quantity" min="0" value="<?= $item['withdrawn_quantity'] ?>">
            
            <label for="deposited_quantity">Cantidad Depositada:</label>
            <input type="number" id="deposited_quantity" name="deposited_quantity" min="0" value="<?= $item['deposited_quantity'] ?>">
            
            <button type="submit" name="update">Actualizar</button>
        </form>
    </div>

    <footer>
        <!-- Código del pie de página -->
        <!-- ... -->
    </footer>
</body>
</html>
