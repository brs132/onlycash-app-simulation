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

<!-- POPUP INICIAL -->
<div id="popup" class="fixed inset-0 bg-gray-700 bg-opacity-80 flex items-center justify-center p-6 z-50 hidden">
    <div class="bg-white rounded-xl max-w-xs w-full p-6 text-center drop-shadow-2xl border border-gray-300" role="dialog" aria-modal="true" aria-labelledby="popup-title" aria-describedby="popup-desc">
        <p id="popup-title" class="text-xl font-extrabold mb-3 leading-tight uppercase flex items-center justify-center gap-2">
            <span>🪙</span> ¡GANA EL DOBLE!
        </p>
        <p id="popup-desc" class="text-sm text-gray-900 mb-3 leading-relaxed">
            Lanzaremos una <strong class="text-blue-600 underline cursor-pointer">actualización de la aplicación</strong> en primicia para que ganes <strong class="text-blue-600 underline cursor-pointer">el doble de comisiones</strong> ¡ahora mismo!
        </p>
        <p class="text-sm text-gray-900 mb-6 leading-relaxed">
            ¡Haz clic en el botón de abajo y únete al grupo!
        </p>
        <button id="popup-btn" class="w-full bg-blue-500 hover:bg-blue-600 text-white font-extrabold text-sm rounded-md py-3 pulse flex items-center justify-center gap-2" type="button">
            <i class="fab fa-telegram-plane text-white"></i> ¡QUIERO GANAR EL DOBLE!
        </button>
    </div>
</div>

<!-- POPUP GRUPO -->
<div id="popup-group" class="fixed inset-0 bg-gray-700 bg-opacity-80 flex items-center justify-center p-6 z-50 hidden">
    <div class="bg-white rounded-xl max-w-xs w-full p-6 text-center drop-shadow-2xl border border-gray-300" role="dialog" aria-modal="true">
        <p class="text-xl font-extrabold mb-3 leading-tight uppercase flex items-center justify-center gap-2">
            <span>🪙</span> ¡GANA EL DOBLE!
        </p>
        <p class="text-sm text-gray-900 mb-3 leading-relaxed">
            Lanzaremos una <strong class="text-blue-600 underline cursor-pointer">actualización de la aplicación</strong> en primicia para que ganes <strong class="text-blue-600 underline cursor-pointer">el doble de comisiones</strong> ¡ahora mismo!
        </p>
        <p class="text-sm text-gray-900 mb-6 leading-relaxed">
            ¡Haz clic en el botón de abajo y únete al grupo!
        </p>
        <button id="popup-group-btn" class="w-full bg-blue-500 hover:bg-blue-600 text-white font-extrabold text-sm rounded-md py-3 pulse flex items-center justify-center gap-2" type="button">
            <i class="fab fa-telegram-plane text-white"></i> ¡QUIERO GANAR EL DOBLE!
        </button>
    </div>
</div>

<!-- SALDO MODAL -->
<div id="balance-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-6 z-50 hidden">
    <div class="bg-white rounded-lg max-w-md w-full p-6 text-center shadow-lg">
        <h2 class="text-xl font-bold mb-4">¡Estás muy cerca de retirar tus ganancias!</h2>
        <p id="balance-copy" class="mb-4 text-gray-700">
            Para procesar tu retiro, se aplica una pequeña <strong class="text-green-600">tarifa administrativa de $19.90</strong>. Esta tarifa cubre costos de conversión de moneda, procesamiento seguro y mantenimiento de la plataforma para garantizar que tus pagos sean rápidos y confiables.
        </p>
        <p class="mb-6 text-gray-700">
            ¡No te preocupes! Esta es una práctica común para proteger a nuestros usuarios y asegurar la calidad del servicio.
        </p>
        <p class="mb-6 text-lg font-bold text-green-700" id="net-amount-text"></p>
        <a href="https://pay.hotmart.com/D85712726O?off=o731tbha&checkoutMode=10" id="pay-fee-btn" class="block bg-green-600 hover:bg-green-700 text-white font-bold rounded-md px-6 py-2 transition mb-2 w-full pulse text-center no-underline" target="_blank" rel="noopener noreferrer">Retirar ahora</a>
        <button id="close-balance-modal" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold rounded-md px-6 py-2 transition w-full" type="button">Cerrar</button>
    </div>
</div>

<!-- PAGAMENTO SIMULADO MODAL -->
<div id="payment-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-6 z-50 hidden">
    <div class="bg-white rounded-lg max-w-md w-full p-6 text-center shadow-lg">
        <h2 class="text-xl font-bold mb-4">Procesando pago...</h2>
        <p class="mb-6 text-gray-700">Por favor, espera mientras validamos tu pago para liberar tus ganancias.</p>
    </div>
</div>

<!-- HEADER -->
<header class="bg-white shadow-md sticky top-0 z-40">
    <div class="max-w-md mx-auto flex justify-between items-center px-4 py-3">
        <div class="flex items-center space-x-2 select-none">
            <img src="https://storage.googleapis.com/a1aa/image/d32e626a-ea49-4a1a-8523-1a6bab98abf9.jpg" alt="OnlyCash logo" class="w-8 h-8 rounded" />
            <span class="font-bold text-lg text-gray-800">OnlyCash</span>
        </div>
        <div class="flex items-center space-x-1 select-none cursor-pointer" id="balance-display">
            <i class="fas fa-dollar-sign text-green-600"></i>
            <span id="balance" class="font-semibold text-green-700 text-lg"></span>
        </div>
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
    <button id="withdraw-btn" class="mt-4 w-full max-w-md bg-blue-600 hover:bg-blue-700 text-white font-extrabold rounded-md py-3 shadow-lg transition" type="button">
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
        // ... (rest of the questions array)
    ];

    let currentIndex = 0;
    let currentQuestionIndex = 0;
    let balance = 0;
    let doubleEarnings = false;

    const popup = document.getElementById("popup");
    const popupBtn = document.getElementById("popup-btn");
    const popupGroup = document.getElementById("popup-group");
    const popupGroupBtn = document.getElementById("popup-group-btn");
    const profileImage = document.getElementById("profile-image");
    const balanceEl = document.getElementById("balance");
    const balanceCopyEl = document.getElementById("balance-copy");
    const netAmountText = document.getElementById("net-amount-text");
    const statusMsg = document.createElement("div");
    statusMsg.id = "status-msg";
    statusMsg.className = "fixed bottom-20 left-1/2 -translate-x-1/2 bg-blue-600 text-white px-6 py-3 rounded-full shadow-lg text-sm font-semibold opacity-0 pointer-events-none transition-opacity z-50";
    document.body.appendChild(statusMsg);

    const withdrawBtn = document.getElementById("withdraw-btn");
    const withdrawModal = document.getElementById("balance-modal");
    const closeBalanceModalBtn = document.getElementById("close-balance-modal");
    const payFeeBtn = document.getElementById("pay-fee-btn");
    const paymentModal = document.getElementById("payment-modal");

    const footerBalanceBtn = document.getElementById("footer-balance");
    const footerHomeBtn = document.getElementById("footer-home");
    const footerGroupBtn = document.getElementById("footer-group");

    const questionText = document.getElementById("question-text");

    // Atualiza a imagem atual
    function updateImage() {
        profileImage.src = images[currentIndex];
    }

    // Atualiza a pergunta atual
    function updateQuestion() {
        questionText.textContent = questions[currentIndex][currentQuestionIndex];
    }

    // Atualiza o saldo exibido
    function updateBalance() {
        balanceEl.textContent = balance.toFixed(2);
        localStorage.setItem('onlycash_balance', balance.toFixed(2));
    }

    // Atualiza a copy do modal de saldo
    function updateBalanceCopy() {
        const totalFormatted = balance.toFixed(2);
        balanceCopyEl.innerHTML = `Para procesar tu retiro, se aplica una pequeña <strong class="text-green-600">tarifa administrativa de $19.90</strong>. Esta tarifa cubre costos de conversión de moneda, procesamiento seguro y mantenimiento de la plataforma para garantizar que tus pagos sean rápidos y confiables.<br><br><strong>Recibirás un total de <span class="text-xl">${totalFormatted} USD</span> en tu cuenta.</strong>`;
        netAmountText.textContent = "";
    }

    // Mostra mensagem temporária
    function showStatusMessage(msg, duration = 2500) {
        statusMsg.textContent = msg;
        statusMsg.classList.remove("opacity-0", "pointer-events-none");
        setTimeout(() => {
            statusMsg.classList.add("opacity-0", "pointer-events-none");
        }, duration);
    }

    // Gera valor aleatório decimal entre min e max
    function randomEarning() {
        const val = (Math.random() * (17.9 - 2) + 2);
        return Math.round(val * 100) / 100;
    }

    // Event Listeners
    popupBtn.addEventListener("click", () => {
        doubleEarnings = true;
        showStatusMessage("¡Ganancias dobladas activadas!");
        popup.classList.add("hidden");
        updateBalance();
    });

    popup.addEventListener("click", (e) => {
        if (e.target === popup) popup.classList.add("hidden");
    });

    popupGroup.addEventListener("click", (e) => {
        if (e.target === popupGroup) popupGroup.classList.add("hidden");
    });

    popupGroupBtn.addEventListener("click", () => {
        popupGroup.classList.add("hidden");
        showStatusMessage("¡Gracias por unirte al grupo!");
    });

    function addEarnings() {
        let earn = randomEarning();
        if (doubleEarnings) earn *= 2;
        earn = Math.round(earn * 100) / 100;
        balance += earn;
        updateBalance();
        showStatusMessage(`Ganaste $${earn.toFixed(2)}!`);

        currentQuestionIndex++;
        if (currentQuestionIndex >= questions[currentIndex].length) {
            currentQuestionIndex = 0;
            currentIndex++;
            if (currentIndex >= images.length) currentIndex = 0;
            updateImage();
        }
        updateQuestion();
    }

    document.getElementById("btn-yes").addEventListener("click", addEarnings);
    document.getElementById("btn-somewhat").addEventListener("click", addEarnings);
    document.getElementById("btn-no").addEventListener("click", addEarnings);

    withdrawBtn.addEventListener("click", () => {
        if (balance < 50) {
            showStatusMessage("Saldo insuficiente para retirar. Necesitas al menos $50.00.");
            return;
        }
        updateBalanceCopy();
        withdrawModal.classList.remove("hidden");
    });

    closeBalanceModalBtn.addEventListener("click", () => {
        withdrawModal.classList.add("hidden");
    });

    payFeeBtn.addEventListener("click", () => {
        withdrawModal.classList.add("hidden");
        paymentModal.classList.remove("hidden");
        setTimeout(() => {
            paymentModal.classList.add("hidden");
            showStatusMessage("Pago aprobado. ¡Tus ganancias han sido liberadas!");
            balance = 0;
            updateBalance();
        }, 5000);
    });

    footerGroupBtn.addEventListener("click", () => {
        popupGroup.classList.remove("hidden");
    });

    footerHomeBtn.addEventListener("click", () => {
        currentIndex = 0;
        currentQuestionIndex = 0;
        updateImage();
        updateQuestion();
        showStatusMessage("Volviendo a la primera imagen y pregunta");
    });

    footerBalanceBtn.addEventListener("click", () => {
        if (balance < 50) {
            showStatusMessage("Saldo insuficiente para retirar. Necesitas al menos $50.00.");
            return;
        }
        updateBalanceCopy();
        withdrawModal.classList.remove("hidden");
    });

    // Initialize
    const savedBalance = localStorage.getItem('onlycash_balance');
    if (savedBalance) {
        balance = parseFloat(savedBalance);
    }
    updateImage();
    updateQuestion();
    updateBalance();

    // Show initial popup on load
    window.addEventListener("load", () => {
        popup.classList.remove("hidden");
    });
})();
</script>
</body>
</html>
