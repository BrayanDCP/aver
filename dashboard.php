<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tecdatel Nexus</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&display=swap');

        :root {
            --bg-color: #000000;
            --text-color: #ffffff;
            --accent-color: #00ffff;
            --card-bg: #111111;
            --hover-bg: #222222;
            --shadow: 0 8px 32px 0 rgba(0, 255, 255, 0.1);
            --transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Space Grotesk', sans-serif;
            background-color: var(--bg-color);
            color: var(--text-color);
            line-height: 1.6;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            overflow-x: hidden;
        }

        .container {
            width: 90%;
            max-width: 1400px;
            margin: 0 auto;
            padding: 2rem 0;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }

        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 4rem;
            position: relative;
        }

        .logo {
            font-size: 2rem;
            font-weight: 700;
            color: var(--accent-color);
            text-decoration: none;
            letter-spacing: 2px;
            position: relative;
            overflow: hidden;
        }

        .logo::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, var(--accent-color), transparent);
            transform: translateX(-100%);
            animation: shimmer 3s infinite;
        }

        @keyframes shimmer {
            100% { transform: translateX(100%); }
        }

        .user-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: var(--card-bg);
            display: flex;
            justify-content: center;
            align-items: center;
            cursor: pointer;
            transition: var(--transition);
            position: relative;
            overflow: hidden;
        }

        .user-icon:hover {
            transform: scale(1.1);
        }

        .user-icon::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: conic-gradient(from 0deg, transparent, var(--accent-color), transparent 30%);
            animation: rotate 4s linear infinite;
        }

        .user-icon::after {
            content: '\f007';
            font-family: 'Font Awesome 5 Free';
            font-weight: 900;
            font-size: 1.2rem;
            color: var(--accent-color);
            position: relative;
            z-index: 1;
        }

        @keyframes rotate {
            100% { transform: rotate(360deg); }
        }

        main {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }

        h1 {
            font-size: 4rem;
            font-weight: 700;
            margin-bottom: 3rem;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        h1::before {
            content: attr(data-text);
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, var(--accent-color), #ff00ff, var(--accent-color));
            background-size: 200% 100%;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            animation: gradientFlow 5s linear infinite;
        }

        @keyframes gradientFlow {
            0% { background-position: 0% 50%; }
            100% { background-position: 200% 50%; }
        }

        .options-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 2rem;
            margin-top: 2rem;
        }

        .option-card {
            background-color: var(--card-bg);
            border-radius: 20px;
            overflow: hidden;
            box-shadow: var(--shadow);
            transition: var(--transition);
            position: relative;
            z-index: 1;
        }

        .option-card::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: conic-gradient(from 0deg, transparent, var(--accent-color), transparent 30%);
            animation: rotate 4s linear infinite;
            opacity: 0;
            transition: opacity 0.3s ease;
            z-index: -1;
        }

        .option-card:hover::before {
            opacity: 1;
        }

        .option-card a {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 3rem 2rem;
            text-decoration: none;
            color: var(--text-color);
            height: 100%;
            transition: var(--transition);
            position: relative;
            z-index: 2;
            background-color: var(--card-bg);
        }

        .option-card:hover a {
            transform: translateY(-5px);
        }

        .option-card i {
            font-size: 3.5rem;
            margin-bottom: 1.5rem;
            transition: var(--transition);
            color: var(--accent-color);
        }

        .option-card:hover i {
            transform: scale(1.2);
            filter: drop-shadow(0 0 10px var(--accent-color));
        }

        .option-card .btn-text {
            font-size: 1.2rem;
            font-weight: 500;
            text-align: center;
            transition: var(--transition);
        }

        .option-card:hover .btn-text {
            color: var(--accent-color);
        }

        .logout-card::before {
            background: conic-gradient(from 0deg, transparent, #ff4d4d, transparent 30%);
        }

        .logout-card i, .logout-card:hover .btn-text {
            color: #ff4d4d;
        }

        footer {
            text-align: center;
            padding: 2rem 0;
            font-size: 0.9rem;
            opacity: 0.7;
            position: relative;
            overflow: hidden;
        }

        footer::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 1px;
            background: linear-gradient(to right, transparent, var(--accent-color), transparent);
        }

        @media (max-width: 768px) {
            .options-grid {
                grid-template-columns: 1fr;
            }

            h1 {
                font-size: 3rem;
            }
        }

        /* Animaciones */
        @keyframes pulseGlow {
            0%, 100% { box-shadow: 0 0 5px var(--accent-color), 0 0 10px var(--accent-color); }
            50% { box-shadow: 0 0 20px var(--accent-color), 0 0 30px var(--accent-color); }
        }

        .option-card {
            animation: fadeInScale 0.6s ease-out forwards, pulseGlow 2s infinite;
            opacity: 0;
        }

        @keyframes fadeInScale {
            0% {
                opacity: 0;
                transform: scale(0.9);
            }
            100% {
                opacity: 1;
                transform: scale(1);
            }
        }

        .option-card:nth-child(1) { animation-delay: 0.1s; }
        .option-card:nth-child(2) { animation-delay: 0.2s; }
        .option-card:nth-child(3) { animation-delay: 0.3s; }
        .option-card:nth-child(4) { animation-delay: 0.4s; }
        .option-card:nth-child(5) { animation-delay: 0.5s; }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <a href="#" class="logo">TECDATEL</a>
            <div class="user-icon"></div>
        </header>

        <main>
            <h1 data-text="Nexus Control">Nexus Control</h1>

            <div class="options-grid">
                <div class="option-card">
                    <a href="delete_category.php">
                        <i class="fas fa-trash-alt"></i>
                        <span class="btn-text">Agregar o Elimina Categorías</span>
                    </a>
                </div>
                <div class="option-card">
                    <a href="add_record.php">
                        <i class="fas fa-plus-circle"></i>
                        <span class="btn-text">Añadir Registro</span>
                    </a>
                </div>
                <div class="option-card">
                    <a href="view_categories.php">
                        <i class="fas fa-list"></i>
                        <span class="btn-text">Ver Categorías</span>
                    </a>
                </div>
                <div class="option-card">
                    <a href="inventory.php">
                        <i class="fas fa-boxes"></i>
                        <span class="btn-text">Inventario</span>
                    </a>
                </div>
                <div class="option-card logout-card">
                    <a href="logout.php">
                        <i class="fas fa-sign-out-alt"></i>
                        <span class="btn-text">Cerrar Sesión</span>
                    </a>
                </div>
            </div>
        </main>

        <footer>
            <p>&copy; 2024 Tecdatel. Redefiniendo el futuro.</p>
        </footer>
    </div>
</body>
</html>