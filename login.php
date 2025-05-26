<?php
session_start();

// Function to read users from JSON file
function getUsers() {
    $jsonFile = 'db.json';
    if (file_exists($jsonFile)) {
        $jsonContent = file_get_contents($jsonFile);
        return json_decode($jsonContent, true)['users'] ?? [];
    }
    return [];
}

// Handle login form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'] ?? '';
    $error = '';

    if (!$email || !$password) {
        $error = "Por favor, complete todos los campos.";
    } else {
        $users = getUsers();
        $authenticated = false;

        foreach ($users as $user) {
            if ($user['email'] === $email && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['email'] = $user['email'];
                $authenticated = true;
                header("Location: app.php");
                exit;
            }
        }

        if (!$authenticated) {
            $error = "Correo electrónico o contraseña incorrectos.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OnlyCash - Login</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <h1>OnlyCash - Login</h1>
            <?php if (isset($error) && !empty($error)): ?>
                <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            <form method="POST" class="login-form">
                <div class="form-group">
                    <input type="email" name="email" placeholder="Correo electrónico" required 
                           value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                </div>
                <div class="form-group">
                    <input type="password" name="password" placeholder="Contraseña" required>
                </div>
                <button type="submit" class="btn-primary">Entrar</button>
            </form>
            <p class="register-link">
                ¿No tienes una cuenta? <a href="register.php">Regístrate aquí</a>
            </p>
        </div>
    </div>
</body>
</html>
