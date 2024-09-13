<?php
session_start();

// Verificar si el usuario está autenticado
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Incluye la configuración de la base de datos
require 'config.php';

// Obtener los detalles del usuario
$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
if ($stmt === false) {
    die('Error en la preparación de la consulta: ' . $conn->error);
}
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Verificar si se obtuvieron datos del usuario
if (!$user) {
    echo "No se encontraron datos para el usuario.";
    exit();
}

// Procesar la actualización del perfil
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
    $surname = filter_input(INPUT_POST, 'surname', FILTER_SANITIZE_STRING);
    $address = filter_input(INPUT_POST, 'address', FILTER_SANITIZE_STRING);
    $age = filter_input(INPUT_POST, 'age', FILTER_VALIDATE_INT);

    if ($name && $surname && $address && $age !== false) {
        $sql_update = "UPDATE users SET name = ?, surname = ?, address = ?, age = ? WHERE id = ?";
        $stmt_update = $conn->prepare($sql_update);
        if ($stmt_update === false) {
            die('Error en la preparación de la consulta de actualización: ' . $conn->error);
        }
        $stmt_update->bind_param("sssii", $name, $surname, $address, $age, $user_id);

        if ($stmt_update->execute()) {
            $success_message = "Perfil actualizado correctamente.";
        } else {
            $error_message = "Error al actualizar el perfil: " . $stmt_update->error;
        }

        $stmt_update->close();
    } else {
        $error_message = "Por favor, complete todos los campos correctamente.";
    }
}

$stmt->close();
$conn->close();
?>
