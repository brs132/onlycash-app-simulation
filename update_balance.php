<?php
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['balance']) || !isset($input['evaluatedImages'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing parameters']);
    exit;
}

$userId = $_SESSION['user_id'];
$balance = floatval($input['balance']);
$evaluatedImages = $input['evaluatedImages'];

$jsonFile = 'db.json';
if (!file_exists($jsonFile)) {
    http_response_code(500);
    echo json_encode(['error' => 'Database file not found']);
    exit;
}

$jsonContent = file_get_contents($jsonFile);
$data = json_decode($jsonContent, true);

if (!isset($data['users'])) {
    http_response_code(500);
    echo json_encode(['error' => 'Invalid database format']);
    exit;
}

$found = false;
foreach ($data['users'] as &$user) {
    if ($user['id'] === $userId) {
        $user['balance'] = $balance;
        $user['evaluatedImages'] = $evaluatedImages;
        $found = true;
        break;
    }
}

if (!$found) {
    http_response_code(404);
    echo json_encode(['error' => 'User not found']);
    exit;
}

if (file_put_contents($jsonFile, json_encode($data, JSON_PRETTY_PRINT)) === false) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to save database']);
    exit;
}

echo json_encode(['success' => true]);
?>
