<?php
require_once __DIR__ . '/db.php';

function startSession() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

function isLoggedIn() {
    startSession();
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

function getCurrentUser() {
    startSession();
    if (!isLoggedIn()) {
        return null;
    }

    try {
        $db = getDatabaseConnection();
        $stmt = $db->prepare('SELECT id, username, email, role FROM users WHERE id = ?');
        $stmt->execute([$_SESSION['user_id']]);
        return $stmt->fetch();
    } catch (Exception $e) {
        return null;
    }
}

function loginUser($email, $password) {
    startSession();

    try {
        $db = getDatabaseConnection();
        $stmt = $db->prepare('SELECT id, password_hash FROM users WHERE email = ?');
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password_hash'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['email'] = $email;
            return true;
        }
    } catch (Exception $e) {
        return false;
    }

    return false;
}

function registerUser($username, $email, $password) {
    try {
        $db = getDatabaseConnection();

        // Check for existing email or username
        $stmt = $db->prepare('SELECT id FROM users WHERE email = ? OR username = ?');
        $stmt->execute([$email, $username]);
        if ($stmt->fetch()) {
            return ['error' => 'Email or username already registered.'];
        }

        $passwordHash = password_hash($password, PASSWORD_BCRYPT);

        $stmt = $db->prepare('INSERT INTO users (username, email, password_hash) VALUES (?, ?, ?)');
        $stmt->execute([$username, $email, $passwordHash]);

        return ['success' => true, 'user_id' => $db->lastInsertId()];
    } catch (Exception $e) {
        return ['error' => 'Registration failed: ' . $e->getMessage()];
    }
}

function requireLogin() {
    startSession();
    if (!isLoggedIn()) {
        header('Location: /login.php');
        exit;
    }
}

function requireAdmin() {
    startSession();
    if (!isLoggedIn()) {
        header('Location: /login.php');
        exit;
    }

    $user = getCurrentUser();
    if (!$user || $user['role'] !== 'admin') {
        header('Location: /index.php');
        exit;
    }
}

function logoutUser() {
    startSession();
    session_destroy();
    header('Location: /index.php');
    exit;
}
?>
