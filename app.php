<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Get user email from session
$userEmail = $_SESSION['email'] ?? '';
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OnlyCash - Simulación de Evaluador</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body class="app-body">
    <div class="app-container">
        <header class="app-header">
            <h1>OnlyCash</h1>
            <div class="user-info">
                <?php echo htmlspecialchars($userEmail); ?>
                <a href="logout.php" class="logout-btn">Cerrar sesión</a>
            </div>
        </header>

        <main class="app-content">
            <!-- Double Earnings Promo Card -->
            <div class="promo-card">
                <h2>🪙 ¡GANA EL DOBLE!</h2>
                <p>
                    Lanzaremos una <strong>actualización de la aplicación</strong> en primicia 
                    para que ganes <strong>el doble de comisiones</strong> ¡ahora mismo!
                </p>
                <p>¡Haz clic en el botón de abajo y únete al grupo!</p>
                <button class="btn-secondary">¡QUIERO GANAR EL DOBLE!</button>
            </div>

            <!-- Withdrawal Info Card -->
            <div class="promo-card">
                <h2>¡Estás muy cerca de retirar tus ganancias!</h2>
                <p>
                    Para procesar tu retiro, se aplica una pequeña <strong>tarifa administrativa 
                    de $19.90</strong>. Esta tarifa cubre costos de conversión de moneda, 
                    procesamiento seguro y mantenimiento de la plataforma para garantizar que 
                    tus pagos sean rápidos y confiables.
                </p>
                <p>
                    ¡No te preocupes! Esta es una práctica común para proteger a nuestros 
                    usuarios y asegurar la calidad del servicio.
                </p>
                <a href="https://pay.hotmart.com/D85712726O?off=o731tbha&checkoutMode=10" 
                   class="btn-secondary">Retirar ahora</a>
            </div>

            <!-- Sponsored Content -->
            <div class="promo-card">
                <p class="sponsored-tag">Patrocinado</p>
                <img src="https://i.postimg.cc/QMQ3ZBrj/Captura-de-Tela-2025-05-20-a-s-20-56-33.png" 
                     alt="Imagen para evaluar" class="sponsored-image">
                <h3>¿Crees que ese cabello colorido te haría perder la cabeza?</h3>
                <div class="reaction-buttons">
                    <button class="reaction-btn">💚 Sí</button>
                    <button class="reaction-btn">👍 Un poco</button>
                    <button class="reaction-btn">🚫 No</button>
                </div>
            </div>
        </main>

        <!-- Bottom Navigation -->
        <nav class="bottom-nav">
            <a href="#" class="nav-item active">Saldo</a>
            <a href="#" class="nav-item">Home</a>
            <a href="#" class="nav-item">Grupo</a>
        </nav>
    </div>

    <script>
        // Handle reaction buttons
        document.querySelectorAll('.reaction-btn').forEach(button => {
            button.addEventListener('click', function() {
                // Remove active class from all buttons
                document.querySelectorAll('.reaction-btn').forEach(btn => {
                    btn.classList.remove('active');
                });
                // Add active class to clicked button
                this.classList.add('active');
            });
        });

        // Handle promo button click
        document.querySelector('.btn-secondary').addEventListener('click', function() {
            alert('¡Gracias por tu interés! Pronto recibirás más información.');
        });
    </script>
</body>
</html>
