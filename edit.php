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

// Obtener los datos del registro a editar
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    try {
        $stmt = $pdo->prepare("SELECT * FROM inventory WHERE id = ?");
        $stmt->execute([$id]);
        $item = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        die('Error al obtener datos: ' . $e->getMessage());
    }
}

// Actualizar los datos del registro
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $material = $_POST['material'];
    $initial_quantity = $_POST['initial_quantity'];
    $withdrawn_quantity = $_POST['withdrawn_quantity'];
    $deposited_quantity = $_POST['deposited_quantity'];
    
    // Calcular cantidad restante
    $remaining_quantity = $initial_quantity - $withdrawn_quantity + $deposited_quantity;

    try {
        $stmt = $pdo->prepare("UPDATE inventory SET material = ?, initial_quantity = ?, withdrawn_quantity = ?, deposited_quantity = ?, remaining_quantity = ? WHERE id = ?");
        $stmt->execute([$material, $initial_quantity, $withdrawn_quantity, $deposited_quantity, $remaining_quantity, $id]);
        header("Location: inventory.php"); // Redirigir después de actualizar
        exit;
    } catch (PDOException $e) {
        $message = "<p>Error al actualizar el registro: " . $e->getMessage() . "</p>";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Inventario</title>
    <style>
        /* Agrega aquí los estilos que desees */
    </style>
</head>
<body>
    <header>
        <a href="inventory.php">Volver a Inventario</a>
        <h1>Editar Inventario</h1>
    </header>

    <div class="container">
        <!-- Mensaje de estado -->
        <?php if (isset($message)) echo $message; ?>

        <!-- Formulario de edición -->
        <form method="post">
            <label for="material">Material:</label>
            <input type="text" id="material" name="material" value="<?php echo htmlspecialchars($item['material']); ?>" required>
            
            <label for="initial_quantity">Cantidad Inicial:</label>
            <input type="number" id="initial_quantity" name="initial_quantity" value="<?php echo htmlspecialchars($item['initial_quantity']); ?>" required>
            
            <label for="withdrawn_quantity">Cantidad Retirada:</label>
            <input type="number" id="withdrawn_quantity" name="withdrawn_quantity" value="<?php echo htmlspecialchars($item['withdrawn_quantity']); ?>" required>
            
            <label for="deposited_quantity">Cantidad Depositada:</label>
            <input type="number" id="deposited_quantity" name="deposited_quantity" value="<?php echo htmlspecialchars($item['deposited_quantity']); ?>" required>
            
            <label for="remaining_quantity">Cantidad Restante:</label>
            <input type="number" id="remaining_quantity" name="remaining_quantity" value="<?php echo htmlspecialchars($item['remaining_quantity']); ?>" readonly>
            
            <button type="submit">Actualizar</button>
        </form>
    </div>
</body>
</html>

