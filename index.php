<?php
// Configuración de la base de datos
$host = 'localhost';
$dbname = 'mi_base_de_datos';
$username = 'root';
$password = '';

// Conectar a la base de datos
$mysqli = new mysqli($host, $username, $password, $dbname);

// Verificar la conexión
if ($mysqli->connect_error) {
    die("Conexión fallida: " . $mysqli->connect_error);
}

// Obtener todas las categorías usando una consulta preparada
$sql = "SELECT id, nombre_categoria FROM categorias";
$categories = [];

if ($stmt = $mysqli->prepare($sql)) {
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $categories[] = $row;
    }

    $stmt->close();
} else {
    echo "Error al preparar la consulta: " . $mysqli->error;
}

$mysqli->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tecdatel - Innovación Tecnológica</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700&family=Roboto:wght@300;400;700&display=swap" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
    <style>
        /* Estilos personalizados */
        .background {
            position: absolute;
            width: 100%;
            height: 100%;
            overflow: hidden;
        }
        #bgCanvas {
            width: 100%;
            height: 100%;
            display: block;
        }
        .content {
            position: relative;
            z-index: 10;
            padding: 20px;
        }
        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #333;
            color: #fff;
            padding: 10px 20px;
        }
        .logo-text {
            font-size: 24px;
            font-weight: 700;
        }
        nav ul {
            list-style: none;
            margin: 0;
            padding: 0;
            display: flex;
        }
        nav ul li {
            margin-left: 20px;
        }
        nav ul li a {
            color: #fff;
            text-decoration: none;
        }
        .btn-primary, .btn-secondary {
            display: inline-block;
            padding: 10px 20px;
            border-radius: 5px;
            color: #fff;
            text-decoration: none;
            font-weight: bold;
        }
        .btn-primary {
            background-color: #3498db;
        }
        .btn-primary:hover {
            background-color: #2980b9;
        }
        .btn-secondary {
            background-color: #2ecc71;
        }
        .btn-secondary:hover {
            background-color: #27ae60;
        }
        .glitch {
            font-size: 48px;
            color: #2c3e50;
            position: relative;
            display: inline-block;
        }
        .glitch::before, .glitch::after {
            content: attr(data-text);
            position: absolute;
            top: 0;
            left: 0;
            color: #e74c3c;
            clip: rect(0, 900px, 0, 0);
            z-index: -1;
            animation: glitch 2s infinite;
        }
        @keyframes glitch {
            0% {
                clip: rect(0, 900px, 0, 0);
            }
            25% {
                clip: rect(0, 900px, 0, 0);
                transform: translate(-10px, -10px);
            }
            50% {
                clip: rect(0, 900px, 0, 0);
                transform: translate(10px, 10px);
            }
            75% {
                clip: rect(0, 900px, 0, 0);
                transform: translate(-10px, 10px);
            }
            100% {
                clip: rect(0, 900px, 0, 0);
                transform: translate(10px, -10px);
            }
        }
        .container {
            max-width: 800px;
            margin: 20px auto;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .back-btn {
            display: inline-block;
            margin-bottom: 20px;
            text-decoration: none;
            color: #3498db;
            font-size: 16px;
        }
        .back-btn:hover {
            text-decoration: underline;
        }
        .category-button {
            margin-bottom: 10px;
        }
        .delete-btn {
            background-color: #e74c3c;
        }
        .delete-btn:hover {
            background-color: #c0392b;
        }
    </style>
</head>
<body>
    <div class="background">
        <canvas id="bgCanvas"></canvas>
    </div>
    <div class="content">
        <header>
            <div class="logo-text">TD</div>
            <nav>
                <ul>
                    <li><a href="#about">Nosotros</a></li>
                    <li><a href="#services">Servicios</a></li>
                    <li><a href="#contact">Contacto</a></li>
                </ul>
            </nav>
        </header>
        <main>
            <section id="home">
                <h1 class="glitch" data-text="Tecdatel">Tecdatel</h1>
                <p class="subtitle">Innovación Tecnológica al Alcance</p>
                <div class="cta-buttons">
                    <a href="login.php" class="btn btn-primary">Iniciar Sesión</a>
                    <a href="register.php" class="btn btn-secondary">Registrarme</a>
                </div>
            </section>
            <section id="about">
                <h2>Sobre Nosotros</h2>
                <p>
                    Tecdatel es líder en soluciones tecnológicas innovadoras con una amplia experiencia en el sector. 
                    Nuestro equipo está compuesto por expertos en diversas áreas tecnológicas, incluyendo desarrollo 
                    de software, ciberseguridad y computación en la nube. Trabajamos para ofrecer soluciones personalizadas 
                    que se adapten a las necesidades específicas de cada cliente, garantizando calidad y eficiencia en 
                    cada proyecto.
                </p>
                <p>
                    Creemos en la importancia de mantenerse a la vanguardia de la tecnología y en la necesidad de una 
                    infraestructura tecnológica robusta. Nuestra misión es proporcionar a nuestros clientes las herramientas 
                    necesarias para alcanzar sus objetivos y mantenerse competitivos en un mundo digital en constante 
                    evolución.
                </p>
            </section>
            <section id="services">
                <h2>Nuestros Servicios</h2>
                <div class="service-grid">
                    <div class="service-item">
                        <i class="icon-cloud"></i>
                        <h3>Cloud Computing</h3>
                        <p>Ofrecemos soluciones de computación en la nube que permiten a las empresas escalar sus recursos 
                        de manera flexible y eficiente. Desde la gestión de servidores hasta la implementación de aplicaciones 
                        en la nube, garantizamos una experiencia fluida y segura.</p>
                    </div>
                    <div class="service-item">
                        <i class="icon-mobile"></i>
                        <h3>Desarrollo Móvil</h3>
                        <p>Desarrollamos aplicaciones móviles a medida para iOS y Android, adaptadas a las necesidades 
                        de nuestros clientes. Nuestro enfoque es crear aplicaciones intuitivas, funcionales y con una 
                        excelente experiencia de usuario.</p>
                    </div>
                    <div class="service-item">
                        <i class="icon-shield"></i>
                        <h3>Ciberseguridad</h3>
                        <p>Protegemos tu infraestructura tecnológica contra amenazas y ataques cibernéticos mediante 
                        soluciones de ciberseguridad avanzadas. Ofrecemos análisis de vulnerabilidades, protección de 
                        datos y monitoreo continuo para garantizar la seguridad de tu información.</p>
                    </div>
                </div>
            </section>
            <section id="contact">
                <h2>Contáctanos</h2>
                <form id="contact-form">
                    <input type="email" placeholder="Tu correo electrónico" required>
                    <textarea placeholder="Tu mensaje" required></textarea>
                    <button type="submit" class="btn btn-primary">Enviar</button>
                </form>
            </section>
            <section id="categories">
                <h2>Categorías Disponibles</h2>
                <div class="container">
                    <a href="index.php" class="back-btn">← Volver</a>
                    <?php if (!empty($categories)): ?>
                        <?php foreach ($categories as $category): ?>
                            <div class="category-button">
                                <a href="view_category.php?id=<?php echo htmlspecialchars($category['id'], ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-primary">
                                    <?php echo htmlspecialchars($category['nombre_categoria'], ENT_QUOTES, 'UTF-8'); ?>
                                </a>
                                <a href="delete_category.php?id=<?php echo htmlspecialchars($category['id'], ENT_QUOTES, 'UTF-8'); ?>" class="btn delete-btn">
                                    Eliminar
                                </a>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p>No hay categorías disponibles.</p>
                    <?php endif; ?>
                </div>
            </section>
        </main>
    </div>
    <script src="script.js"></script>
</body>
</html>


