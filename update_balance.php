<?php
file_put_contents('/tmp/debug.log', date('Y-m-d H:i:s') . " - Request received: " . file_get_contents('php://input') . "\n", FILE_APPEND);

session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
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

// Function to save users to JSON file
function saveUsers($users) {
    $jsonFile = 'db.json';
    $data = ['users' => $users];
    return file_put_contents($jsonFile, json_encode($data, JSON_PRETTY_PRINT));
}

// Function to generate random daily earning between $0.41 and $1.19
function getDailyEarning() {
    return round(rand(41, 119) / 100, 2);
}

// Get POST data
$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['action']) || $input['action'] !== 'evaluate') {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid action']);
    exit;
}

$userId = $_SESSION['user_id'];
$users = getUsers();
$userFound = false;
$response = [];

foreach ($users as &$user) {
    if ($user['id'] === $userId) {
        $userFound = true;
        
        // Initialize daily_progress if not exists
        if (!isset($user['daily_progress'])) {
            $user['daily_progress'] = [
                'last_evaluation_date' => '',
                'evaluations_today' => 0,
                'consecutive_days' => 0,
                'can_withdraw' => false
            ];
        }
        
        // Initialize balance if not exists
        if (!isset($user['balance'])) {
            $user['balance'] = 0;
        }

        $today = date('Y-m-d');
        $lastDate = $user['daily_progress']['last_evaluation_date'];

        // Check if this is a new day
        if ($lastDate !== $today) {
            // If user missed a day (except first time), reset consecutive days
            if ($lastDate !== '' && date('Y-m-d', strtotime($lastDate . ' +1 day')) !== $today) {
                $user['daily_progress']['consecutive_days'] = 0;
            }
            $user['daily_progress']['evaluations_today'] = 0;
            $user['daily_progress']['last_evaluation_date'] = $today;
        }

        // Update evaluations count for today
        $user['daily_progress']['evaluations_today']++;

        // If completed 5 evaluations for the day
        if ($user['daily_progress']['evaluations_today'] === 5) {
            // Add daily earning
            $dailyEarning = getDailyEarning();
            $user['balance'] += $dailyEarning;
            
            // Increment consecutive days
            if ($user['daily_progress']['evaluations_today'] === 5) {
                $user['daily_progress']['consecutive_days']++;
            }

            // Check if reached 15 consecutive days
            if ($user['daily_progress']['consecutive_days'] >= 15) {
                $user['daily_progress']['can_withdraw'] = true;
            }
        }

        // Prepare response
        $response = [
            'success' => true,
            'balance' => $user['balance'],
            'daily_progress' => [
                'evaluations_today' => $user['daily_progress']['evaluations_today'],
                'consecutive_days' => $user['daily_progress']['consecutive_days'],
                'can_withdraw' => $user['daily_progress']['can_withdraw']
            ],
            'message' => ''
        ];

        // Add appropriate message
        if ($user['daily_progress']['can_withdraw']) {
            $response['message'] = '¡Felicitaciones! Has completado tu misión de 15 días y puedes retirar tu saldo.';
        } else {
            $daysLeft = 15 - $user['daily_progress']['consecutive_days'];
            $response['message'] = "¡Faltan solo {$daysLeft} días para liberar tu retiro! (Día {$user['daily_progress']['consecutive_days']} de 15)";
        }

        break;
    }
}

if (!$userFound) {
    http_response_code(404);
    echo json_encode(['error' => 'User not found']);
    exit;
}

// Save updated user data
if (saveUsers($users)) {
    echo json_encode($response);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to save user data']);
}
?>
