<?php
header('Content-Type: application/json');

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';

startSession();

if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['error' => 'Not logged in']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$race_id = intval($input['race_id'] ?? 0);
$note_text = trim($input['note_text'] ?? '');
$user_id = $_SESSION['user_id'];

if ($race_id <= 0) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid race ID']);
    exit;
}

$db = getDB();

// Check if note exists
$stmt = $db->prepare('SELECT id FROM notes WHERE user_id = ? AND race_id = ?');
$stmt->execute([$user_id, $race_id]);
$exists = $stmt->fetch();

if ($exists) {
    // Update note
    $stmt = $db->prepare('UPDATE notes SET note_text = ? WHERE user_id = ? AND race_id = ?');
    $stmt->execute([$note_text, $user_id, $race_id]);
} else {
    // Insert note
    $stmt = $db->prepare('INSERT INTO notes (user_id, race_id, note_text) VALUES (?, ?, ?)');
    $stmt->execute([$user_id, $race_id, $note_text]);
}

echo json_encode(['success' => true]);
?>
