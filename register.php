<?php
session_start();

// Function to read users from JSON file
function getUsers() {
    $jsonFile = 'db.json';
    if (file_exists($jsonFile)) {
        $jsonContent = file_get_contents($jsonFile);
        return json_decode($jsonContent, true)['users'] ?? [];
    }
    return ['users' => []];
}

// Function to save users to JSON file
function saveUsers($users) {
    $jsonFile = 'db.json';
    $data = ['users' => $users];
    file_put_contents($jsonFile, json_encode($data, JSON_PRETTY_PRINT));
}

// Handle registration form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $error = '';
    $success = '';

    if (!$email || !$password || !$confirm_password) {
        $error = "Por favor, complete todos los campos.";
    } elseif ($password !== $confirm_password) {
        $error = "Las contraseñas no coinciden.";
    } else {
        $users = getUsers();
        
        // Check if email already exists
        $emailExists = false;
        foreach ($users as $user) {
            if ($user['email'] === $email) {
                $emailExists = true;
                break;
            }
        }

        if ($emailExists) {
            $error = "Este correo electrónico ya está registrado.";
        } else {
            // Create new user
            $newUser = [
                'id' => uniqid(),
                'email' => $email,
                'password' => password_hash($password, PASSWORD_DEFAULT),
                'created_at' => date('Y-m-d H:i:s')
            ];

            $users[] = $newUser;
            saveUsers($users);
            $success = "¡Registro exitoso! Ahora puedes iniciar sesión.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OnlyCash - Registro</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <h1>OnlyCash - Registro</h1>
            <?php if (isset($error) && !empty($error)): ?>
                <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            <?php if (isset($success) && !empty($success)): ?>
                <div class="success-message"><?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>
            <form method="POST" class="login-form">
                <div class="form-group">
                    <input type="email" name="email" placeholder="Correo electrónico" required 
                           value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                </div>
                <div class="form-group">
                    <input type="password" name="password" placeholder="Contraseña" required>
                </div>
                <div class="form-group">
                    <input type="password" name="confirm_password" placeholder="Confirmar contraseña" required>
                </div>
                <button type="submit" class="btn-primary">Registrarse</button>
            </form>
            <p class="register-link">
                ¿Ya tienes una cuenta? <a href="login.php">Iniciar sesión</a>
            </p>
        </div>
    </div>
</body>
</html>
