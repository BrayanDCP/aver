<?php
// Mostrar errores y advertencias para depuración (desactivar en producción)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Iniciar la sesión
session_start();

// Configuración de conexión a la base de datos
define('DB_SERVER', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'usuarios_db');

// Manejar errores de conexión de MySQL
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

function connectToDatabase() {
    $conn = new mysqli(DB_SERVER, DB_USER, DB_PASS, DB_NAME);
    if ($conn->connect_error) {
        die("Conexión fallida: " . $conn->connect_error);
    }
    return $conn;
}

function sanitizeInput($input) {
    return htmlspecialchars(strip_tags(trim($input)), ENT_QUOTES, 'UTF-8');
}

function authenticateUser($conn, $email, $password) {
    $email = sanitizeInput($email);
    $sql = "SELECT id, password FROM users WHERE email = ?";
    
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        
        // Verificar si se encontró algún usuario
        if ($stmt->num_rows === 1) {
            $stmt->bind_result($id, $hashed_password);
            $stmt->fetch(); // Recupera los valores de las variables
            
            // Verificar la contraseña
            if (password_verify($password, $hashed_password)) {
                $_SESSION['user_id'] = $id;
                $stmt->close();
                return ['success' => true];
            } else {
                $stmt->close();
                return ['success' => false, 'error' => "Correo o contraseña incorrectos."];
            }
        } else {
            $stmt->close();
            return ['success' => false, 'error' => "Correo o contraseña incorrectos."];
        }
    } else {
        return ['success' => false, 'error' => "Error en la consulta SQL: " . $conn->error];
    }
}

// Manejar la solicitud de inicio de sesión
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if (!empty($email) && !empty($password)) {
        $conn = connectToDatabase();
        $result = authenticateUser($conn, $email, $password);
        $conn->close();
        
        header('Content-Type: application/json');
        echo json_encode($result);
        exit;
    } else {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'error' => "Por favor, completa todos los campos."]);
        exit;
    }
}

// Si no es una solicitud POST, incluir el formulario HTML
include 'login.html';
?>
