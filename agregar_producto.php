<?php
include 'conexion.php'; // Asegúrate de que `conexion.php` maneja la conexión a la base de datos

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener y sanitizar las entradas del formulario
    $nombre = filter_input(INPUT_POST, 'nombre', FILTER_SANITIZE_STRING);
    $cantidad = filter_input(INPUT_POST, 'cantidad', FILTER_SANITIZE_NUMBER_INT);
    $precio = filter_input(INPUT_POST, 'precio', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    $fecha_ingreso = filter_input(INPUT_POST, 'fecha_ingreso', FILTER_SANITIZE_STRING);

    // Validar que los datos no están vacíos y son válidos
    if ($nombre && $cantidad !== false && $precio !== false && $fecha_ingreso) {
        // Preparar la consulta SQL para evitar inyección SQL
        $sql = "INSERT INTO productos (nombre, cantidad, precio, fecha_ingreso) VALUES (?, ?, ?, ?)";

        // Preparar la declaración
        if ($stmt = $conn->prepare($sql)) {
            // Vincular parámetros
            $stmt->bind_param('sids', $nombre, $cantidad, $precio, $fecha_ingreso);

            // Ejecutar la declaración
            if ($stmt->execute()) {
                // Redirigir a index.php con un mensaje de éxito
                header("Location: index.php?success=Producto agregado correctamente");
                exit();
            } else {
                // Manejar errores de ejecución
                echo "Error al agregar producto: " . $stmt->error;
            }

            // Cerrar la declaración
            $stmt->close();
        } else {
            // Manejar errores de preparación de consulta
            echo "Error en la preparación de la consulta: " . $conn->error;
        }
    } else {
        echo "Todos los campos son obligatorios y deben ser válidos.";
    }

    // Cerrar la conexión a la base de datos
    $conn->close();
}
?>
