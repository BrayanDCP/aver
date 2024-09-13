<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$host = 'localhost';         // Cambia esto si tu base de datos no está en el localhost
$db = 'nombre_base_de_datos'; // Reemplaza con el nombre de tu base de datos
$user = 'nombre_usuario';     // Reemplaza con tu nombre de usuario de base de datos
$pass = 'contraseña';         // Reemplaza con tu contraseña de base de datos

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}

$user_id = $_SESSION['user_id'];

$sql = "SELECT username, email, profile_picture FROM users WHERE id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if (!$user) {
    echo "User not found.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Perfil de Usuario</title>
</head>
<body>
    <h1>Perfil de <?php echo htmlspecialchars($user['username']); ?></h1>
    <p>Email: <?php echo htmlspecialchars($user['email']); ?></p>
    <p><img src="<?php echo htmlspecialchars($user['profile_picture']); ?>" alt="Foto de perfil" width="100"></p>
    <a href="logout.php">Cerrar sesión</a>
</body>
</html>


