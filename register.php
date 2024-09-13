<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

$config = [
    'servername' => "localhost",
    'username' => "root",
    'password' => "",
    'dbname' => "usuarios_db"
];

function connectToDatabase($config) {
    $conn = new mysqli($config['servername'], $config['username'], $config['password'], $config['dbname']);
    if ($conn->connect_error) {
        die("Conexión fallida: " . $conn->connect_error);
    }
    return $conn;
}

$error = '';
$success = '';
$email = '';
$username = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $username = filter_var($_POST['username'] ?? '', FILTER_SANITIZE_STRING);

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Correo electrónico no válido.";
    } elseif (empty($username) || strlen($username) < 3) {
        $error = "El nombre de usuario debe tener al menos 3 caracteres.";
    } elseif (empty($password)) {
        $error = "Por favor, ingresa una contraseña.";
    } elseif ($password !== $confirm_password) {
        $error = "Las contraseñas no coinciden.";
    } elseif (strlen($password) < 8) {
        $error = "La contraseña debe tener al menos 8 caracteres.";
    } else {
        $conn = connectToDatabase($config);

        $sql = "SELECT * FROM users WHERE email = ? OR username = ?";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            die("Error en la preparación de la consulta: " . $conn->error);
        }
        $stmt->bind_param("ss", $email, $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $user_exists = $result->num_rows > 0;

        if ($user_exists) {
            $error = "El usuario o correo electrónico ya está registrado.";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $sql = "INSERT INTO users (email, username, password) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($sql);
            if (!$stmt) {
                die("Error en la preparación de la consulta: " . $conn->error);
            }
            $stmt->bind_param("sss", $email, $username, $hashed_password);

            if ($stmt->execute()) {
                $success = "Usuario registrado exitosamente.";
            } else {
                $error = "Error al registrar el usuario: " . $stmt->error;
            }

            $stmt->close();
        }

        $conn->close();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Usuario</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #4a69bd;
            --secondary-color: #6a89cc;
            --background-color: #f1f2f6;
            --text-color: #2f3542;
            --error-color: #e55039;
            --success-color: #78e08f;
            --input-bg: #ffffff;
            --input-border: #dcdde1;
        }

        body, html {
            font-family: 'Roboto', sans-serif;
            background: linear-gradient(135deg, var(--secondary-color) 0%, var(--primary-color) 100%);
            color: var(--text-color);
            height: 100%;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .container {
            background: rgba(255, 255, 255, 0.95);
            padding: 4rem;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 400px;
            text-align: flex;
            backdrop-filter: blur(5px);
            transition: all 0.3s ease-in-out;
        }

        .container:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(0,0,0,0.2);
        }

        h2 {
            margin-bottom: 2rem;
            font-size: 2.2rem;
            color: var(--primary-color);
            text-shadow: 1px 1px 2px rgba(0,0,0,0.1);
        }

        .message {
            padding: 0.8rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            font-weight: 500;
            animation: fadeIn 0.5s ease-out;
        }

        .error {
            color: var(--error-color);
            background: rgba(229, 80, 57, 0.1);
            border: 1px solid var(--error-color);
        }

        .success {
            color: var(--success-color);
            background: rgba(120, 224, 143, 0.1);
            border: 1px solid var(--success-color);
        }

        form {
            display: flex;
            flex-direction: column;
        }

        .input-group {
            position: relative;
            margin-bottom: 1.2rem;
        }

        .input-group i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--secondary-color);
            transition: all 0.3s;
        }

        input {
            width: 100%;
            padding: 0.8rem 1rem 0.8rem 2.5rem;
            border: 2px solid var(--input-border);
            border-radius: 25px;
            font-size: 1rem;
            transition: all 0.3s;
            background-color: var(--input-bg);
            color: var(--text-color);
        }

        input:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(74, 105, 189, 0.1);
        }

        button {
            padding: 0.8rem;
            border: none;
            border-radius: 25px;
            background: linear-gradient(45deg, var(--primary-color), var(--secondary-color));
            color: #ffffff;
            font-size: 1.1rem;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        button:hover {
            background: linear-gradient(45deg, var(--secondary-color), var(--primary-color));
            box-shadow: 0 5px 15px rgba(74, 105, 189, 0.4);
        }

        button:active {
            transform: scale(0.98);
        }

        a {
            display: inline-block;
            margin-top: 1.5rem;
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s;
        }

        a:hover {
            color: var(--secondary-color);
        }

        .password-strength {
            display: flex;
            justify-content: space-between;
            margin-top: 0.5rem;
        }

        .password-strength span {
            height: 4px;
            width: 18%;
            background: #dfe4ea;
            border-radius: 2px;
            transition: background 0.3s;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @media (max-width: 480px) {
            .container {
                padding: 2rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Registro de Usuario</h2>

        <?php if (!empty($error)): ?>
            <div class="message error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <?php if (!empty($success)): ?>
            <div class="message success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <form method="post">
            <div class="input-group">
                <i class="fas fa-user"></i>
                <input type="text" name="username" placeholder="Nombre de usuario" value="<?php echo htmlspecialchars($username); ?>" required>
            </div>

            <div class="input-group">
                <i class="fas fa-envelope"></i>
                <input type="email" name="email" placeholder="Correo electrónico" value="<?php echo htmlspecialchars($email); ?>" required>
            </div>

            <div class="input-group">
                <i class="fas fa-lock"></i>
                <input type="password" name="password" id="password" placeholder="Contraseña" required>
            </div>

            <div class="password-strength" id="password-strength">
                <span></span><span></span><span></span><span></span><span></span>
            </div>

            <div class="input-group">
                <i class="fas fa-lock"></i>
                <input type="password" name="confirm_password" placeholder="Confirmar contraseña" required>
            </div>

            <button type="submit">Registrarse</button>
        </form>

        <a href="login.php">Ya tengo una cuenta</a>
    </div>

    <script>
        const passwordInput = document.getElementById('password');
        const passwordStrength = document.getElementById('password-strength');

        passwordInput.addEventListener('input', () => {
            const value = passwordInput.value;
            let strength = 0;

            if (value.length > 7) strength++;
            if (/[A-Z]/.test(value)) strength++;
            if (/[0-9]/.test(value)) strength++;
            if (/[^A-Za-z0-9]/.test(value)) strength++;

            const spans = passwordStrength.querySelectorAll('span');
            spans.forEach((span, index) => {
                if (index < strength) {
                    span.style.background = 
                        strength === 1 ? '#e55039' :
                        strength === 2 ? '#fa983a' :
                        strength === 3 ? '#78e08f' :
                        '#4a69bd';
                } else {
                    span.style.background = '#dfe4ea';
                }
            });
        });
    </script>
</body>
</html>
