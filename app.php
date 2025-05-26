<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Function to read users from JSON file
function getUsers() {
    $jsonFile = 'db.json';
    if (file_exists($jsonFile)) {
        $jsonContent = file_get_contents($jsonFile);
        return json_decode($jsonContent, true)['users'] ?? [];
    }
    return [];
}

// Get user data
$users = getUsers();
$userData = null;
foreach ($users as $user) {
    if ($user['id'] === $_SESSION['user_id']) {
        $userData = $user;
        break;
    }
}

if (!$userData) {
    header("Location: logout.php");
    exit;
}

// Initialize daily_progress if not set
$dailyProgress = $userData['daily_progress'] ?? [
    'evaluations_today' => 0,
    'consecutive_days' => 0,
    'can_withdraw' => false
];

$balance = $userData['balance'] ?? 0;
?>
<!DOCTYPE html>
<html lang="es" class="scroll-smooth">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>OnlyCash - Simulación de Evaluador</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet" />
    <style>
        @keyframes pulse-button {
            0%, 100% {
                transform: scale(1);
                box-shadow: 0 0 8px rgba(59, 130, 246, 0.7);
            }
            50% {
                transform: scale(1.05);
                box-shadow: 0 0 20px rgba(59, 130, 246, 1);
            }
        }
        .pulse {
            animation: pulse-button 2s infinite;
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen flex flex-col">

<!-- HEADER -->
<header class="bg-white shadow-md sticky top-0 z-40">
    <div class="max-w-md mx-auto flex justify-between items-center px-4 py-3">
        <div class="flex items-center space-x-2 select-none">
            <img src="https://storage.googleapis.com/a1aa/image/d32e626a-ea49-4a1a-8523-1a6bab98abf9.jpg" alt="OnlyCash logo" class="w-8 h-8 rounded" />
            <span class="font-bold text-lg text-gray-800">OnlyCash</span>
        </div>
        <div class="flex items-center space-x-4">
            <!-- Daily Progress -->
            <div class="text-sm text-gray-600">
                <span id="evaluations-today"><?php echo $dailyProgress['evaluations_today']; ?></span>/5 hoy
            </div>
            <!-- Balance Display -->
            <div class="flex items-center space-x-1 select-none cursor-pointer" id="balance-display">
                <i class="fas fa-dollar-sign text-green-600"></i>
                <span id="balance" class="font-semibold text-green-700 text-lg"><?php echo number_format($balance, 2); ?></span>
            </div>
        </div>
    </div>
    <!-- Progress Message -->
    <div class="bg-blue-50 text-center py-2 text-sm text-blue-700" id="progress-message">
        <?php
        if ($dailyProgress['can_withdraw']) {
            echo '¡Felicitaciones! Has completado tu misión de 15 días y puedes retirar tu saldo.';
        } else {
            $daysLeft = 15 - $dailyProgress['consecutive_days'];
            echo "¡Faltan solo {$daysLeft} días para liberar tu retiro! (Día {$dailyProgress['consecutive_days']} de 15)";
        }
        ?>
    </div>
</header>

<!-- MAIN CONTENT -->
<main class="flex-grow max-w-md mx-auto w-full p-4 flex flex-col items-center space-y-6">
    <!-- Carrossel de imagens -->
    <div class="relative w-full rounded-lg overflow-hidden shadow-lg bg-white select-none">
        <img id="profile-image" src="https://i.postimg.cc/QMQ3ZBrj/Captura-de-Tela-2025-05-20-a-s-20-56-33.png" alt="Imagen para evaluar" class="w-full h-auto" loading="lazy" />
        <div class="absolute top-2 left-3 text-xs font-semibold text-gray-700 bg-white bg-opacity-70 rounded px-2 select-none">
            Patrocinado
        </div>
    </div>

    <!-- Pergunta -->
    <p id="question-text" class="text-center text-gray-900 font-semibold text-lg px-2 min-h-[3rem]">
        ¿Crees que ese cabello colorido te haría perder la cabeza?
    </p>

    <!-- Botões de resposta -->
    <div class="flex justify-center gap-4 w-full max-w-md">
        <button id="btn-yes" class="flex items-center gap-2 bg-green-500 hover:bg-green-600 text-white font-semibold rounded-full px-6 py-2 shadow-md transition" type="button">
            <span>💚</span> Sí
        </button>
        <button id="btn-somewhat" class="flex items-center gap-2 bg-yellow-400 hover:bg-yellow-500 text-white font-semibold rounded-full px-6 py-2 shadow-md transition" type="button">
            <span>👍</span> Un poco
        </button>
        <button id="btn-no" class="flex items-center gap-2 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-full px-6 py-2 shadow-md transition" type="button">
            <span>🚫</span> No
        </button>
    </div>

    <!-- Botão saque -->
    <button id="withdraw-btn" class="mt-4 w-full max-w-md bg-blue-600 hover:bg-blue-700 text-white font-extrabold rounded-md py-3 shadow-lg transition <?php echo !$dailyProgress['can_withdraw'] ? 'opacity-50 cursor-not-allowed' : ''; ?>" type="button" <?php echo !$dailyProgress['can_withdraw'] ? 'disabled' : ''; ?>>
        Retirar Ganancias
    </button>
</main>

<!-- FOOTER -->
<footer class="bg-white shadow-inner fixed bottom-0 left-0 right-0 max-w-md mx-auto w-full flex justify-around items-center py-3 border-t border-gray-300 z-40">
    <button id="footer-balance" class="flex flex-col items-center text-gray-700 text-xs select-none" type="button">
        <i class="fas fa-dollar-sign fa-lg mb-1"></i>
        Saldo
    </button>
    <button id="footer-home" class="flex flex-col items-center text-blue-600 text-xs select-none" type="button">
        <i class="fas fa-home fa-lg mb-1"></i>
        Home
    </button>
    <button id="footer-group" class="flex flex-col items-center text-gray-700 text-xs select-none" type="button">
        <i class="fab fa-telegram-plane fa-lg mb-1"></i>
        Grupo
    </button>
</footer>

<script>
(() => {
    // Imagens para avaliação
    const images = [
        "https://i.postimg.cc/QMQ3ZBrj/Captura-de-Tela-2025-05-20-a-s-20-56-33.png",
        "https://i.postimg.cc/KzmyGZZB/Captura-de-Tela-2025-05-20-a-s-20-57-56.png",
        "https://i.postimg.cc/bJLfdKJM/Captura-de-Tela-2025-05-20-a-s-20-58-21.png",
        "https://i.postimg.cc/wjJKDtMD/Captura-de-Tela-2025-05-20-a-s-20-58-41.png",
        "https://i.postimg.cc/c4p21shC/Captura-de-Tela-2025-05-20-a-s-21-00-41.png",
        "https://i.postimg.cc/MKMg8cyg/Captura-de-Tela-2025-05-20-a-s-21-00-27.png",
        "https://i.postimg.cc/44FjMvhz/Captura-de-Tela-2025-05-20-a-s-21-00-17.png",
        "https://i.postimg.cc/GpHXvVqV/Captura-de-Tela-2025-05-20-a-s-21-00-54.png",
        "https://i.postimg.cc/vmk3hhKB/Captura-de-Tela-2025-05-20-a-s-21-01-02.png",
        "https://i.postimg.cc/kGrYJsNZ/Captura-de-Tela-2025-05-20-a-s-21-01-16.png",
        "https://i.postimg.cc/HLxhBVsS/Captura-de-Tela-2025-05-20-a-s-21-01-25.png",
        "https://i.postimg.cc/mD16WJ9C/Captura-de-Tela-2025-05-20-a-s-21-01-34.png",
        "https://i.postimg.cc/cCQ9r3XJ/Captura-de-Tela-2025-05-20-a-s-21-01-46.png",
        "https://i.postimg.cc/13yTVWx1/Captura-de-Tela-2025-05-20-a-s-21-01-58.png",
    ];

    // Perguntas para cada imagem
    const questions = [
        [
            "¿Crees que ese cabello colorido te haría perder la cabeza?",
            "¿Te gustaría acariciar ese cabello tan vibrante?",
            "¿Imagina qué tan suave será su piel bajo esa luz?",
            "¿Esa sonrisa te invita a un juego travieso?",
            "¿Le darías un beso apasionado en esa foto?",
        ],
        // ... rest of questions array
    ];

    let currentIndex = 0;
    let currentQuestionIndex = 0;
    const evaluationsToday = <?php echo $dailyProgress['evaluations_today']; ?>;
    const canWithdraw = <?php echo $dailyProgress['can_withdraw'] ? 'true' : 'false'; ?>;
    let balance = <?php echo $balance; ?>;

    const profileImage = document.getElementById("profile-image");
    const balanceEl = document.getElementById("balance");
    const evaluationsTodayEl = document.getElementById("evaluations-today");
    const questionText = document.getElementById("question-text");
    const withdrawBtn = document.getElementById("withdraw-btn");
    const progressMessage = document.getElementById("progress-message");

    // Atualiza a imagem atual
    function updateImage() {
        profileImage.src = images[currentIndex];
    }

    // Atualiza a pergunta atual
    function updateQuestion() {
        questionText.textContent = questions[currentIndex][currentQuestionIndex];
    }

    // Atualiza o saldo exibido
    function updateBalance(newBalance) {
        balance = newBalance;
        balanceEl.textContent = balance.toFixed(2);
    }

    // Handle evaluation
    function handleEvaluation() {
        console.log('Sending evaluation request...');
        // Send evaluation to server
        fetch('update_balance.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ action: 'evaluate' })
        })
        .then(response => {
            console.log('Response status:', response.status);
            return response.text();
        })
        .then(text => {
            console.log('Response text:', text);
            try {
                const data = JSON.parse(text);
                if (data.success) {
                    // Update UI
                    updateBalance(data.balance);
                    evaluationsTodayEl.textContent = data.daily_progress.evaluations_today;
                    progressMessage.textContent = data.message;
                    
                    // Update withdraw button state
                    if (data.daily_progress.can_withdraw) {
                        withdrawBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                        withdrawBtn.disabled = false;
                    }
 
                    // Move to next question/image
                    currentQuestionIndex++;
                    if (currentQuestionIndex >= questions[currentIndex].length) {
                        currentQuestionIndex = 0;
                        currentIndex++;
                        if (currentIndex >= images.length) {
                            currentIndex = 0;
                        }
                        updateImage();
                    }
                    updateQuestion();
                } else {
                    console.error('Evaluation failed:', data.error);
                }
            } catch (e) {
                console.error('Failed to parse JSON:', e, text);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al evaluar. Por favor, intente nuevamente.');
        });
    }

    // Event Listeners for evaluation buttons
    document.getElementById("btn-yes").addEventListener("click", handleEvaluation);
    document.getElementById("btn-somewhat").addEventListener("click", handleEvaluation);
    document.getElementById("btn-no").addEventListener("click", handleEvaluation);

    // Initialize
    updateImage();
    updateQuestion();
window.addEventListener('error', function(event) {
  console.error('Global error caught:', event.error);
});
})();
</script>
</body>
</html>
