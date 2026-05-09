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
$user_id = $_SESSION['user_id'];

if ($race_id <= 0) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid race ID']);
    exit;
}

$db = getDatabaseConnection();

// Check if already favorited
$stmt = $db->prepare('SELECT id FROM favorites WHERE user_id = ? AND race_id = ?');
$stmt->execute([$user_id, $race_id]);
$exists = $stmt->fetch();

if ($exists) {
    // Remove favorite
    $stmt = $db->prepare('DELETE FROM favorites WHERE user_id = ? AND race_id = ?');
    $stmt->execute([$user_id, $race_id]);

    // Update planner association: keep row but clear is_favorite
    $stmt = $db->prepare('
        UPDATE user_races
           SET is_favorite = 0
         WHERE user_id = ? AND race_id = ?
    ');
    $stmt->execute([$user_id, $race_id]);

    echo json_encode(['status' => 'removed']);
} else {
    // Add favorite
    $stmt = $db->prepare('INSERT INTO favorites (user_id, race_id) VALUES (?, ?)');
    $stmt->execute([$user_id, $race_id]);

    // Ensure planner association exists and mark as favorite
    $stmt = $db->prepare('
        INSERT INTO user_races (user_id, race_id, is_favorite)
        VALUES (?, ?, 1)
        ON DUPLICATE KEY UPDATE is_favorite = VALUES(is_favorite)
    ');
    $stmt->execute([$user_id, $race_id]);

    echo json_encode(['status' => 'added']);
}
